<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

$post = block_post( $block, 'post' );
if ( ! $post ) {
	return;
}
$reviewed = valid_rate_date( field( 'scs_guide_reviewed_on', $post->ID ) );
$date = '' !== $reviewed ? \DateTimeImmutable::createFromFormat( '!Ymd', $reviewed, wp_timezone() ) : false;
$trip_ids = ids( field( 'scs_guide_trips', $post->ID ) );
$match = array( 'relation' => 'OR' );
foreach ( array( DESTINATION, EXPERIENCE ) as $taxonomy ) {
	$terms = get_the_terms( $post, $taxonomy );
	foreach ( is_array( $terms ) ? $terms : array() as $term ) {
		$match[] = term_clause( $term );
	}
}
$trips = $trip_ids ? public_posts( SAFARI, array( 'post__in' => $trip_ids, 'orderby' => 'post__in' ) ) : array();
if ( ! $trips && count( $match ) > 1 ) {
	$trips = matching_safaris( $match, array(), 3 );
}
if ( $date ) {
	echo '<p class="scs-reviewed">' . esc_html( sprintf( __( 'Reviewed %s', 'scs-core' ), wp_date( get_option( 'date_format' ), $date->getTimestamp() ) ) ) . '</p>';
}
if ( $trips ) {
	echo '<section class="scs-section"><h2>' . esc_html__( 'Related safaris', 'scs-core' ) . '</h2>';
	cards( $trips );
	echo '</section>';
}
