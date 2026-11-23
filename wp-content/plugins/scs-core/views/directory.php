<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

$experience = 'experience' === ( $attributes['type'] ?? '' );
$taxonomy = $experience ? EXPERIENCE : DESTINATION;
$filter = $experience ? 'experience' : 'where';
$terms = inventory_terms()[ $filter ];
foreach ( ready_terms( $taxonomy ) as $term ) {
	$terms[ $term->slug ] = $term;
}
$primary = $experience ? $terms : array_filter( $terms, static function ( \WP_Term $term ): bool {
	return ! $term->parent && ! isset( DESTINATION_REGIONS[ $term->slug ] ) && ! isset( curated_destination_guides()[ $term->slug ] );
} );
$sections = $experience
	? array( __( 'Find an experience', 'scs-core' ) => $primary )
	: array( __( 'Browse by country', 'scs-core' ) => $primary, __( 'Parks and places', 'scs-core' ) => array_diff_key( $terms, $primary ) );

foreach ( array_filter( $sections ) as $heading => $items ) {
	echo '<section class="scs-shell scs-section"><h2>' . esc_html( $heading ) . '</h2>';
	term_items( $items, $filter );
	echo '</section>';
}
if ( ! $terms ) {
	echo '<div class="scs-shell scs-section">';
	echo action_link( catalogue_url(), __( 'Explore safaris', 'scs-core' ) ); // Escaped by action_link().
	echo '</div>';
}
