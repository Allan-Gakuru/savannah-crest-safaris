<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

$trip = block_post( $block, SAFARI );
if ( ! $trip ) {
	return;
}
$fields = safari_fields( $trip->ID );
$days = rows( $fields['scs_itinerary'] );
$route = route_for( $fields );
$price = price_label( $fields, wp_date( 'Ymd' ) );
$rate = rate( $fields );
$rate_note = has_valid_rate( $rate ) && $rate['until'] >= wp_date( 'Ymd' ) ? $rate['note'] : '';
$planner = planner_url( 'publish' === $trip->post_status ? 'trip' : '', $trip->post_name );
$primary = get_term( positive_int( $fields['scs_primary_destination'] ), DESTINATION );
$primary_url = $primary instanceof \WP_Term && hub_ready( $primary ) ? get_term_link( $primary ) : '';
$related = ids( $fields['scs_related_safaris'] );
$related = array_values( array_diff( $related, array( $trip->ID ) ) );
$related_posts = $related ? public_posts( SAFARI, array( 'post__in' => $related, 'orderby' => 'post__in' ) ) : array();
$excluded = array_merge( array( $trip->ID ), wp_list_pluck( $related_posts, 'ID' ) );
if ( count( $related_posts ) < 3 && $primary instanceof \WP_Term ) {
	$related_posts = array_merge( $related_posts, matching_safaris( array( term_clause( $primary ) ), array(), 3 - count( $related_posts ), $excluded ) );
}
$experiences = get_the_terms( $trip, EXPERIENCE );
if ( is_array( $experiences ) && count( $related_posts ) < 3 ) {
	$experience_query = array_merge( array( 'relation' => 'OR' ), array_map( __NAMESPACE__ . '\term_clause', $experiences ) );
	$related_posts = array_merge( $related_posts, matching_safaris( $experience_query, array(), 3 - count( $related_posts ), array_merge( $excluded, wp_list_pluck( $related_posts, 'ID' ) ) ) );
}
$best_time = rows( $fields['scs_best_time'] );
$includes = bullet_labels( $fields['scs_includes'] );
$excludes = bullet_labels( $fields['scs_excludes'] );
$highlights = bullet_labels( $fields['scs_highlights'] );
$hero = image( get_post_thumbnail_id( $trip ), 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) );
$duration = duration_label( $fields['scs_duration_days'] );
?>
<article class="scs-shell scs-trip"<?php if ( 'publish' === $trip->post_status ) : ?> data-scs-trip="<?php echo esc_attr( $trip->post_name ); ?>"<?php endif; ?>>
	<nav class="scs-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'scs-core' ); ?>">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'scs-core' ); ?></a>
		<a href="<?php echo esc_url( catalogue_url() ); ?>"><?php esc_html_e( 'Safaris', 'scs-core' ); ?></a>
		<?php if ( $primary_url && ! is_wp_error( $primary_url ) ) : ?><a href="<?php echo esc_url( $primary_url ); ?>"><?php echo esc_html( $primary->name ); ?></a><?php endif; ?>
	</nav>
	<header class="scs-trip-hero<?php echo $hero ? ' scs-trip-hero--photo' : ''; ?>">
		<div class="scs-trip-hero__copy">
			<?php if ( $route ) : ?><p class="scs-eyeline"><?php echo esc_html( $route ); ?></p><?php endif; ?>
			<h1><?php echo esc_html( get_the_title( $trip ) ); ?></h1>
			<?php if ( $trip->post_excerpt ) : ?><p><?php echo esc_html( wp_strip_all_tags( $trip->post_excerpt ) ); ?></p><?php endif; ?>
			<dl class="scs-facts">
				<?php if ( $duration ) : ?><div><dt><?php esc_html_e( 'Duration', 'scs-core' ); ?></dt><dd><?php echo esc_html( $duration ); ?></dd></div><?php endif; ?>
				<?php if ( $fields['scs_start_location'] ) : ?><div><dt><?php esc_html_e( 'Starts in', 'scs-core' ); ?></dt><dd><?php echo esc_html( $fields['scs_start_location'] ); ?></dd></div><?php endif; ?>
				<?php if ( $fields['scs_end_location'] ) : ?><div><dt><?php esc_html_e( 'Ends in', 'scs-core' ); ?></dt><dd><?php echo esc_html( $fields['scs_end_location'] ); ?></dd></div><?php endif; ?>
			</dl>
			<p class="scs-price"><?php echo esc_html( $price ); ?></p>
			<?php if ( $rate_note ) : ?><p class="scs-rate-note"><?php echo esc_html( $rate_note ); ?></p><?php endif; ?>
			<?php echo action_link( $planner, __( 'Enquire about this safari', 'scs-core' ), 'scs-button', 'itinerary' ); ?>
		</div>
		<?php if ( $hero ) : ?><figure class="scs-trip-hero__image"><?php echo $hero; // WordPress attachment markup. ?></figure><?php endif; ?>
	</header>
	<?php if ( $days || $includes || $excludes ) : ?>
		<nav class="scs-jump" aria-label="<?php esc_attr_e( 'On this page', 'scs-core' ); ?>">
			<?php if ( $days ) : ?><a href="#itinerary"><?php esc_html_e( 'Itinerary', 'scs-core' ); ?></a><?php endif; ?>
			<?php if ( $includes || $excludes ) : ?><a href="#price-and-inclusions"><?php esc_html_e( 'Price and inclusions', 'scs-core' ); ?></a><?php endif; ?>
			<?php if ( $best_time ) : ?><a href="#when-to-go"><?php esc_html_e( 'When to go', 'scs-core' ); ?></a><?php endif; ?>
		</nav>
	<?php endif; ?>
	<?php if ( $trip->post_content || $highlights ) : ?>
		<section class="scs-section scs-prose">
			<h2><?php esc_html_e( 'Safari overview', 'scs-core' ); ?></h2>
			<?php echo apply_filters( 'the_content', get_the_content( null, false, $trip ) ); ?>
			<?php bulleted_section( __( 'Highlights', 'scs-core' ), $highlights ); ?>
		</section>
	<?php endif; ?>
	<?php if ( $days ) : ?>
		<section class="scs-section" id="itinerary">
			<h2><?php esc_html_e( 'Your itinerary', 'scs-core' ); ?></h2>
			<div class="scs-accordion">
				<?php foreach ( $days as $index => $day ) : ?>
					<?php
					$from = positive_int( $day['day_from'] ?? null );
					$to   = positive_int( $day['day_to'] ?? null );
					$label = $from ? sprintf( __( 'Day %s', 'scs-core' ), $from ) : __( 'Itinerary stop', 'scs-core' );
					if ( $from && $to > $from ) {
						$label = sprintf( __( 'Days %1$s–%2$s', 'scs-core' ), $from, $to );
					}
					$stay = ! empty( $day['stay'] ) ? get_post( positive_int( $day['stay'] ) ) : null;
					$public_stay = $stay instanceof \WP_Post && STAY === $stay->post_type && 'publish' === $stay->post_status && '' === $stay->post_password;
					$destination = term_label( $day['destination'] ?? 0 );
					?>
					<details<?php echo 0 === $index ? ' open' : ''; ?>>
						<summary><span><?php echo esc_html( $label ); ?></span> <?php echo esc_html( text( $day['heading'] ?? '' ) ); ?></summary>
						<div class="scs-accordion__body">
							<?php if ( $destination ) : ?><p class="scs-eyeline"><?php echo esc_html( $destination ); ?></p><?php endif; ?>
							<?php echo wp_kses_post( text( $day['narrative'] ?? '' ) ); ?>
							<?php if ( $public_stay || ! empty( $day['stay_label'] ) ) : ?>
								<p><strong><?php esc_html_e( 'Stay', 'scs-core' ); ?></strong> <?php
								echo $public_stay ? action_link( get_permalink( $stay ), get_the_title( $stay ), 'scs-text-link' ) : esc_html( text( $day['stay_label'] ) );
								?></p>
							<?php endif; ?>
							<?php if ( ! empty( $day['meals'] ) ) : ?><p><strong><?php esc_html_e( 'Meals', 'scs-core' ); ?></strong> <?php echo esc_html( text( $day['meals'] ) ); ?></p><?php endif; ?>
							<?php if ( ! empty( $day['transfer_note'] ) ) : ?><p><strong><?php esc_html_e( 'Transfer', 'scs-core' ); ?></strong> <?php echo esc_html( text( $day['transfer_note'] ) ); ?></p><?php endif; ?>
							<?php echo image( $day['image'] ?? 0, 'large', array( 'loading' => 'lazy' ) ); ?>
						</div>
					</details>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>
	<?php if ( $includes || $excludes ) : ?>
		<section class="scs-section" id="price-and-inclusions">
			<h2><?php esc_html_e( 'Price and inclusions', 'scs-core' ); ?></h2>
			<p class="scs-price"><?php echo esc_html( $price ); ?></p>
			<?php if ( $rate_note ) : ?><p><?php echo esc_html( $rate_note ); ?></p><?php endif; ?>
			<div class="scs-two-col">
				<?php bulleted_section( __( 'What’s included', 'scs-core' ), $includes ); ?>
				<?php bulleted_section( __( 'What’s extra', 'scs-core' ), $excludes ); ?>
			</div>
		</section>
	<?php endif; ?>
	<?php if ( $best_time ) : ?>
		<section class="scs-section scs-prose" id="when-to-go">
			<h2><?php esc_html_e( 'When to go', 'scs-core' ); ?></h2>
			<?php foreach ( $best_time as $period ) : ?>
				<?php $months = array_filter( array_map( __NAMESPACE__ . '\text', (array) ( $period['months'] ?? array() ) ) ); ?>
				<?php if ( $months ) : ?><h3><?php echo esc_html( implode( ', ', $months ) ); ?></h3><?php endif; ?>
				<?php if ( ! empty( $period['note'] ) ) : ?><p><?php echo esc_html( text( $period['note'] ) ); ?></p><?php endif; ?>
			<?php endforeach; ?>
		</section>
	<?php endif; ?>
	<?php photos( $fields['scs_gallery'] ); ?>
	<?php if ( $related_posts ) : ?>
		<section class="scs-section">
			<h2><?php esc_html_e( 'Related safaris', 'scs-core' ); ?></h2>
			<?php cards( $related_posts ); ?>
		</section>
	<?php endif; ?>
	<?php if ( $planner ) : ?>
		<div class="scs-section scs-trip-cta">
			<?php echo action_link( $planner, __( 'Enquire about this safari', 'scs-core' ), 'scs-button', 'itinerary' ); ?>
		</div>
	<?php endif; ?>
</article>
