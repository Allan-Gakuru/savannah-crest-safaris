<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

function action_link( string $url, string $label, string $class = 'scs-button', string $placement = '' ): string {
	if ( '' === $url ) {
		return '';
	}
	$tracking = $placement ? ' data-scs-enquiry="' . esc_attr( $placement ) . '"' : '';
	return sprintf( '<a class="%1$s" href="%2$s"%3$s>%4$s</a>', esc_attr( $class ), esc_url( $url ), $tracking, esc_html( $label ) );
}

function image( $id, string $size, array $attributes = array() ): string {
	$id = positive_int( $id );
	return $id ? wp_get_attachment_image( $id, $size, false, $attributes ) : '';
}

function route_for( array $fields ): string {
	$route = itinerary_route( rows( $fields['scs_itinerary'] ?? array() ) );
	return $route ? implode( ' · ', $route ) : term_label( $fields['scs_primary_destination'] ?? 0 );
}

function duration_label( $value ): string {
	$days = positive_int( $value );
	return $days ? sprintf( _n( '%s day', '%s days', $days, 'scs-core' ), number_format_i18n( $days ) ) : '';
}

function safari_card_thumbnail( \WP_Post $safari, array $gallery ): string {
	static $selections;
	$selections ??= require __DIR__ . '/../content/safari-card-media.php';
	$cover = (int) get_post_thumbnail_id( $safari );
	$picked = $selections[ $safari->post_name ] ?? array();
	$picked = $cover === ( $picked['cover'] ?? 0 ) ? $picked : array();
	$sizes = '(max-width: 560px) 92vw, (max-width: 900px) 46vw, (max-width: 1328px) 31vw, 390px';

	if ( isset( $picked['asset'] ) ) {
		$asset = 'assets/images/' . $picked['asset'];
		if ( is_readable( DIR . '/' . $asset . '-960.webp' ) && is_readable( DIR . '/' . $asset . '-480.webp' ) ) {
			$small = plugins_url( $asset . '-480.webp', FILE );
			$large = plugins_url( $asset . '-960.webp', FILE );
			return sprintf(
				'<img src="%1$s" srcset="%2$s 480w, %1$s 960w" sizes="%3$s" width="960" height="640" alt="%4$s" loading="lazy" decoding="async">',
				esc_url( $large ),
				esc_url( $small ),
				esc_attr( $sizes ),
				esc_attr( $picked['alt'] )
			);
		}
	}

	$attachment = $picked['attachment'] ?? 0;
	$preferred = in_array( $attachment, $gallery, true ) ? $attachment : 0;
	$attributes = array( 'sizes' => $sizes, 'loading' => 'lazy', 'decoding' => 'async' );
	foreach ( array_unique( array_merge( array( $preferred, $cover ), $gallery ) ) as $id ) {
		$photo = image( $id, 'medium_large', $attributes );
		if ( $photo ) {
			return $photo;
		}
	}
	return '';
}

function cards( array $safaris, string $enquiry_url = '' ): void {
	if ( ! $safaris && '' === $enquiry_url ) {
		return;
	}
	echo '<ul class="scs-card-grid" role="list">';
	foreach ( $safaris as $safari ) {
		$fields = safari_fields( $safari->ID );
		$route  = term_label( $fields['scs_primary_destination'] ) ?: route_for( $fields );
		$days = duration_label( $fields['scs_duration_days'] );
		$title = get_the_title( $safari );
		$overview = wp_trim_words( strip_shortcodes( get_the_excerpt( $safari ) ), 28 );
		$photo = safari_card_thumbnail( $safari, ids( $fields['scs_gallery'] ) );
		?>
		<li class="scs-card">
			<a class="scs-card__link" href="<?php echo esc_url( get_permalink( $safari ) ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View trip details for %s', 'scs-core' ), $title ) ); ?>">
				<div class="scs-card__image">
					<?php echo $photo; // Escaped image markup. ?>
					<?php if ( $days ) : ?><span class="scs-card__duration"><?php echo esc_html( $days ); ?></span><?php endif; ?>
				</div>
				<div class="scs-card__body">
					<?php if ( $route ) : ?><p class="scs-card__meta"><?php echo esc_html( $route ); ?></p><?php endif; ?>
					<h3><?php echo esc_html( $title ); ?></h3>
					<?php if ( $overview ) : ?><p class="scs-card__overview"><?php echo esc_html( $overview ); ?></p><?php endif; ?>
					<div class="scs-card__footer">
						<p class="scs-card__price"><?php echo esc_html( price_label( $fields, wp_date( 'Ymd' ) ) ); ?></p>
						<span class="scs-button scs-button--outline scs-card__action"><?php esc_html_e( 'View trip details', 'scs-core' ); ?></span>
					</div>
				</div>
			</a>
		</li>
		<?php
	}
	if ( '' !== $enquiry_url ) :
		?>
		<li class="scs-card scs-card--dream">
			<div class="scs-card__image" aria-hidden="true"></div>
			<div class="scs-card__body">
				<h3><?php esc_html_e( 'Tell us about your dream safari', 'scs-core' ); ?></h3>
				<p><?php esc_html_e( 'Share what you’d love to see, when you’d like to go, and who’s coming. We’ll help you find the right route.', 'scs-core' ); ?></p>
				<?php echo action_link( $enquiry_url, __( 'Enquire now', 'scs-core' ), 'scs-button scs-button--reverse scs-card__action', 'home-dream-safari' ); ?>
			</div>
		</li>
		<?php
	endif;
	echo '</ul>';
}

function photos( $attachments ): void {
	$gallery = array_filter( array_map( static function ( int $id ): string {
		return image( $id, 'large', array( 'loading' => 'lazy', 'decoding' => 'async' ) );
	}, ids( $attachments ) ) );
	if ( $gallery ) {
		echo '<div class="scs-photo-grid">' . implode( '', $gallery ) . '</div>'; // WordPress attachment markup.
	}
}

function bulleted_section( string $title, array $items ): void {
	if ( ! $items ) {
		return;
	}
	echo '<div><h3>' . esc_html( $title ) . '</h3><ul>';
	foreach ( $items as $item ) {
		echo '<li>' . esc_html( $item ) . '</li>';
	}
	echo '</ul></div>';
}

function filter_select( string $key, string $label, array $options, string $value, string $id ): void {
	?>
	<label class="scs-field">
		<span><?php echo esc_html( $label ); ?></span>
		<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>">
			<?php foreach ( $options as $slug => $option ) : ?>
				<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $value, $slug ); ?>><?php echo esc_html( $option ); ?></option>
			<?php endforeach; ?>
		</select>
	</label>
	<?php
}

function filter_options(): array {
	$inventory = inventory_terms();
	return array(
		'where' => array( __( 'Destination', 'scs-core' ), array( '' => __( 'Any destination', 'scs-core' ) ) + wp_list_pluck( $inventory['where'], 'name' ) ),
		'experience' => array( __( 'Experience', 'scs-core' ), array( '' => __( 'Any experience', 'scs-core' ) ) + wp_list_pluck( $inventory['experience'], 'name' ) ),
		'duration' => array( __( 'Duration', 'scs-core' ), array( '' => __( 'Any duration', 'scs-core' ), '1-4' => __( '1–4 days', 'scs-core' ), '5-7' => __( '5–7 days', 'scs-core' ), '8-plus' => __( '8+ days', 'scs-core' ) ) ),
		'comfort' => array( __( 'Comfort', 'scs-core' ), array( '' => __( 'Any comfort level', 'scs-core' ) ) + wp_list_pluck( $inventory['comfort'], 'name' ) ),
		'sort' => array( __( 'Sort by', 'scs-core' ), array( '' => __( 'Newest', 'scs-core' ), 'shortest' => __( 'Shortest first', 'scs-core' ) ) ),
	);
}

function attribution( array $source ): array {
	$output = array();
	foreach ( array( 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term' ) as $key ) {
		if ( isset( $source[ $key ] ) && is_string( $source[ $key ] ) ) {
			$output[ $key ] = sanitize_text_field( substr( $source[ $key ], 0, 100 ) );
		}
	}
	return $output;
}

function filter_form( array $values, bool $finder = false ): void {
	$action = catalogue_url();
	if ( ! $action ) {
		return;
	}
	$options = filter_options();
	$keys = $finder ? array( 'where', 'duration' ) : FILTERS;
	$id_prefix = $finder ? 'safari-finder-' : 'safari-filter-';
	$hidden = attribution( wp_unslash( $_GET ) );
	$query = wp_parse_url( $action, PHP_URL_QUERY );
	if ( $query ) {
		wp_parse_str( $query, $args );
		$hidden += array_intersect_key( $args, array( 'post_type' => true ) );
	}
	?>
	<form method="get" action="<?php echo esc_url( $action ); ?>" class="scs-filter-form" aria-label="<?php echo esc_attr( $finder ? __( 'Find a safari', 'scs-core' ) : __( 'Filter safaris', 'scs-core' ) ); ?>">
		<?php foreach ( $keys as $key ) : ?>
			<?php if ( 'comfort' !== $key || count( $options['comfort'][1] ) > 1 ) : ?>
				<?php filter_select( $key, $options[ $key ][0], $options[ $key ][1], $values[ $key ] ?? '', $id_prefix . $key ); ?>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php foreach ( $hidden as $key => $value ) : ?><input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>"><?php endforeach; ?>
		<button type="submit" class="scs-button"><?php echo esc_html( $finder ? __( 'Find safaris', 'scs-core' ) : __( 'Show safaris', 'scs-core' ) ); ?></button>
	</form>
	<?php
}

function term_items( array $terms, string $catalogue_filter = '' ): void {
	if ( ! $terms ) {
		return;
	}
	$catalogue = $catalogue_filter ? catalogue_url() : '';
	echo '<ul class="scs-term-grid" role="list">';
	foreach ( $terms as $term ) {
		$url = browse_term_url( $term, $catalogue_filter, $catalogue );
		if ( ! $url ) {
			continue;
		}
		$attributes = array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' );
		$photo = image( term_field( 'scs_term_hero', $term ), 'medium_large', $attributes );
		if ( ! $photo && $catalogue ) {
			$trips = matching_safaris( array( term_clause( $term ) ), array(), 1 );
			if ( $trips ) {
				$thumbnail = (int) get_post_thumbnail_id( $trips[0] );
				$gallery = array_values( array_diff( ids( field( 'scs_gallery', $trips[0]->ID ) ), array( $thumbnail ) ) );
				$photo = image( $gallery[0] ?? 0, 'medium_large', $attributes ) ?: image( $thumbnail, 'medium_large', $attributes );
			}
		}
		echo '<li><a href="' . esc_url( $url ) . '">';
		echo $photo; // WordPress attachment markup.
		echo '<span>' . esc_html( $term->name ) . ' <span aria-hidden="true">↗</span></span></a></li>';
	}
	echo '</ul>';
}

function ready_terms( string $taxonomy, array $args = array(), array $picks = array(), int $limit = 0 ): array {
	$terms = get_terms( array_replace( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ), $args ) );
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	$terms = array_filter( $terms, __NAMESPACE__ . '\hub_ready' );
	$by_id = array();
	foreach ( $terms as $term ) {
		$by_id[ $term->term_id ] = $term;
	}
	$sorted = array_replace( array_intersect_key( array_fill_keys( $picks, null ), $by_id ), $by_id );
	return $limit ? array_slice( array_values( $sorted ), 0, $limit ) : array_values( $sorted );
}

function guide_links( array $posts ): void {
	if ( ! $posts ) {
		return;
	}
	echo '<ul class="scs-reading-list" role="list">';
	foreach ( $posts as $post ) {
		echo '<li><a href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '<span aria-hidden="true">↗</span></a></li>';
	}
	echo '</ul>';
}
