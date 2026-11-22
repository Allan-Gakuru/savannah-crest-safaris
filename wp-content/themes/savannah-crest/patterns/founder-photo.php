<?php
/**
 * Title: Savannah Crest founder photo
 * Slug: savannah-crest/founder-photo
 * Inserter: no
 */
defined( 'ABSPATH' ) || exit;

$portrait = 'assets/images/sam-founder.webp';
if ( ! is_readable( get_theme_file_path( $portrait ) ) ) {
	return;
}
?>
<!-- wp:image {"className":"scs-founder-image"} -->
<figure class="wp-block-image scs-founder-image"><img src="<?php echo esc_url( get_theme_file_uri( $portrait ) ); ?>" alt="<?php esc_attr_e( 'Sam, founder of Savannah Crest Safaris', 'savannah-crest' ); ?>" width="880" height="856" loading="lazy" decoding="async"></figure>
<!-- /wp:image -->
