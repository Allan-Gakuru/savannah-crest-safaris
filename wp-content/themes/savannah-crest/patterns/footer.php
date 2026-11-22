<?php
/**
 * Title: Site footer
 * Slug: savannah-crest/site-footer
 * Inserter: no
 */
use function SavannahCrest\Theme\page_link;
use function SavannahCrest\Theme\site_mark;
use function SavannahCrest\Theme\menu_term_links;
use const SavannahCrest\Theme\CONTACT_EMAIL;
use const SavannahCrest\Theme\CONTACT_PHONE;
use const SavannahCrest\Theme\CONTACT_PHONE_URL;

defined( 'ABSPATH' ) || exit;
$catalogue = (string) get_post_type_archive_link( 'scs_safari' );
$planner = page_link( 'plan-your-safari' );
$enquiry = page_link( 'enquire' ) ?: $planner;
$about = page_link( 'about' );
$destinations = array();
$experiences = array();
if ( $catalogue && function_exists( 'SavannahCrest\Core\inventory_terms' ) && function_exists( 'SavannahCrest\Core\browse_term_url' ) ) {
	$inventory = SavannahCrest\Core\inventory_terms();
	$destinations = menu_term_links( array_slice( $inventory['where'], 0, 8 ), 'where', $catalogue );
	$experiences = menu_term_links( array_slice( $inventory['experience'], 0, 8 ), 'experience', $catalogue );
}
$quick_links = array_filter( array(
	__( 'All safaris', 'savannah-crest' ) => $catalogue,
	__( 'About us', 'savannah-crest' ) => $about,
	__( 'Traveller stories', 'savannah-crest' ) => page_link( 'traveller-stories' ),
	__( 'Travel guide', 'savannah-crest' ) => page_link( 'guides' ),
	__( 'Safari planning FAQs', 'savannah-crest' ) => $about ? $about . '#safari-faqs' : '',
	__( 'Plan your safari', 'savannah-crest' ) => $planner,
) );
$policies = array_filter( array(
	__( 'Privacy policy', 'savannah-crest' ) => get_privacy_policy_url() ?: page_link( 'privacy' ),
	__( 'Booking terms', 'savannah-crest' ) => page_link( 'booking-terms' ),
) );
?>
<!-- wp:group {"className":"scs-site-footer","layout":{"type":"default"}} -->
<div class="wp-block-group scs-site-footer">
	<!-- wp:html -->
	<div class="scs-shell scs-footer-shell">
		<?php if ( $planner ) : ?>
			<div class="scs-footer-invitation">
				<div>
					<h2><?php esc_html_e( 'Your safari starts here.', 'savannah-crest' ); ?></h2>
					<p><?php esc_html_e( 'Tell us what you have in mind and let us help you plan your journey.', 'savannah-crest' ); ?></p>
				</div>
				<a class="scs-footer-invitation__button" href="<?php echo esc_url( $planner ); ?>"><?php esc_html_e( 'Plan your safari', 'savannah-crest' ); ?><span aria-hidden="true"> &rarr;</span></a>
			</div>
		<?php endif; ?>
		<div class="scs-footer-inner">
			<div class="scs-footer-brand">
				<?php site_mark( true ); ?>
				<?php if ( $enquiry ) : ?><a class="scs-footer-enquire" href="<?php echo esc_url( $enquiry ); ?>"><?php esc_html_e( 'Enquire', 'savannah-crest' ); ?></a><?php endif; ?>
				<div class="scs-footer-contact">
					<h2><?php esc_html_e( 'Get in touch', 'savannah-crest' ); ?></h2>
					<a href="<?php echo esc_url( CONTACT_PHONE_URL ); ?>"><?php echo esc_html( CONTACT_PHONE ); ?></a>
					<a href="<?php echo esc_url( 'mailto:' . CONTACT_EMAIL ); ?>"><?php echo esc_html( CONTACT_EMAIL ); ?></a>
				</div>
			</div>
			<?php foreach ( array(
				__( 'Destinations', 'savannah-crest' ) => $destinations,
				__( 'Safari experiences', 'savannah-crest' ) => $experiences,
				__( 'Quick links', 'savannah-crest' ) => $quick_links,
			) as $heading => $items ) : ?>
				<?php if ( $items ) : ?>
					<nav class="scs-footer-nav" aria-label="<?php echo esc_attr( $heading ); ?>">
						<h2><?php echo esc_html( $heading ); ?></h2>
						<ul role="list"><?php foreach ( $items as $name => $url ) : ?>
							<li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a></li>
						<?php endforeach; ?></ul>
					</nav>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<div class="scs-footer-bottom">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php esc_html_e( 'Savannah Crest Safaris', 'savannah-crest' ); ?></p>
			<?php if ( $policies ) : ?><nav aria-label="<?php esc_attr_e( 'Policies', 'savannah-crest' ); ?>"><ul role="list"><?php foreach ( $policies as $name => $url ) : ?><li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a></li><?php endforeach; ?></ul></nav><?php endif; ?>
		</div>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
