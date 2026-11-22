<?php
/**
 * Title: Homepage safari planning
 * Slug: savannah-crest/home-planning
 * Inserter: no
 */
use function SavannahCrest\Theme\page_link;

defined( 'ABSPATH' ) || exit;

$about = page_link( 'about' );
$enquiry = page_link( 'enquire' );
$catalogue = (string) get_post_type_archive_link( 'scs_safari' );
?>
<!-- wp:group {"tagName":"section","className":"scs-shell scs-home-experts","layout":{"type":"default"}} -->
<section class="wp-block-group scs-shell scs-home-experts">
	<!-- wp:group {"className":"scs-home-experts__copy","layout":{"type":"default"}} -->
	<div class="wp-block-group scs-home-experts__copy">
		<!-- wp:heading -->
		<h2 class="wp-block-heading"><?php esc_html_e( 'African safaris shaped by local experience', 'savannah-crest' ); ?></h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph -->
		<p><?php esc_html_e( 'Sam, our founder, has worked as a tour operator in East Africa for 15 years. Tell us what draws you to Africa and we’ll help you choose where to go, where to stay and how much time to spend in each place.', 'savannah-crest' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:group {"className":"scs-home-experts__actions","layout":{"type":"default"}} -->
		<div class="wp-block-group scs-home-experts__actions">
			<!-- wp:scs/site-action {"action":"plan"} /-->
			<?php if ( $about ) : ?>
				<!-- wp:html -->
				<a class="scs-text-link" href="<?php echo esc_url( $about . '#meet-sam' ); ?>"><?php esc_html_e( 'Meet Sam', 'savannah-crest' ); ?></a>
				<!-- /wp:html -->
			<?php endif; ?>
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
	<!-- wp:pattern {"slug":"savannah-crest/founder-photo"} /-->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"scs-shell scs-home-process","layout":{"type":"default"}} -->
<section class="wp-block-group scs-shell scs-home-process">
	<!-- wp:heading -->
	<h2 class="wp-block-heading"><?php esc_html_e( 'How it works', 'savannah-crest' ); ?></h2>
	<!-- /wp:heading -->
	<!-- wp:html -->
	<ol class="scs-home-process__steps" role="list">
		<li>
			<span class="scs-home-process__number" aria-hidden="true">01</span>
			<h3><?php esc_html_e( 'Find your inspiration', 'savannah-crest' ); ?></h3>
			<p><?php esc_html_e( 'Browse our itineraries to get a feel for the places, wildlife and pace that suit you.', 'savannah-crest' ); ?></p>
			<?php if ( $catalogue ) : ?>
				<a class="scs-text-link" href="<?php echo esc_url( $catalogue ); ?>"><?php esc_html_e( 'Browse safaris', 'savannah-crest' ); ?></a>
			<?php endif; ?>
		</li>
		<li>
			<span class="scs-home-process__number" aria-hidden="true">02</span>
			<h3><?php esc_html_e( 'Tell us what matters', 'savannah-crest' ); ?></h3>
			<p><?php esc_html_e( 'Share who’s coming, when you’d like to travel and what you hope to see. It’s fine if your dates or destination are still open.', 'savannah-crest' ); ?></p>
			<?php if ( $enquiry ) : ?>
				<a class="scs-text-link" href="<?php echo esc_url( $enquiry ); ?>" data-scs-enquiry="home-how-it-works"><?php esc_html_e( 'Send an enquiry', 'savannah-crest' ); ?></a>
			<?php endif; ?>
		</li>
		<li>
			<span class="scs-home-process__number" aria-hidden="true">03</span>
			<h3><?php esc_html_e( 'Review your itinerary', 'savannah-crest' ); ?></h3>
			<p><?php esc_html_e( 'We’ll suggest a route and talk through the stays, transfers and costs with you before you decide to book.', 'savannah-crest' ); ?></p>
		</li>
	</ol>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
