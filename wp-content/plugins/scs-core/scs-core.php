<?php
/**
 * Plugin Name: Savannah Crest Core
 * Description: Safari content, catalogue, itinerary blocks and planner integration.
 * Version: 0.2.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Text Domain: scs-core
 */

namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

const VERSION = '0.2.0';
const FILE    = __FILE__;
const DIR     = __DIR__;

require_once DIR . '/src/content.php';
require_once DIR . '/src/safari.php';
require_once DIR . '/src/catalogue.php';
require_once DIR . '/src/destinations.php';
require_once DIR . '/src/planner.php';
require_once DIR . '/src/editorial.php';
require_once DIR . '/src/seo.php';
require_once DIR . '/src/blocks.php';

add_action( 'init', __NAMESPACE__ . '\register_content', 5 );
add_action( 'init', __NAMESPACE__ . '\register_blocks' );
add_action( 'init', __NAMESPACE__ . '\maybe_refresh_rewrites', 99 );
add_action( 'admin_notices', __NAMESPACE__ . '\dependency_notice' );
add_action( 'admin_init', __NAMESPACE__ . '\register_planner_settings' );
add_action( 'admin_init', __NAMESPACE__ . '\ensure_enquiry_page' );
add_action( 'admin_init', __NAMESPACE__ . '\ensure_editorial_pages' );
add_filter( 'acf/settings/load_json', __NAMESPACE__ . '\field_group_paths' );
add_filter( 'rest_pre_insert_scs_safari', __NAMESPACE__ . '\validate_rest_safari', 10, 2 );
add_filter( 'wp_insert_post_empty_content', __NAMESPACE__ . '\guard_safari_write', 10, 2 );
add_action( 'pre_get_posts', __NAMESPACE__ . '\filter_catalogue' );
add_filter( 'query_vars', __NAMESPACE__ . '\public_filter_vars' );
add_filter( 'redirect_canonical', __NAMESPACE__ . '\preserve_catalogue_controls' );
add_action( 'template_redirect', __NAMESPACE__ . '\exclude_enquiry_forms_from_cache', 0 );
add_filter( 'wp_robots', __NAMESPACE__ . '\robots' );
add_filter( 'document_title_parts', __NAMESPACE__ . '\destination_document_title' );
add_filter( 'wpseo_metadesc', __NAMESPACE__ . '\destination_meta_description' );
add_action( 'wp_head', __NAMESPACE__ . '\destination_core_meta' );
add_filter( 'wp_sitemaps_taxonomies_query_args', __NAMESPACE__ . '\term_sitemap', 10, 2 );
add_filter( 'wp_sitemaps_posts_query_args', __NAMESPACE__ . '\page_sitemap', 10, 2 );
add_filter( 'wpseo_robots_array', __NAMESPACE__ . '\robots' );
add_filter( 'wpseo_canonical', static function ( $canonical ) {
	return is_post_type_archive( SAFARI ) && has_catalogue_request() ? false : $canonical;
} );
add_action( 'wp_enqueue_scripts', static function (): void {
	$dependencies = is_page( array( PLANNER_SLUG, ENQUIRY_SLUG ) ) ? array( 'jquery' ) : array();
	wp_enqueue_script( 'scs-events', plugins_url( 'assets/events.js', FILE ), $dependencies, VERSION, true );
	if ( is_page( array( PLANNER_SLUG, ENQUIRY_SLUG ) ) ) {
		wp_enqueue_script( 'scs-form-flow', plugins_url( 'assets/form-flow.js', FILE ), array(), VERSION, true );
	}
	if ( is_page( ENQUIRY_SLUG ) ) {
		wp_enqueue_script( 'scs-enquiry', plugins_url( 'assets/enquiry.js', FILE ), array(), VERSION, true );
	}
} );
add_filter( 'wpseo_sitemap_entry', __NAMESPACE__ . '\yoast_sitemap_entry', 10, 3 );

register_activation_hook( FILE, __NAMESPACE__ . '\activate' );
register_deactivation_hook( FILE, __NAMESPACE__ . '\deactivate' );
