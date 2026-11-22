<?php
namespace SavannahCrest\Theme;

use SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

const CONTACT_PHONE = '+254 723 464058';
const CONTACT_PHONE_URL = 'tel:+254723464058';
const CONTACT_EMAIL = 'info@savannahcrestsafaris.com';

add_action( 'init', static function (): void {
	wp_register_script(
		'scs-navigation-editor',
		get_theme_file_uri( 'assets/navigation-editor.js' ),
		array( 'wp-blocks', 'wp-element', 'wp-server-side-render' ),
		VERSION,
		true
	);
	wp_register_script( 'scs-navigation', get_theme_file_uri( 'assets/navigation.js' ), array(), VERSION, true );
	register_block_type( __DIR__ . '/../blocks/site-header', array( 'render_callback' => __NAMESPACE__ . '\render_header' ) );
} );

function menu_group( string $title, array $links ): array {
	return array( 'title' => $title, 'links' => array_filter( $links ) );
}

function menu_term_links( array $terms, string $filter, string $catalogue ): array {
	$links = array();
	foreach ( $terms as $term ) {
		$url = Core\browse_term_url( $term, $filter, $catalogue );
		if ( $url ) {
			$links[ $term->name ] = $url;
		}
	}
	return $links;
}

function menu_panel( string $label, string $all_label, string $url, array $groups, array $feature ): array {
	return array(
		'label' => $label,
		'all_label' => $all_label,
		'url' => $url,
		'groups' => array_filter( $groups, static function ( array $group ): bool { return (bool) $group['links']; } ),
		'feature' => $feature,
	);
}

function menu_panels( string $planner, string $enquiry, string $stories ): array {
	$panels = array();
	$catalogue = (string) get_post_type_archive_link( 'scs_safari' );
	$destinations = page_link( 'destinations' ) ?: ( $catalogue ? $catalogue . '#safari-filter-where' : '' );
	$experiences = page_link( 'experiences' ) ?: ( $catalogue ? $catalogue . '#safari-filter-experience' : '' );
	$about = page_link( 'about' );

	if ( $catalogue && function_exists( 'SavannahCrest\Core\browse_term_url' ) ) {
		$inventory = Core\inventory_terms();
		$countries = array_filter( $inventory['where'], static function ( \WP_Term $term ): bool {
			return ! $term->parent && ! isset( Core\DESTINATION_REGIONS[ $term->slug ] );
		} );
		$places = array_diff_key( $inventory['where'], $countries );
		$east = array_intersect_key( $countries, array_flip( array( 'kenya', 'tanzania', 'uganda', 'rwanda' ) ) );
		$south = array_intersect_key( $countries, array_flip( array( 'botswana', 'namibia', 'south-africa', 'zambia', 'zimbabwe', 'malawi', 'mozambique' ) ) );
		$elsewhere = array_diff_key( $countries, $east, $south );
		$country_links = array();
		foreach ( $countries as $country ) {
			$country_links[ $country->name ] = add_query_arg( 'where', $country->slug, $catalogue );
		}

		$trips = Core\public_posts( Core\SAFARI, array( 'posts_per_page' => 5 ) );
		$trip_links = array();
		foreach ( $trips as $trip ) {
			$trip_links[ get_the_title( $trip ) ] = get_permalink( $trip );
		}
		$feature_trip = $trips[0] ?? null;
		$recent_links = array_slice( $trip_links, 1 );
		$panels[] = menu_panel( __( 'Safaris & tours', 'savannah-crest' ), __( 'View all safaris', 'savannah-crest' ), $catalogue, array(
			menu_group( __( 'Safaris by country', 'savannah-crest' ), $country_links ),
			menu_group( __( 'Recently added safaris', 'savannah-crest' ), $recent_links ),
		), array(
			'image' => 'menu-plains.webp',
			'attachment' => $feature_trip ? get_post_thumbnail_id( $feature_trip ) : 0,
			'label' => $feature_trip ? get_the_title( $feature_trip ) : __( 'Explore the safari catalogue', 'savannah-crest' ),
			'url' => $feature_trip ? get_permalink( $feature_trip ) : $catalogue,
		) );

		$panels[] = menu_panel( __( 'Destinations', 'savannah-crest' ), __( 'View all destinations', 'savannah-crest' ), $destinations, array(
			menu_group( __( 'East Africa', 'savannah-crest' ), menu_term_links( $east, 'where', $catalogue ) ),
			menu_group( __( 'Southern Africa', 'savannah-crest' ), menu_term_links( $south, 'where', $catalogue ) ),
			menu_group( __( 'Other destinations', 'savannah-crest' ), menu_term_links( $elsewhere, 'where', $catalogue ) ),
			menu_group( __( 'Parks & places', 'savannah-crest' ), menu_term_links( array_slice( $places, 0, 8 ), 'where', $catalogue ) ),
		), array(
			'image' => 'menu-plains.webp',
			'label' => isset( $countries['kenya'] ) ? __( 'Explore Kenya', 'savannah-crest' ) : __( 'Find your destination', 'savannah-crest' ),
			'url' => isset( $countries['kenya'] ) ? Core\browse_term_url( $countries['kenya'], 'where', $catalogue ) : $destinations,
		) );

		$panels[] = menu_panel( __( 'Experiences', 'savannah-crest' ), __( 'View all experiences', 'savannah-crest' ), $experiences, array(
			menu_group( __( 'Browse by experience', 'savannah-crest' ), menu_term_links( $inventory['experience'], 'experience', $catalogue ) ),
			menu_group( __( 'Planning your trip', 'savannah-crest' ), array(
				__( 'Plan a tailor-made safari', 'savannah-crest' ) => $planner,
				__( 'Safari planning FAQs', 'savannah-crest' ) => $about ? $about . '#safari-faqs' : '',
			) ),
		), array(
			'image' => 'menu-wildlife.webp',
			'label' => isset( $inventory['experience']['game-drives'] ) ? __( 'Explore game drives', 'savannah-crest' ) : __( 'Explore safari experiences', 'savannah-crest' ),
			'url' => isset( $inventory['experience']['game-drives'] ) ? Core\browse_term_url( $inventory['experience']['game-drives'], 'experience', $catalogue ) : $experiences,
		) );
	} else {
		foreach ( array(
			array( __( 'Safaris & tours', 'savannah-crest' ), $catalogue ),
			array( __( 'Destinations', 'savannah-crest' ), $destinations ),
			array( __( 'Experiences', 'savannah-crest' ), $experiences ),
		) as list( $label, $url ) ) {
			if ( $url ) {
				$panels[] = menu_panel( $label, $label, $url, array(), array() );
			}
		}
	}

	if ( $about ) {
		$panels[] = menu_panel( __( 'About us', 'savannah-crest' ), __( 'About Savannah Crest', 'savannah-crest' ), $about, array(
			menu_group( __( 'Get to know us', 'savannah-crest' ), array(
				__( 'Meet Sam, our founder', 'savannah-crest' ) => $about . '#meet-sam',
				__( 'Why travel with us', 'savannah-crest' ) => $about . '#our-standards',
				__( 'How we plan your safari', 'savannah-crest' ) => $about . '#how-it-works',
			) ),
			menu_group( __( 'Before you travel', 'savannah-crest' ), array(
				__( 'Safari planning FAQs', 'savannah-crest' ) => $about . '#safari-faqs',
				__( 'Travel guides', 'savannah-crest' ) => page_link( 'guides' ),
				__( 'Contact us', 'savannah-crest' ) => $enquiry,
			) ),
		), array(
			'image' => 'menu-field-notes.webp',
			'label' => $stories ? __( 'Read traveller stories', 'savannah-crest' ) : __( 'Meet Sam', 'savannah-crest' ),
			'url' => $stories ?: $about . '#meet-sam',
		) );
	}
	return $panels;
}

function render_header(): string {
	$planner = page_link( 'plan-your-safari' );
	$enquiry = page_link( 'enquire' );
	$stories = page_link( 'traveller-stories' );
	$panels = menu_panels( $planner, $enquiry, $stories );
	$search_id = wp_unique_id( 'scs-search-' );
	$nav_id = wp_unique_id( 'scs-primary-' );
	ob_start();
	require __DIR__ . '/../views/site-header.php';
	return (string) ob_get_clean();
}
