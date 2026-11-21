"""Validate the Savannah Crest deployment sources without loading WordPress."""
import json
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
THEME = ROOT / 'wp-content/themes/savannah-crest'
PLUGIN = ROOT / 'wp-content/plugins/scs-core'


def require(condition, message):
    if not condition:
        raise SystemExit(message)


def read_json(path):
    return json.loads(path.read_text(encoding='utf-8'))


def validate_blocks_and_markup():
    registered = {}
    for metadata_path in sorted((PLUGIN / 'blocks').glob('*/block.json')):
        block = read_json(metadata_path)
        expected = 'scs/' + metadata_path.parent.name
        require(block['name'] == expected, f'Unexpected registration for {metadata_path}')
        view = PLUGIN / 'views' / f'{metadata_path.parent.name}.php'
        require(view.is_file(), f'Missing view for {expected}')
        require(block['editorScript'] == 'scs-block-editor', f'Missing editor script for {expected}')
        registered[expected] = block

    parts = {path.stem for path in (THEME / 'parts').glob('*.html')}
    templates = {path.stem for path in (THEME / 'templates').glob('*.html')}
    patterns = {
        match.group(1)
        for path in (THEME / 'patterns').glob('*.php')
        if (match := re.search(r'^\s*\*\s*Slug:\s*(\S+)', path.read_text(encoding='utf-8'), re.M))
    }
    required = {
        'index', 'front-page', 'archive-scs_safari', 'single-scs_safari', 'taxonomy-scs_destination',
        'taxonomy-scs_experience', 'single-scs_stay', 'home', 'single', 'page-plan-your-safari', '404',
    }
    require(required <= templates, f'Missing templates: {required - templates}')
    require((THEME / 'style.css').is_file(), 'Missing theme stylesheet')
    sources = [*(THEME / 'templates').glob('*.html'), *(THEME / 'parts').glob('*.html'), *(THEME / 'patterns').glob('*.php')]
    used = set()
    for source in sources:
        stack = []
        content = source.read_text(encoding='utf-8')
        for match in re.finditer(r'<!--\s*(/?)wp:([\w/-]+)\s*(.*?)\s*(/?)-->', content, re.S):
            close, name, payload, single = match.groups()
            settings = json.loads(payload) if payload else {}
            if close:
                require(stack and stack.pop() == name, f'Unexpected closing block {name} in {source.name}')
            elif not single:
                stack.append(name)
            if name.startswith('scs/'):
                require(name in registered, f'Unknown dynamic block {name} in {source.name}')
                require(single, f'Dynamic block {name} should be self-closing in {source.name}')
                used.add(name)
            if name == 'template-part':
                require(settings.get('slug') in parts, f'Unknown template part in {source.name}')
            if name == 'pattern':
                require(settings.get('slug') in patterns, f'Unknown pattern in {source.name}')
        require(not stack, f'Unclosed blocks in {source.name}: {stack}')
    require(used == set(registered), f'Unreferenced dynamic blocks: {set(registered) - used}')
    print(f'Validated {len(templates)} templates, {len(parts)} parts and {len(registered)} dynamic blocks.')


def validate_field_groups():
    keys = set()

    def check_fields(fields):
        seen_names = set()
        for field in fields:
            key = field['key']
            name = field['name']
            require(key.startswith('field_scs_') and key not in keys, f'Duplicate or noncanonical field key {key}')
            require(name not in seen_names, f'Duplicate field name {name}')
            keys.add(key)
            seen_names.add(name)
            if field['type'] == 'repeater':
                check_fields(field['sub_fields'])

    groups = list((PLUGIN / 'scf-json').glob('group_*.json'))
    require(len(groups) == 7, 'Expected all seven versioned field groups')
    for source in sorted(groups):
        group = read_json(source)
        require(source.stem == group['key'], f'Group key and filename differ: {source}')
        require(group['location'], f'Missing group location: {source}')
        check_fields(group['fields'])
    safari = read_json(PLUGIN / 'scf-json/group_scs_safari.json')
    source = (PLUGIN / 'src/safari.php').read_text(encoding='utf-8')
    declared = {field['name'] for field in safari['fields']}
    canonical = set(re.findall(r"'(scs_[a-z_]+)'", source.split('function bullet_labels', 1)[0]))
    require(canonical <= declared, f'PHP references unknown safari fields: {canonical - declared}')
    print(f'Validated {len(groups)} SCF groups with {len(keys)} unique field keys.')


def validate_assets():
    settings = read_json(THEME / 'theme.json')['settings']
    for family in settings['typography']['fontFamilies']:
        for face in family['fontFace']:
            for uri in face['src']:
                file = THEME / uri.removeprefix('file:./')
                require(file.is_file() and file.read_bytes()[:4] == b'wOF2', f'Invalid WOFF2 file: {file}')
                require((file.parent / 'OFL.txt').is_file() or file.with_name(file.stem + '-OFL.txt').is_file(), f'Missing font license for {file}')
    brand = THEME / 'assets/brand'
    for asset in brand.iterdir():
        source = ROOT / 'assets/brand' / ('web' if asset.name.startswith(('favicon', 'apple-touch')) else 'svg') / asset.name
        require(source.is_file(), f'Missing brand master: {source}')
        if asset.suffix == '.svg':
            matches = asset.read_text(encoding='utf-8') == source.read_text(encoding='utf-8')
        else:
            matches = asset.read_bytes() == source.read_bytes()
        require(matches, f'Unapproved asset copy: {asset}')
    require(not (ROOT / 'wp-config.php').exists() and not (ROOT / 'wp-content/uploads').exists(), 'WordPress runtime data in the repository')
    print(f'Validated {len(list(brand.iterdir()))} brand assets and the configured fonts and licenses.')


if __name__ == '__main__':
    validate_blocks_and_markup()
    validate_field_groups()
    validate_assets()
