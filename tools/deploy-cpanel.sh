#!/bin/bash
set -euo pipefail

fail() {
    printf 'Savannah Crest deployment: %s\n' "$*" >&2
    exit 1
}

if [[ $# -lt 1 || $# -gt 2 || ( $# -eq 2 && "$2" != --check ) ]]; then
    fail "Usage: bash tools/deploy-cpanel.sh /path/to/deploy-root-file [--check]"
fi

config=$1
[[ -f "$config" && -r "$config" ]] || fail "Create $config with the absolute path to the hosted WordPress directory."
mapfile -t lines < "$config"
[[ ${#lines[@]} -eq 1 ]] || fail "Enter one absolute WordPress path in $config."
configured_root=${lines[0]%$'\r'}
[[ "$configured_root" == /* ]] || fail "Use an absolute WordPress path in $config."
wordpress_root=$(cd -- "$configured_root" && pwd -P) || fail "Cannot open the configured WordPress directory."
repo_root=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd -P)

if [[ "$repo_root/" == "$wordpress_root/"* || "$wordpress_root/" == "$repo_root/"* ]]; then
    fail "Use separate directories for the repository and the hosted WordPress installation."
fi
if [[ ! -f "$wordpress_root/wp-load.php" || ! -f "$wordpress_root/wp-includes/version.php" ]]; then
    fail "The configured path does not contain a standard WordPress installation."
fi

for parent in wp-content wp-content/themes wp-content/plugins; do
    path="$wordpress_root/$parent"
    [[ -d "$path" && ! -L "$path" ]] || fail "Expected a real directory at $path."
done
for parent in wp-content/themes wp-content/plugins; do
    [[ -w "$wordpress_root/$parent" ]] || fail "The cPanel account needs write access to $wordpress_root/$parent."
done

theme=wp-content/themes/savannah-crest
plugin=wp-content/plugins/scs-core

for project in "$theme" "$plugin"; do
    target="$wordpress_root/$project"
    [[ ! -L "$target" ]] || fail "Deployment folder is a symbolic link: $target."
    if [[ -e "$target" ]]; then
        [[ -d "$target" && -w "$target" ]] || fail "Expected a writable project directory at $target."
        symlink=$(find "$target" -type l -print -quit)
        [[ -z "$symlink" ]] || fail "Project directory contains a symbolic link: $symlink."
    fi
done

git_bin=git
if ! command -v git >/dev/null 2>&1; then
    git_bin=/usr/local/cpanel/3rdparty/bin/git
    [[ -x "$git_bin" ]] || fail "Git is unavailable in this cPanel account."
fi
commit=$("$git_bin" -C "$repo_root" rev-parse --verify HEAD)
"$git_bin" -C "$repo_root" cat-file -e "$commit:$theme/style.css" || fail "The commit has no Savannah Crest theme."
"$git_bin" -C "$repo_root" cat-file -e "$commit:$plugin/scs-core.php" || fail "The commit has no Savannah Crest Core plugin."
tree=$("$git_bin" -C "$repo_root" ls-tree -r "$commit" -- "$theme" "$plugin")
if grep -Eq '^(120000|160000) ' <<< "$tree"; then
    fail "The committed deployment files include a symbolic link or a submodule."
fi

printf 'Savannah Crest revision %s, WordPress directory %s\n' "$commit" "$wordpress_root"
if [[ "${2:-}" == --check ]]; then
    printf 'The deployment paths passed preflight.\n'
    exit 0
fi

"$git_bin" -C "$repo_root" -c core.autocrlf=false -c core.eol=lf archive --format=tar "$commit" -- "$theme" "$plugin" |
    tar --extract --file=- --directory="$wordpress_root" --no-overwrite-dir
printf 'Deployed the Savannah Crest theme and core plugin.\n'
