<?php
/**
 * Title: Planner header
 * Slug: savannah-crest/planner-header
 * Inserter: no
 */
use function SavannahCrest\Theme\site_mark;

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"className":"scs-site-header","layout":{"type":"default"}} -->
<div class="wp-block-group scs-site-header">
	<!-- wp:html -->
	<div class="scs-header-inner scs-shell scs-header-inner--quiet">
		<?php site_mark(); ?>
		<a class="scs-text-link" href="<?php echo esc_url( get_post_type_archive_link( 'scs_safari' ) ?: home_url( '/' ) ); ?>"><?php esc_html_e( 'Continue exploring', 'savannah-crest' ); ?></a>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
