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

function destination_guide( \WP_Term $term ): array {
	if ( DESTINATION !== $term->taxonomy || ! isset( destination_guides()[ $term->slug ] ) ) {
		return array();
	}
	return isset( inventory_terms()['where'][ $term->slug ] ) ? destination_guides()[ $term->slug ] : array();
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
