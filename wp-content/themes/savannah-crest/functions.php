<?php
/**
 * Savannah Crest theme support and assets.
 */
namespace SavannahCrest\Theme;

defined( 'ABSPATH' ) || exit;

const VERSION = '0.1.0';

add_action( 'after_setup_theme', static function (): void {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_editor_style( 'style.css' );
} );

add_action( 'wp_enqueue_scripts', static function (): void {
	wp_enqueue_style( 'savannah-crest', get_stylesheet_uri(), array(), VERSION );
} );

add_action( 'wp_head', static function (): void {
	if ( has_site_icon() ) {
		return;
	}
	echo '<link rel="icon" href="' . esc_url( get_theme_file_uri( 'assets/brand/favicon.svg' ) ) . '" type="image/svg+xml">';
	echo '<link rel="alternate icon" href="' . esc_url( get_theme_file_uri( 'assets/brand/favicon.ico' ) ) . '" sizes="any">';
	echo '<link rel="apple-touch-icon" href="' . esc_url( get_theme_file_uri( 'assets/brand/apple-touch-icon.png' ) ) . '">';
}, 9 );

function page_link( string $slug ): string {
	$page = get_page_by_path( $slug );
	return $page instanceof \WP_Post && 'publish' === $page->post_status ? (string) get_permalink( $page ) : '';
}

function site_mark( bool $reverse = false ): void {
	$logo = $reverse ? 'logo-horizontal-reverse.svg' : 'logo-horizontal.svg';
	$shield = $reverse ? 'shield-cream.svg' : 'shield-espresso.svg';
	?>
	<a class="scs-site-mark" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<picture>
			<?php if ( ! $reverse ) : ?><source media="(max-width: 560px)" srcset="<?php echo esc_url( get_theme_file_uri( 'assets/brand/' . $shield ) ); ?>"><?php endif; ?>
			<img src="<?php echo esc_url( get_theme_file_uri( 'assets/brand/' . $logo ) ); ?>" width="240" height="46" alt="<?php esc_attr_e( 'Savannah Crest Safaris home', 'savannah-crest' ); ?>">
		</picture>
	</a>
	<?php
}

function links(): array {
	return array_filter( array(
		__( 'Safaris', 'savannah-crest' )      => (string) get_post_type_archive_link( 'scs_safari' ),
		__( 'Destinations', 'savannah-crest' ) => page_link( 'destinations' ),
		__( 'Experiences', 'savannah-crest' )  => page_link( 'experiences' ),
		__( 'Travel Guide', 'savannah-crest' ) => page_link( 'guides' ),
		__( 'About', 'savannah-crest' )        => page_link( 'about' ),
	) );
}

function nav_link_block( string $label, string $url ): void {
	$attributes = wp_json_encode( array( 'label' => $label, 'url' => $url, 'kind' => 'custom' ), JSON_HEX_TAG | JSON_HEX_AMP );
	echo '<!-- wp:navigation-link ' . $attributes . ' /-->';
}
