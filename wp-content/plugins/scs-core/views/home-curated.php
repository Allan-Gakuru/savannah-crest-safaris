<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

$curated_cards = array();
$tour_catalogue = catalogue_url();
foreach ( curated_destination_guides() as $slug => $guide ) {
	$term = get_term_by( 'slug', $slug, DESTINATION );
	if ( ! $term instanceof \WP_Term ) {
		continue;
	}
	$url = browse_term_url( $term, 'where', $tour_catalogue );
	if ( $url ) {
		$curated_cards[] = array( $term, $guide, $url );
	}
}
if ( ! $curated_cards ) {
	return;
}
?>
<section class="scs-shell scs-section scs-home-curated" aria-labelledby="scs-home-curated-title">
	<header class="scs-home-curated__intro">
		<h2 id="scs-home-curated-title"><?php esc_html_e( 'Curated African Safaris and Tours', 'scs-core' ); ?></h2>
		<p><?php esc_html_e( 'Start with a place you’ve always wanted to see. Explore the wildlife, seasons and sample itineraries to find a route that suits you.', 'scs-core' ); ?></p>
	</header>
	<ul class="scs-curated-grid" role="list">
		<?php foreach ( $curated_cards as list( $term, $guide, $url ) ) : ?>
			<li>
				<a class="scs-curated-card" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Explore guide to %s', 'scs-core' ), $guide['name'] ) ); ?>">
					<div class="scs-curated-card__media">
						<?php echo destination_guide_image( $term, $guide, true ); // Escaped attachment markup. ?>
					</div>
					<div class="scs-curated-card__body">
						<h3><?php echo esc_html( $guide['name'] ); ?></h3>
						<p><?php echo esc_html( $guide['teaser'] ); ?></p>
						<span class="scs-text-link"><?php esc_html_e( 'Explore guide', 'scs-core' ); ?></span>
					</div>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="scs-home-curated__actions">
		<?php echo action_link( $tour_catalogue, __( 'View all safaris', 'scs-core' ), 'scs-button scs-button--outline' ); ?>
		<?php echo action_link( $enquiry_url, __( 'Help me plan', 'scs-core' ), 'scs-button', 'home-curated' ); ?>
	</div>
</section>
