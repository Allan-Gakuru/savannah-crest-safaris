<?php
/**
 * Title: Homepage introduction
 * Slug: savannah-crest/home-hero
 * Inserter: no
 */
defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"tagName":"section","className":"scs-home-hero scs-shell","layout":{"type":"default"}} -->
<section class="wp-block-group scs-home-hero scs-shell">
	<!-- wp:group {"className":"scs-home-hero-copy","layout":{"type":"default"}} -->
	<div class="wp-block-group scs-home-hero-copy">
		<!-- wp:heading {"level":1} -->
		<h1 class="wp-block-heading"><?php esc_html_e( 'Kenya, at your own pace', 'savannah-crest' ); ?></h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph -->
		<p><?php esc_html_e( 'Explore Kenya safari itineraries, then plan a route around how you want to travel.', 'savannah-crest' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:group {"className":"scs-home-actions","layout":{"type":"default"}} -->
		<div class="wp-block-group scs-home-actions">
			<!-- wp:scs/site-action {"action":"plan"} /-->
			<!-- wp:scs/site-action {"action":"explore"} /-->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
	<?php if ( 'page' === get_option( 'show_on_front' ) && has_post_thumbnail( (int) get_option( 'page_on_front' ) ) ) : ?>
		<!-- wp:post-featured-image {"sizeSlug":"full","className":"scs-home-image"} /-->
	<?php endif; ?>
</section>
<!-- /wp:group -->
