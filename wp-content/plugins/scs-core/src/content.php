<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

const SAFARI      = 'scs_safari';
const STAY        = 'scs_stay';
const DESTINATION = 'scs_destination';
const EXPERIENCE  = 'scs_experience';
const COMFORT     = 'scs_comfort';
const REWRITE_REV = '1';

function register_content(): void {
	register_post_type( SAFARI, array(
		'labels'       => array(
			'name'          => __( 'Safaris', 'scs-core' ),
			'singular_name' => __( 'Safari', 'scs-core' ),
			'add_new_item'  => __( 'Add Safari', 'scs-core' ),
			'edit_item'     => __( 'Edit Safari', 'scs-core' ),
		),
		'public'       => true,
		'show_in_rest' => true,
		'has_archive'  => 'safaris',
		'rewrite'      => array( 'slug' => 'safaris', 'with_front' => false ),
		'menu_icon'    => 'dashicons-location-alt',
		'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'custom-fields' ),
		'capability_type' => 'post',
		'map_meta_cap' => true,
	) );

	register_post_type( STAY, array(
		'labels'       => array( 'name' => __( 'Stays', 'scs-core' ), 'singular_name' => __( 'Stay', 'scs-core' ) ),
		'public'       => true,
		'show_in_rest' => true,
		'has_archive'  => false,
		'rewrite'      => array( 'slug' => 'stays', 'with_front' => false ),
		'menu_icon'    => 'dashicons-admin-home',
		'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author' ),
		'capability_type' => 'post',
		'map_meta_cap' => true,
	) );

	register_taxonomy( DESTINATION, array( SAFARI, STAY, 'post' ), array(
		'labels'            => array( 'name' => __( 'Destinations', 'scs-core' ), 'singular_name' => __( 'Destination', 'scs-core' ) ),
		'public'            => true,
		'hierarchical'      => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'destinations', 'with_front' => false, 'hierarchical' => true ),
	) );

	register_taxonomy( EXPERIENCE, array( SAFARI, 'post' ), array(
		'labels'            => array( 'name' => __( 'Experiences', 'scs-core' ), 'singular_name' => __( 'Experience', 'scs-core' ) ),
		'public'            => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'experiences', 'with_front' => false ),
	) );

	register_taxonomy( COMFORT, array( SAFARI, STAY ), array(
		'labels'            => array( 'name' => __( 'Comfort levels', 'scs-core' ), 'singular_name' => __( 'Comfort level', 'scs-core' ) ),
		'public'            => false,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'meta_box_cb'       => false,
		'rewrite'           => false,
		'query_var'         => false,
	) );
}

function activate(): void {
	register_content();
	flush_rewrite_rules( false );
	update_option( 'scs_rewrite_revision', REWRITE_REV, false );
}

function deactivate(): void {
	foreach ( array( DESTINATION, EXPERIENCE, COMFORT ) as $taxonomy ) {
		unregister_taxonomy( $taxonomy );
	}
	unregister_post_type( SAFARI );
	unregister_post_type( STAY );
	flush_rewrite_rules( false );
	delete_option( 'scs_rewrite_revision' );
}

function maybe_refresh_rewrites(): void {
	if ( REWRITE_REV !== get_option( 'scs_rewrite_revision' ) ) {
		flush_rewrite_rules( false );
		update_option( 'scs_rewrite_revision', REWRITE_REV, false );
	}
}

function field_group_paths( array $paths ): array {
	$paths[] = DIR . '/scf-json';
	return $paths;
}

function dependency_notice(): void {
	if ( function_exists( 'get_field' ) && function_exists( 'acf_get_field_type' ) && acf_get_field_type( 'repeater' ) ) {
		return;
	}
	if ( current_user_can( 'activate_plugins' ) ) {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Savannah Crest Core needs Secure Custom Fields with Repeater support to edit and publish complete safari itineraries.', 'scs-core' ) .
			'</p></div>';
	}
}

function field( string $name, $object = false ) {
	return function_exists( 'get_field' ) ? get_field( $name, $object ) : false;
}

function rows( $value ): array {
	return is_array( $value ) ? array_values( array_filter( $value, 'is_array' ) ) : array();
}

function text( $value ): string {
	return is_scalar( $value ) ? trim( (string) $value ) : '';
}

function positive_int( $value ): int {
	return is_scalar( $value ) && filter_var( $value, FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) )
		? (int) $value
		: 0;
}

function ids( $value ): array {
	return array_values( array_unique( array_filter( array_map( __NAMESPACE__ . '\positive_int', (array) $value ) ) ) );
}

function term_field( string $name, \WP_Term $term ) {
	return field( $name, $term->taxonomy . '_' . $term->term_id );
}

function hub_ready( \WP_Term $term ): bool {
	return (bool) get_term_meta( $term->term_id, 'scs_term_ready', true )
		&& '' !== trim( wp_strip_all_tags( (string) get_term_meta( $term->term_id, 'scs_term_intro', true ) . $term->description . (string) get_term_meta( $term->term_id, 'scs_term_body', true ) ) );
}

function term_label( $id, string $taxonomy = DESTINATION ): string {
	$term = get_term( positive_int( $id ), $taxonomy );
	return $term instanceof \WP_Term ? $term->name : '';
}

function page_url( string $slug ): string {
	$page = get_page_by_path( $slug );
	return $page instanceof \WP_Post && 'publish' === $page->post_status ? (string) get_permalink( $page ) : '';
}

function catalogue_url(): string {
	return (string) get_post_type_archive_link( SAFARI );
}
