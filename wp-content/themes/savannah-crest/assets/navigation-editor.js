(function (blocks, element, serverSideRender) {
	'use strict';

	blocks.registerBlockType('savannah-crest/site-header', {
		edit: function () {
			return element.createElement(serverSideRender.default || serverSideRender, {
				block: 'savannah-crest/site-header',
				httpMethod: 'POST'
			});
		},
		save: function () {
			return null;
		}
	});
})(wp.blocks, wp.element, wp.serverSideRender);
