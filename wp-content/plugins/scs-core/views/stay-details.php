<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

$stay = block_post( $block, STAY );
if ( ! $stay ) {
	return;
}
$facilities = bullet_labels( field( 'scs_stay_facilities', $stay->ID ) );
$transfer = text( field( 'scs_stay_transfer', $stay->ID ) );
$access = text( field( 'scs_stay_access_notes', $stay->ID ) );
$gallery = ids( field( 'scs_stay_gallery', $stay->ID ) );
$candidates = public_posts( SAFARI, array( 'posts_per_page' => 75 ) );
$trips = array();
foreach ( $candidates as $candidate ) {
	foreach ( rows( field( 'scs_itinerary', $candidate->ID ) ) as $day ) {
		if ( (int) $stay->ID === positive_int( $day['stay'] ?? null ) ) {
			$trips[] = $candidate;
			break;
		}
	}
	if ( count( $trips ) >= 3 ) {
		break;
	}
}
?>
<div class="scs-shell">
	<?php if ( $facilities || $transfer || $access ) : ?>
		<section class="scs-section scs-prose">
			<h2><?php esc_html_e( 'About the stay', 'scs-core' ); ?></h2>
			<?php bulleted_section( __( 'Facilities', 'scs-core' ), $facilities ); ?>
			<?php if ( $transfer ) : ?><h3><?php esc_html_e( 'Getting there', 'scs-core' ); ?></h3><p><?php echo esc_html( $transfer ); ?></p><?php endif; ?>
			<?php if ( $access ) : ?><h3><?php esc_html_e( 'Accessibility', 'scs-core' ); ?></h3><p><?php echo esc_html( $access ); ?></p><?php endif; ?>
		</section>
	<?php endif; ?>
	<?php photos( $gallery ); ?>
	<?php if ( $trips ) : ?><section class="scs-section"><h2><?php esc_html_e( 'Safaris featuring this stay', 'scs-core' ); ?></h2><?php cards( $trips ); ?></section><?php endif; ?>
	<div class="scs-section"><?php echo action_link( planner_url(), __( 'Plan your safari', 'scs-core' ) ); ?></div>
</div>
