<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

const DESTINATION_REGIONS = array( 'zanzibar' => 'tanzania' );

function destination_guides(): array {
	static $guides;
	if ( null === $guides ) {
		$guides = require __DIR__ . '/../content/destination-guides.php';
	}
	return $guides;
}

function curated_destination_guides(): array {
	static $guides;
	$guides ??= require __DIR__ . '/../content/curated-destinations.php';
	return $guides;
}

function destination_guide( \WP_Term $term ): array {
	if ( DESTINATION !== $term->taxonomy ) {
		return array();
	}
	if ( isset( curated_destination_guides()[ $term->slug ] ) ) {
		return curated_destination_guides()[ $term->slug ];
	}
	$country = destination_guides()[ $term->slug ] ?? array();
	return $country && isset( inventory_terms()['where'][ $term->slug ] ) ? $country : array();
}

function provision_curated_destinations(): void {
	$option = 'scs_curated_destinations_seeded';
	$completed = (array) get_option( $option, array() );
	$updated = $completed;
	foreach ( array_diff_key( curated_destination_guides(), $completed ) as $slug => $guide ) {
		$term = get_term_by( 'slug', $slug, DESTINATION );
		if ( ! $term instanceof \WP_Term ) {
			$parent_id = 0;
			if ( isset( $guide['parent'] ) ) {
				$parent = get_term_by( 'slug', $guide['parent'], DESTINATION );
				if ( ! $parent instanceof \WP_Term ) {
					continue;
				}
				$parent_id = $parent->term_id;
			}
			// Keep an existing term and its editorial settings if another request created it.
			wp_insert_term( $guide['name'], DESTINATION, array( 'slug' => $slug, 'parent' => $parent_id ) );
			$term = get_term_by( 'slug', $slug, DESTINATION );
			if ( ! $term instanceof \WP_Term ) {
				continue;
			}
		}
		if ( isset( $guide['seed_trip'] ) ) {
			$safari = get_page_by_path( $guide['seed_trip'], OBJECT, SAFARI );
			if ( ! $safari instanceof \WP_Post || 'publish' !== $safari->post_status || '' !== $safari->post_password ) {
				continue;
			}
			if ( ! has_term( (int) $term->term_id, DESTINATION, $safari->ID ) &&
				is_wp_error( wp_add_object_terms( $safari->ID, (int) $term->term_id, DESTINATION ) ) ) {
				continue;
			}
		}
		$updated[ $slug ] = true;
	}
	if ( $updated !== $completed ) {
		update_option( $option, $updated, false );
	}
}

function country_guides(): array {
	$country_order = array_diff_key( destination_guides(), DESTINATION_REGIONS );
	return array_filter( array_replace(
		array_fill_keys( array_keys( $country_order ), null ),
		array_intersect_key( inventory_terms()['where'], $country_order )
	) );
}

function destination_regions( \WP_Term $term ): array {
	return DESTINATION === $term->taxonomy ? array_keys( DESTINATION_REGIONS, $term->slug, true ) : array();
}

function destination_guide_image( \WP_Term $term, array $guide, bool $card = false ): string {
	$attributes = $card
		? array( 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' )
		: array( 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async' );
	$editorial = image( term_field( 'scs_term_hero', $term ), $card ? 'medium_large' : 'large', $attributes );
	if ( $editorial ) {
		return $editorial;
	}
	$path = 'assets/images/destinations/' . $term->slug;
	if ( ! is_readable( get_theme_file_path( $path . '-720.webp' ) ) || ! is_readable( get_theme_file_path( $path . '-1440.webp' ) ) ) {
		return '';
	}
	$small = get_theme_file_uri( $path . '-720.webp' );
	$large = get_theme_file_uri( $path . '-1440.webp' );
	return sprintf(
		'<img src="%1$s" srcset="%2$s 720w, %3$s 1440w" sizes="%4$s" width="720" height="450" alt="%5$s" loading="%6$s" decoding="async"%7$s>',
		esc_url( $card ? $small : $large ),
		esc_url( $small ),
		esc_url( $large ),
		$card ? '(min-width: 950px) 390px, (min-width: 600px) 46vw, 92vw' : '(min-width: 800px) 54vw, 100vw',
		esc_attr( $card ? '' : $guide['alt'] ),
		$attributes['loading'],
		$card ? '' : ' fetchpriority="high"'
	);
}
