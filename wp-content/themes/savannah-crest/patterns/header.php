<?php
/**
 * Title: Site header
 * Slug: savannah-crest/site-header
 * Inserter: no
 */
use function SavannahCrest\Theme\links;
use function SavannahCrest\Theme\nav_link_block;
use function SavannahCrest\Theme\page_link;
use function SavannahCrest\Theme\site_mark;

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"className":"scs-site-header","layout":{"type":"default"}} -->
<div class="wp-block-group scs-site-header">
	<!-- wp:group {"className":"scs-header-inner scs-shell","layout":{"type":"default"}} -->
	<div class="wp-block-group scs-header-inner scs-shell">
		<!-- wp:html -->
		<?php site_mark(); ?>
		<!-- /wp:html -->
		<!-- wp:navigation {"overlayMenu":"mobile","className":"scs-primary-nav","layout":{"type":"flex","justifyContent":"center"}} -->
		<?php foreach ( links() as $label => $url ) { nav_link_block( $label, $url ); } ?>
		<!-- /wp:navigation -->
		<?php if ( page_link( 'plan-your-safari' ) ) : ?>
			<!-- wp:html -->
			<a class="scs-button scs-header-action" href="<?php echo esc_url( page_link( 'plan-your-safari' ) ); ?>"><?php esc_html_e( 'Plan your safari', 'savannah-crest' ); ?></a>
			<!-- /wp:html -->
		<?php endif; ?>
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
