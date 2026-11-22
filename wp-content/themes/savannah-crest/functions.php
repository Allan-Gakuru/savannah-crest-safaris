<?php
/**
 * Savannah Crest theme support and assets.
 */
namespace SavannahCrest\Theme;

defined( 'ABSPATH' ) || exit;

const VERSION = '0.3.5';

require_once __DIR__ . '/inc/navigation.php';

add_action( 'after_setup_theme', static function (): void {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_editor_style( 'style.css' );
} );

add_action( 'wp_enqueue_scripts', static function (): void {
	wp_enqueue_style( 'savannah-crest', get_stylesheet_uri(), array(), VERSION );
	wp_enqueue_style( 'savannah-crest-navigation', get_theme_file_uri( 'assets/navigation.css' ), array( 'savannah-crest' ), VERSION );
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
	$page = get_page_by_path( $slug, OBJECT, array( 'page' ) );
	return $page instanceof \WP_Post && 'publish' === $page->post_status && '' === $page->post_password
		? (string) get_permalink( $page ) : '';
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
	$catalogue = (string) get_post_type_archive_link( 'scs_safari' );
	return array_filter( array(
		__( 'Safaris', 'savannah-crest' )      => $catalogue,
		__( 'Destinations', 'savannah-crest' ) => page_link( 'destinations' ) ?: ( $catalogue ? $catalogue . '#safari-filter-where' : '' ),
		__( 'Experiences', 'savannah-crest' )  => page_link( 'experiences' ) ?: ( $catalogue ? $catalogue . '#safari-filter-experience' : '' ),
		__( 'Travel Guide', 'savannah-crest' ) => page_link( 'guides' ),
		__( 'About us', 'savannah-crest' )     => page_link( 'about' ),
		__( 'Traveller stories', 'savannah-crest' ) => page_link( 'traveller-stories' ),
	) );
}
