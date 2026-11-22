<?php
/**
 * Title: Site footer
 * Slug: savannah-crest/site-footer
 * Inserter: no
 */
use function SavannahCrest\Theme\links;
use function SavannahCrest\Theme\page_link;
use function SavannahCrest\Theme\site_mark;
use const SavannahCrest\Theme\CONTACT_EMAIL;
use const SavannahCrest\Theme\CONTACT_PHONE;
use const SavannahCrest\Theme\CONTACT_PHONE_URL;

defined( 'ABSPATH' ) || exit;
$policies = array_filter( array(
	__( 'Privacy', 'savannah-crest' ) => get_privacy_policy_url() ?: page_link( 'privacy' ),
	__( 'Booking terms', 'savannah-crest' ) => page_link( 'booking-terms' ),
) );
?>
<!-- wp:group {"className":"scs-site-footer","layout":{"type":"default"}} -->
<div class="wp-block-group scs-site-footer">
	<!-- wp:html -->
	<div class="scs-shell scs-footer-inner">
		<div class="scs-footer-brand">
			<?php site_mark( true ); ?>
			<ul role="list">
				<li><a href="<?php echo esc_url( CONTACT_PHONE_URL ); ?>"><?php echo esc_html( CONTACT_PHONE ); ?></a></li>
				<li><a href="<?php echo esc_url( 'mailto:' . CONTACT_EMAIL ); ?>"><?php echo esc_html( CONTACT_EMAIL ); ?></a></li>
			</ul>
		</div>
		<?php if ( links() ) : ?>
			<nav aria-label="<?php esc_attr_e( 'Explore', 'savannah-crest' ); ?>">
				<ul role="list"><?php foreach ( links() as $name => $url ) : ?>
					<li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a></li>
				<?php endforeach; ?></ul>
			</nav>
		<?php endif; ?>
		<?php if ( $policies || page_link( 'plan-your-safari' ) ) : ?>
			<nav aria-label="<?php esc_attr_e( 'Planning and policies', 'savannah-crest' ); ?>">
				<ul role="list">
					<?php if ( page_link( 'plan-your-safari' ) ) : ?><li><a href="<?php echo esc_url( page_link( 'plan-your-safari' ) ); ?>"><?php esc_html_e( 'Plan your safari', 'savannah-crest' ); ?></a></li><?php endif; ?>
					<?php foreach ( $policies as $name => $url ) : ?><li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a></li><?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
