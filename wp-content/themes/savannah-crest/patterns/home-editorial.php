<?php
/**
 * Title: Homepage editorial content
 * Slug: savannah-crest/home-editorial
 * Inserter: no
 */

defined( 'ABSPATH' ) || exit;

if ( 'page' !== get_option( 'show_on_front' ) || ! get_option( 'page_on_front' ) ) {
	return;
}
?>
<!-- wp:group {"className":"scs-shell scs-editorial-home","layout":{"type":"default"}} -->
<div class="wp-block-group scs-shell scs-editorial-home">
	<!-- wp:post-content {"layout":{"type":"constrained"}} /-->
</div>
<!-- /wp:group -->
