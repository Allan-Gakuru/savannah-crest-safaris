<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

if ( ! is_post_type_archive( SAFARI ) ) {
	return;
}

global $wp_query;
$state = current_filters();
$values = $state['values'];
$active_options = filter_options();
$url = catalogue_url();
$tracking = attribution( wp_unslash( $_GET ) );
?>
<section class="scs-shell scs-section scs-catalogue" aria-label="<?php esc_attr_e( 'Safari catalogue', 'scs-core' ); ?>">
	<?php filter_form( $values ); ?>
	<?php if ( $state['invalid'] ) : ?>
		<div class="scs-message">
			<h2><?php esc_html_e( 'Some filters are unavailable', 'scs-core' ); ?></h2>
			<p><?php esc_html_e( 'Return to the full catalogue and choose from the current filters.', 'scs-core' ); ?></p>
			<?php echo action_link( $url, __( 'Clear filters', 'scs-core' ), 'scs-button scs-button--outline' ); ?>
		</div>
	<?php else : ?>
		<?php if ( $values ) : ?>
			<ul class="scs-chips" role="list" aria-label="<?php esc_attr_e( 'Active filters', 'scs-core' ); ?>">
				<?php foreach ( $values as $key => $value ) : ?>
					<?php
					$label = $active_options[ $key ][1][ $value ];
					$remaining = array_diff_key( $values, array( $key => true ) ) + $tracking;
					?>
					<li><a href="<?php echo esc_url( add_query_arg( $remaining, $url ) ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Remove %s', 'scs-core' ), $label ) ); ?>"><?php echo esc_html( $label ); ?> <span aria-hidden="true">×</span></a></li>
				<?php endforeach; ?>
				<li><a href="<?php echo esc_url( add_query_arg( $tracking, $url ) ); ?>"><?php esc_html_e( 'Clear filters', 'scs-core' ); ?></a></li>
			</ul>
		<?php endif; ?>
		<?php if ( $wp_query->posts ) : ?>
			<h2 class="scs-results-heading"><?php echo esc_html( sprintf( _n( '%s safari', '%s safaris', (int) $wp_query->found_posts, 'scs-core' ), number_format_i18n( $wp_query->found_posts ) ) ); ?></h2>
			<?php
			cards( $wp_query->posts );
			$links = paginate_links( array(
				'total' => (int) $wp_query->max_num_pages,
				'current' => max( 1, (int) get_query_var( 'paged' ) ),
				'type' => 'list',
				'add_args' => $values + $tracking,
				'prev_text' => __( 'Previous', 'scs-core' ),
				'next_text' => __( 'Next', 'scs-core' ),
			) );
			?>
			<?php if ( $links ) : ?><nav class="scs-pagination" aria-label="<?php esc_attr_e( 'Safari pages', 'scs-core' ); ?>"><?php echo $links; // WordPress pagination markup. ?></nav><?php endif; ?>
		<?php else : ?>
			<div class="scs-message">
				<h2><?php echo esc_html( $values ? __( 'No safaris match these filters', 'scs-core' ) : __( 'There are no itineraries to browse yet', 'scs-core' ) ); ?></h2>
				<?php if ( $values ) : ?><p><?php esc_html_e( 'Try removing a filter to see more safaris.', 'scs-core' ); ?></p><?php endif; ?>
				<?php echo action_link( planner_url(), __( 'Plan your safari', 'scs-core' ) ); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</section>
