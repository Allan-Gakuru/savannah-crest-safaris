<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;
?>
<div class="scs-shell scs-enquiry"<?php if ( $has_form ) : ?> data-scs-enquiry-flow<?php endif; ?>>
	<section id="enquiry-start" class="scs-enquiry__step" aria-labelledby="enquiry-title">
		<?php if ( $has_form ) : ?>
			<p class="scs-enquiry__progress"><span><?php esc_html_e( 'Step 1 of 2', 'scs-core' ); ?></span><span><?php esc_html_e( 'Introduction', 'scs-core' ); ?></span></p>
			<progress class="scs-enquiry__bar" max="2" value="1" aria-label="<?php esc_attr_e( 'Enquiry progress', 'scs-core' ); ?>"></progress>
		<?php endif; ?>
		<h1 id="enquiry-title" tabindex="-1"><?php esc_html_e( 'Tell us about your safari', 'scs-core' ); ?></h1>
		<p><?php esc_html_e( 'Share when you’d like to travel, who’s coming, and what you’re hoping to see. Your answers give us a starting point for planning your Kenya safari.', 'scs-core' ); ?></p>
		<?php if ( $has_form ) : ?>
			<a class="scs-button" href="#enquiry-details"><?php esc_html_e( 'Start my enquiry', 'scs-core' ); ?></a>
		<?php else : ?>
			<p><?php esc_html_e( 'Online enquiries are currently unavailable.', 'scs-core' ); ?></p>
		<?php endif; ?>
	</section>
	<?php if ( $has_form ) : ?>
		<section id="enquiry-details" class="scs-enquiry__step" aria-labelledby="enquiry-details-title" data-scs-planner data-scs-context="general" data-scs-surface="enquiry">
			<p class="scs-enquiry__progress"><span><?php esc_html_e( 'Step 2 of 2', 'scs-core' ); ?></span><span><?php esc_html_e( 'Your enquiry', 'scs-core' ); ?></span></p>
			<progress class="scs-enquiry__bar" max="2" value="2" aria-label="<?php esc_attr_e( 'Enquiry progress', 'scs-core' ); ?>"></progress>
			<h2 id="enquiry-details-title" tabindex="-1"><?php esc_html_e( 'Your trip details', 'scs-core' ); ?></h2>
			<a class="scs-text-link scs-enquiry__back" href="#enquiry-start"><?php esc_html_e( 'Back to introduction', 'scs-core' ); ?></a>
			<?php echo do_shortcode( '[forminator_form id="' . (int) $settings['id'] . '"]' ); ?>
		</section>
	<?php endif; ?>
</div>
