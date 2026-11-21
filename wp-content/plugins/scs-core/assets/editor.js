(function (wp, blocks) {
	'use strict';

	blocks.forEach(function (block) {
		wp.blocks.registerBlockType(block.name, {
			title: block.title,
			description: block.description,
			category: block.category,
			icon: 'location-alt',
			attributes: block.attributes,
			supports: block.supports,
			edit: function () {
				const props = wp.blockEditor.useBlockProps();
				return wp.element.createElement('div', props,
					wp.element.createElement('strong', null, block.title),
					wp.element.createElement('p', null, block.description)
				);
			},
			save: function () { return null; }
		});
	});

	wp.domReady(function () {
		const panel = 'taxonomy-panel-scs_comfort';
		function hideComfortPanel() {
			const editor = wp.data.dispatch('core/editor');
			const legacyEditor = wp.data.dispatch('core/edit-post');
			if (editor && typeof editor.removeEditorPanel === 'function') {
				editor.removeEditorPanel(panel);
			} else if (legacyEditor && typeof legacyEditor.removeEditorPanel === 'function') {
				legacyEditor.removeEditorPanel(panel);
			}
		}
		hideComfortPanel();
	});
})(window.wp, window.scsBlocks || []);
