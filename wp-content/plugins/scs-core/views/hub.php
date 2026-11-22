<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

$term = get_queried_object();
if ( ! $term instanceof \WP_Term || ! in_array( $term->taxonomy, array( DESTINATION, EXPERIENCE ), true ) ) {
	return;
}
$guide = destination_guide( $term );
if ( $guide ) {
	require __DIR__ . '/destination-guide.php';
	return;
}
$is_destination = DESTINATION === $term->taxonomy;
$parent = $is_destination && $term->parent ? get_term( $term->parent, DESTINATION ) : null;
$parent_url = $parent instanceof \WP_Term && hub_ready( $parent ) ? get_term_link( $parent ) : '';
$index = page_url( $is_destination ? 'destinations' : 'experiences' );
$banner = image( term_field( 'scs_term_hero', $term ), 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) );
$intro = text( term_field( 'scs_term_intro', $term ) );
$picks = ids( term_field( 'scs_term_featured_trips', $term ) );
$safaris = matching_safaris( array( term_clause( $term ) ), $picks );
$places = $is_destination ? ready_terms( DESTINATION, array( 'parent' => $term->term_id ) ) : array();
$body = text( term_field( 'scs_term_body', $term ) );
$seasons = $is_destination ? rows( term_field( 'scs_term_seasons', $term ) ) : array();
$faqs = rows( term_field( 'scs_term_faqs', $term ) );
$guide_ids = ids( term_field( 'scs_term_guides', $term ) );
$guides = $guide_ids
	? public_posts( 'post', array( 'post__in' => $guide_ids, 'orderby' => 'post__in' ) )
	: public_posts( 'post', array( 'tax_query' => array( term_clause( $term ) ) ) );
$stays = $is_destination ? public_posts( STAY, array( 'tax_query' => array( term_clause( $term ) ) ) ) : array();
$plan = hub_ready( $term ) ? planner_url( $is_destination ? 'destination' : 'experience', $term->slug ) : planner_url();
?>
<article class="scs-shell">
	<nav class="scs-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'scs-core' ); ?>">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'scs-core' ); ?></a>
		<?php echo action_link( $index, $is_destination ? __( 'Destinations', 'scs-core' ) : __( 'Experiences', 'scs-core' ), 'scs-text-link' ); ?>
		<?php if ( $parent_url && ! is_wp_error( $parent_url ) ) : ?><a href="<?php echo esc_url( $parent_url ); ?>"><?php echo esc_html( $parent->name ); ?></a><?php endif; ?>
	</nav>
	<header class="scs-trip-hero<?php echo $banner ? ' scs-trip-hero--photo' : ''; ?>">
		<div class="scs-trip-hero__copy">
			<h1><?php echo esc_html( $term->name ); ?></h1>
			<?php if ( $intro ) : ?><p><?php echo esc_html( $intro ); ?></p>
			<?php elseif ( $term->description ) : ?><div><?php echo wp_kses_post( wpautop( $term->description ) ); ?></div><?php endif; ?>
			<?php echo action_link( $plan, __( 'Plan your safari', 'scs-core' ) ); ?>
		</div>
		<?php if ( $banner ) : ?><figure class="scs-trip-hero__image"><?php echo $banner; // WordPress attachment markup. ?></figure><?php endif; ?>
	</header>
	<?php if ( $safaris ) : ?>
		<section class="scs-section">
			<h2><?php esc_html_e( 'Matching safaris', 'scs-core' ); ?></h2>
			<?php cards( $safaris ); ?>
		</section>
	<?php endif; ?>
	<?php if ( $places ) : ?><section class="scs-section"><h2><?php esc_html_e( 'Places to explore', 'scs-core' ); ?></h2><?php term_items( $places ); ?></section><?php endif; ?>
	<?php if ( $body || $seasons ) : ?>
		<section class="scs-section scs-prose">
			<h2><?php esc_html_e( 'Planning your visit', 'scs-core' ); ?></h2>
			<?php echo wp_kses_post( $body ); ?>
			<?php foreach ( $seasons as $season ) : ?>
				<?php if ( ! empty( $season['short_label'] ) ) : ?><h3><?php echo esc_html( text( $season['short_label'] ) ); ?></h3><?php endif; ?>
				<?php $months = array_filter( array_map( __NAMESPACE__ . '\text', (array) ( $season['months'] ?? array() ) ) ); ?>
				<?php if ( $months ) : ?><p><?php echo esc_html( implode( ', ', $months ) ); ?></p><?php endif; ?>
				<?php if ( ! empty( $season['conditions'] ) ) : ?><p><?php echo esc_html( text( $season['conditions'] ) ); ?></p><?php endif; ?>
			<?php endforeach; ?>
		</section>
	<?php endif; ?>
	<?php if ( $faqs ) : ?>
		<section class="scs-section scs-accordion">
			<h2><?php esc_html_e( 'Frequently asked questions', 'scs-core' ); ?></h2>
			<?php foreach ( $faqs as $faq ) : ?>
				<?php if ( ! empty( $faq['question'] ) && ! empty( $faq['answer'] ) ) : ?>
					<details><summary><?php echo esc_html( text( $faq['question'] ) ); ?></summary><div class="scs-accordion__body"><?php echo wpautop( esc_html( text( $faq['answer'] ) ) ); ?></div></details>
				<?php endif; ?>
			<?php endforeach; ?>
		</section>
	<?php endif; ?>
	<?php if ( $stays ) : ?><section class="scs-section"><h2><?php esc_html_e( 'Places to stay', 'scs-core' ); ?></h2><?php guide_links( $stays ); ?></section><?php endif; ?>
	<?php if ( $guides ) : ?><section class="scs-section"><h2><?php esc_html_e( 'Travel guides', 'scs-core' ); ?></h2><?php guide_links( $guides ); ?></section><?php endif; ?>
</article>
