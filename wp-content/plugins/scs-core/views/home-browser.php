<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

$section = $attributes['section'] ?? 'all';
if ( 'browse' !== $section ) : ?>
	<section class="scs-shell scs-finder" aria-label="<?php esc_attr_e( 'Find your safari', 'scs-core' ); ?>">
		<?php filter_form( array(), true ); ?>
	</section>
<?php endif;

if ( 'finder' === $section ) {
	return;
}

$front = (int) get_option( 'page_on_front' );
$safaris = matching_safaris( array(), $front ? ids( field( 'scs_home_featured_trips', $front ) ) : array(), 5 );
$enquiry_url = page_url( ENQUIRY_SLUG ) ?: planner_url();
$place_picks = $front ? ids( field( 'scs_home_featured_places', $front ) ) : array();
$parks = array_filter( inventory_terms()['where'], static function ( \WP_Term $term ): bool {
	return (bool) $term->parent;
} );
$parks = array_column( $parks, null, 'term_id' );
$parks = array_replace( array_intersect_key( array_fill_keys( $place_picks, null ), $parks ), $parks );
$experiences = ready_terms( EXPERIENCE, array(), array(), 4 );
?>
<?php if ( $safaris ) : ?>
	<section class="scs-shell scs-section scs-featured-safaris" aria-label="<?php esc_attr_e( 'Featured safaris', 'scs-core' ); ?>">
		<div class="scs-section-head">
			<div class="scs-featured-safaris__intro">
				<h2><?php esc_html_e( 'African safaris, tailored to you', 'scs-core' ); ?></h2>
				<p><?php esc_html_e( 'Explore a few of our favourite itineraries across Africa. We’ll tailor the route and pace to suit you.', 'scs-core' ); ?></p>
			</div>
			<?php echo action_link( catalogue_url(), __( 'Explore all safaris', 'scs-core' ), 'scs-text-link' ); ?>
		</div>
		<?php cards( $safaris, $enquiry_url ); ?>
	</section>
<?php endif; ?>
<?php require __DIR__ . '/home-countries.php'; ?>
<?php if ( $parks ) : ?>
	<section class="scs-shell scs-section scs-home-destinations">
		<h2><?php esc_html_e( 'Parks and places to explore', 'scs-core' ); ?></h2>
		<?php term_items( array_slice( $parks, 0, 3 ), 'where' ); ?>
	</section>
<?php endif; ?>
<?php if ( $experiences ) : ?>
	<section class="scs-shell scs-section">
		<h2><?php esc_html_e( 'Explore by experience', 'scs-core' ); ?></h2>
		<?php term_items( $experiences ); ?>
	</section>
<?php endif; ?>
<?php if ( planner_url() ) : ?>
	<section class="scs-shell scs-section scs-invitation">
		<h2><?php esc_html_e( 'Start with the trip you have in mind', 'scs-core' ); ?></h2>
		<p><?php esc_html_e( 'Tell us when you’d like to travel, who’s coming, and what you most want to see. We’ll use that to shape your safari.', 'scs-core' ); ?></p>
		<?php echo action_link( planner_url(), __( 'Plan your safari', 'scs-core' ), 'scs-button scs-button--reverse' ); ?>
	</section>
<?php endif; ?>
