<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

function ensure_editorial_pages(): void {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'publish_pages' ) ) {
		return;
	}

	$pages = array(
		'about' => __( 'About us', 'scs-core' ),
		'traveller-stories' => __( 'Traveller stories', 'scs-core' ),
		'destinations' => __( 'Destinations', 'scs-core' ),
		'experiences' => __( 'Experiences', 'scs-core' ),
	);
	foreach ( $pages as $slug => $title ) {
		$option = 'scs_editorial_page_' . $slug;
		if ( get_option( $option ) ) {
			continue;
		}
		$source = DIR . '/pages/' . $slug . '.html';
		$content = is_readable( $source ) ? file_get_contents( $source ) : false;
		if ( false === $content || ! add_option( $option, '1', '', false ) ) {
			continue;
		}
		if ( get_page_by_path( $slug, OBJECT, array( 'page' ) ) ) {
			continue;
		}
		$page_id = wp_insert_post( wp_slash( array(
			'post_type' => 'page',
			'post_name' => $slug,
			'post_title' => $title,
			'post_content' => $content,
			'post_status' => 'publish',
		) ), true );
		if ( ! $page_id || is_wp_error( $page_id ) ) {
			delete_option( $option );
		}
	}
}
