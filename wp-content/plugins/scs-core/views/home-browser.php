<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

$front = (int) get_option( 'page_on_front' );
$safaris = matching_safaris( array(), $front ? ids( field( 'scs_home_featured_trips', $front ) ) : array(), 3 );
$places = ready_terms( DESTINATION, array( 'parent' => 0 ), $front ? ids( field( 'scs_home_featured_places', $front ) ) : array(), 4 );
$experiences = ready_terms( EXPERIENCE, array(), array(), 4 );
?>
<section class="scs-shell scs-section scs-finder" aria-labelledby="scs-find-title">
	<h2 id="scs-find-title"><?php esc_html_e( 'Find your safari', 'scs-core' ); ?></h2>
	<?php filter_form( array(), true ); ?>
</section>
<?php if ( $safaris ) : ?>
	<section class="scs-shell scs-section">
		<div class="scs-section-head">
			<h2><?php esc_html_e( 'Explore our safaris', 'scs-core' ); ?></h2>
			<?php echo action_link( catalogue_url(), __( 'See all safaris', 'scs-core' ), 'scs-text-link' ); ?>
		</div>
		<?php cards( $safaris ); ?>
	</section>
<?php endif; ?>
<?php if ( $places ) : ?>
	<section class="scs-shell scs-section">
		<h2><?php esc_html_e( 'Where to go', 'scs-core' ); ?></h2>
		<?php term_items( $places ); ?>
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
