<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

$picks = ids( term_field( 'scs_term_featured_trips', $term ) );
$safaris = matching_safaris( array( term_clause( $term ) ), $picks, 6 );
$inventory = inventory_terms()['where'];
$directory = page_url( 'destinations' );
$parent = $term->parent ? get_term( $term->parent, DESTINATION ) : ( $inventory[ DESTINATION_REGIONS[ $term->slug ] ?? '' ] ?? null );
$parent_url = $parent instanceof \WP_Term ? browse_term_url( $parent, 'where', catalogue_url() ) : '';
$is_curated = isset( curated_destination_guides()[ $term->slug ] );
$places_heading = $is_curated ? __( 'What to experience', 'scs-core' ) : __( 'Where to go', 'scs-core' );
$catalogue = catalogue_url();
$all_trips = $catalogue ? add_query_arg( 'where', $term->slug, $catalogue ) : '';
$plan = planner_url( 'destination', $term->slug );
$banner = destination_guide_image( $term, $guide );
$intro = text( term_field( 'scs_term_intro', $term ) ) ?: $guide['intro'];
$body = text( term_field( 'scs_term_body', $term ) );
$guide_ids = ids( term_field( 'scs_term_guides', $term ) );
$related_guides = $guide_ids
	? public_posts( 'post', array( 'post__in' => $guide_ids, 'orderby' => 'post__in' ) )
	: public_posts( 'post', array( 'tax_query' => array( term_clause( $term ) ) ) );
$stays = public_posts( STAY, array( 'tax_query' => array( term_clause( $term ) ) ) );
$faqs = $guide['faqs'];
foreach ( rows( term_field( 'scs_term_faqs', $term ) ) as $faq ) {
	if ( ! empty( $faq['question'] ) && ! empty( $faq['answer'] ) ) {
		$faqs[ text( $faq['question'] ) ] = text( $faq['answer'] );
	}
}
?>
<article class="scs-shell scs-country-guide">
	<nav class="scs-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'scs-core' ); ?>">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'scs-core' ); ?></a>
		<?php echo action_link( $directory, __( 'Destinations', 'scs-core' ), 'scs-text-link' ); ?>
		<?php if ( $parent_url ) : ?><a href="<?php echo esc_url( $parent_url ); ?>"><?php echo esc_html( $parent->name ); ?></a><?php endif; ?>
	</nav>

	<header class="scs-country-guide__hero<?php echo $banner ? ' scs-country-guide__hero--photo' : ''; ?>">
		<div class="scs-country-guide__intro">
			<h1><?php echo esc_html( $guide['title'] ); ?></h1>
			<p><?php echo esc_html( $intro ); ?></p>
			<div class="scs-country-guide__actions">
				<?php echo action_link( $plan, __( 'Plan your safari', 'scs-core' ), 'scs-button', 'destination-guide' ); ?>
				<?php if ( $safaris ) : ?><a class="scs-button scs-button--outline" href="#destination-trips"><?php esc_html_e( 'View itineraries', 'scs-core' ); ?></a><?php endif; ?>
			</div>
		</div>
		<?php if ( $banner ) : ?><figure class="scs-country-guide__media"><?php echo $banner; // Escaped by destination_guide_image(). ?></figure><?php endif; ?>
	</header>

	<nav class="scs-country-guide__jump" aria-label="<?php esc_attr_e( 'In this guide', 'scs-core' ); ?>">
		<ul role="list">
			<?php if ( $safaris ) : ?><li><a href="#destination-trips"><?php esc_html_e( 'Itineraries', 'scs-core' ); ?></a></li><?php endif; ?>
			<li><a href="#destination-places"><?php echo esc_html( $places_heading ); ?></a></li>
			<li><a href="#destination-timing"><?php esc_html_e( 'When to go', 'scs-core' ); ?></a></li>
			<li><a href="#destination-planning"><?php esc_html_e( 'Planning tips', 'scs-core' ); ?></a></li>
			<li><a href="#destination-faqs"><?php esc_html_e( 'FAQs', 'scs-core' ); ?></a></li>
		</ul>
	</nav>

	<?php if ( $safaris ) : ?>
		<section id="destination-trips" class="scs-section">
			<div class="scs-section-head">
				<h2><?php esc_html_e( 'Itineraries to start with', 'scs-core' ); ?></h2>
				<?php echo action_link( $all_trips, __( 'See all matching trips', 'scs-core' ), 'scs-text-link' ); ?>
			</div>
			<?php cards( $safaris ); ?>
		</section>
	<?php elseif ( $is_curated && $plan ) : ?>
		<section class="scs-section">
			<h2><?php printf( esc_html__( 'Plan a trip to %s', 'scs-core' ), esc_html( $guide['name'] ) ); ?></h2>
			<p class="scs-prose"><?php esc_html_e( 'Share your dates and the other places you’d like to visit. We’ll help you work this destination into a safari route.', 'scs-core' ); ?></p>
			<?php echo action_link( $plan, __( 'Plan a custom trip', 'scs-core' ), 'scs-button', 'destination-custom-trip' ); ?>
		</section>
	<?php endif; ?>

	<section id="destination-places" class="scs-section">
		<h2><?php echo esc_html( $places_heading ); ?></h2>
		<dl class="scs-country-guide__notes">
			<?php foreach ( $guide['places'] as $slug => list( $name, $description ) ) : ?>
				<?php $url = isset( $inventory[ $slug ] ) ? browse_term_url( $inventory[ $slug ], 'where', $catalogue ) : ''; ?>
				<div>
					<dt><?php if ( $url ) : ?><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a><?php else : echo esc_html( $name ); endif; ?></dt>
					<dd><?php echo esc_html( $description ); ?></dd>
				</div>
			<?php endforeach; ?>
		</dl>
	</section>

	<section id="destination-timing" class="scs-section">
		<h2><?php esc_html_e( 'When to go', 'scs-core' ); ?></h2>
		<?php $seasons = rows( term_field( 'scs_term_seasons', $term ) ); ?>
		<dl class="scs-country-guide__notes">
			<?php foreach ( $guide['timing'] as $period => $description ) : ?>
				<div><dt><?php echo esc_html( $period ); ?></dt><dd><?php echo esc_html( $description ); ?></dd></div>
			<?php endforeach; ?>
			<?php foreach ( $seasons as $season ) : ?>
				<?php if ( ! empty( $season['short_label'] ) && ! empty( $season['conditions'] ) ) : ?>
					<div><dt><?php echo esc_html( text( $season['short_label'] ) ); ?></dt><dd><?php echo esc_html( text( $season['conditions'] ) ); ?></dd></div>
				<?php endif; ?>
			<?php endforeach; ?>
		</dl>
	</section>

	<section id="destination-planning" class="scs-section">
		<h2><?php esc_html_e( 'Planning your trip', 'scs-core' ); ?></h2>
		<?php if ( $body ) : ?><div class="scs-prose"><?php echo wp_kses_post( $body ); ?></div><?php endif; ?>
		<dl class="scs-country-guide__notes">
			<?php foreach ( $guide['practical'] as $tip => $description ) : ?>
				<div><dt><?php echo esc_html( $tip ); ?></dt><dd><?php echo esc_html( $description ); ?></dd></div>
			<?php endforeach; ?>
		</dl>
	</section>

	<section id="destination-faqs" class="scs-section scs-accordion">
		<h2><?php esc_html_e( 'Frequently asked questions', 'scs-core' ); ?></h2>
		<?php foreach ( $faqs as $question => $answer ) : ?>
			<details>
				<summary><?php echo esc_html( $question ); ?></summary>
				<div class="scs-accordion__body"><p><?php echo esc_html( $answer ); ?></p></div>
			</details>
		<?php endforeach; ?>
	</section>

	<?php if ( $stays ) : ?>
		<section class="scs-section">
			<h2><?php esc_html_e( 'Places to stay', 'scs-core' ); ?></h2>
			<?php guide_links( $stays ); ?>
		</section>
	<?php endif; ?>
	<?php if ( $related_guides ) : ?>
		<section class="scs-section">
			<h2><?php esc_html_e( 'Travel guides', 'scs-core' ); ?></h2>
			<?php guide_links( $related_guides ); ?>
		</section>
	<?php endif; ?>

	<?php if ( $plan ) : ?>
		<section class="scs-section scs-invitation">
			<h2><?php esc_html_e( 'Let’s plan your route', 'scs-core' ); ?></h2>
			<p><?php esc_html_e( 'Tell us when you’d like to go and what you want to experience. We’ll help you choose where to spend your time.', 'scs-core' ); ?></p>
			<?php echo action_link( $plan, __( 'Plan your safari', 'scs-core' ), 'scs-button scs-button--reverse', 'destination-guide' ); ?>
		</section>
	<?php endif; ?>
</article>
