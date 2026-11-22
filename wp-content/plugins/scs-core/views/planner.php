<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

$settings = configured_form();
$has_form = $settings && shortcode_exists( 'forminator_form' );
if ( 'enquiry' === ( $attributes['mode'] ?? '' ) ) {
	require __DIR__ . '/enquiry.php';
	return;
}
$context = active_context();
?>
<section class="scs-planner" data-scs-planner data-scs-context="<?php echo esc_attr( $context['kind'] ?? 'general' ); ?>" data-scs-slug="<?php echo esc_attr( $context['slug'] ?? '' ); ?>">
	<?php if ( $context ) :
		$context_label = 'trip' === $context['kind'] ? __( 'You’re enquiring about %s', 'scs-core' ) : __( 'Planning around %s', 'scs-core' );
		?>
		<p class="scs-context"><?php echo esc_html( sprintf( $context_label, $context['title'] ) ); ?></p>
	<?php endif; ?>
	<?php if ( $has_form ) : ?>
		<?php echo do_shortcode( '[forminator_form id="' . (int) $settings['id'] . '"]' ); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Online enquiries are currently unavailable.', 'scs-core' ); ?></p>
	<?php endif; ?>
</section>
