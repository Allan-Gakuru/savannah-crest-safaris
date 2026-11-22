<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

$countries = country_guides();
if ( ! $countries ) {
	return;
}
$directory = page_url( 'destinations' );
?>
<section class="scs-shell scs-section scs-home-countries" aria-labelledby="scs-home-countries-title">
	<header class="scs-home-countries__intro">
		<h2 id="scs-home-countries-title"><?php esc_html_e( 'Luxury Africa Safari Destinations', 'scs-core' ); ?></h2>
		<p><?php esc_html_e( 'Compare the places we travel, from Kenya’s open plains to the forests of Uganda and the waterways of Botswana.', 'scs-core' ); ?></p>
	</header>
	<ul class="scs-country-grid" role="list">
		<?php foreach ( $countries as $term ) : ?>
			<?php
			$guide = destination_guides()[ $term->slug ];
			$url = browse_term_url( $term, 'where', catalogue_url() );
			$photo = destination_guide_image( $term, $guide, true );
			if ( ! $url ) {
				continue;
			}
			?>
			<li class="scs-country-card">
				<?php if ( $photo ) : ?><div class="scs-country-card__media"><?php echo $photo; // Escaped by destination_guide_image(). ?></div><?php endif; ?>
				<div class="scs-country-card__body">
					<h3><?php echo esc_html( $term->name ); ?></h3>
					<p><?php echo esc_html( $guide['teaser'] ); ?></p>
					<a class="scs-text-link" href="<?php echo esc_url( $url ); ?>">
						<?php printf( esc_html__( 'Explore %s', 'scs-core' ), esc_html( $term->name ) ); ?> <span aria-hidden="true">→</span>
					</a>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ( $directory ) : ?>
		<div class="scs-home-countries__all"><?php echo action_link( $directory, __( 'View all destinations', 'scs-core' ), 'scs-button scs-button--outline' ); ?></div>
	<?php endif; ?>
</section>
