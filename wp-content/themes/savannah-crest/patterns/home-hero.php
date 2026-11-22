<?php
/**
 * Title: Homepage introduction
 * Slug: savannah-crest/home-hero
 * Inserter: no
 */
defined( 'ABSPATH' ) || exit;

$front_page = 'page' === get_option( 'show_on_front' ) ? (int) get_option( 'page_on_front' ) : 0;
$photo = $front_page ? get_the_post_thumbnail( $front_page, 'full', array(
	'alt'           => '',
	'loading'       => 'eager',
	'fetchpriority' => 'high',
	'sizes'         => '100vw',
) ) : '';
?>
<!-- wp:group {"tagName":"section","align":"full","className":"scs-home-hero","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull scs-home-hero">
	<!-- wp:html -->
	<figure class="scs-home-image" aria-hidden="true">
		<?php if ( $photo ) : ?>
			<?php echo $photo; // WordPress attachment markup. ?>
		<?php else : ?>
			<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/kenya-hero.webp' ) ); ?>" srcset="<?php echo esc_url( get_theme_file_uri( 'assets/images/kenya-hero-small.webp' ) ); ?> 768w, <?php echo esc_url( get_theme_file_uri( 'assets/images/kenya-hero.webp' ) ); ?> 1536w" sizes="100vw" width="1536" height="1024" alt="" fetchpriority="high">
		<?php endif; ?>
	</figure>
	<!-- /wp:html -->
	<!-- wp:group {"className":"scs-home-hero-copy scs-shell","layout":{"type":"default"}} -->
	<div class="wp-block-group scs-home-hero-copy scs-shell">
		<!-- wp:heading {"level":1} -->
		<h1 class="wp-block-heading"><?php esc_html_e( 'African Safaris, at your own pace', 'savannah-crest' ); ?></h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph -->
		<p><?php esc_html_e( 'Explore African safari itineraries, then plan a route around how you want to travel.', 'savannah-crest' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:group {"className":"scs-home-actions","layout":{"type":"default"}} -->
		<div class="wp-block-group scs-home-actions">
			<!-- wp:scs/site-action {"action":"plan"} /-->
			<!-- wp:scs/site-action {"action":"explore"} /-->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
