<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

$settings = configured_form();
$context = active_context();
?>
<section class="scs-planner" data-scs-planner data-scs-context="<?php echo esc_attr( $context['kind'] ?? 'general' ); ?>" data-scs-slug="<?php echo esc_attr( $context['slug'] ?? '' ); ?>">
	<?php if ( $context ) : ?><p class="scs-context"><?php echo esc_html( sprintf( __( 'Planning around %s', 'scs-core' ), $context['title'] ) ); ?></p><?php endif; ?>
	<?php if ( $settings && shortcode_exists( 'forminator_form' ) ) : ?>
		<?php echo do_shortcode( '[forminator_form id="' . (int) $settings['id'] . '"]' ); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Online enquiries are currently unavailable.', 'scs-core' ); ?></p>
	<?php endif; ?>
</section>
