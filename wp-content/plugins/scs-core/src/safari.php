<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

const PRICE_BASES = array( 'per_person_sharing', 'per_group' );

function itinerary_error( array $itinerary, int $duration ): string {
	$expected = 1;
	foreach ( $itinerary as $day ) {
		$from = positive_int( $day['day_from'] ?? null );
		$end  = text( $day['day_to'] ?? '' );
		$to   = '' === $end ? $from : positive_int( $end );
		if ( $expected !== $from || $to < $from || $to > $duration ) {
			return __( 'Enter the itinerary in order, covering every day from Day 1 through the trip duration exactly once.', 'scs-core' );
		}
		if ( '' === text( $day['heading'] ?? '' ) || '' === term_label( $day['destination'] ?? 0 ) || '' === trim( wp_strip_all_tags( text( $day['narrative'] ?? '' ) ) ) ) {
			return __( 'Give every itinerary row a heading, a valid destination and a description.', 'scs-core' );
		}
		$expected = $to + 1;
	}
	return $duration > 0 && $expected === $duration + 1 ? '' :
		__( 'Add itinerary rows covering the full trip duration.', 'scs-core' );
}

function valid_rate_date( $value ): string {
	$digits = str_replace( '-', '', text( $value ) );
	if ( ! preg_match( '/^[0-9]{8}$/D', $digits ) ) {
		return '';
	}
	$date = \DateTimeImmutable::createFromFormat( '!Ymd', $digits );
	return $date && $date->format( 'Ymd' ) === $digits ? $digits : '';
}

function amount( $value ): float {
	return is_numeric( $value ) && is_finite( (float) $value ) && (float) $value > 0 ? (float) $value : 0.0;
}

function rate( array $fields ): array {
	$mode = text( $fields['scs_price_mode'] ?? '' ) ?: 'on_request';
	$from = amount( $fields['scs_price_from'] ?? null );
	$to   = amount( $fields['scs_price_to'] ?? null );
	$expiry = $fields['scs_price_valid_until'] ?? '';
	// An empty field has no expiry. An invalid date must keep the rate hidden.
	$until = '' === $expiry || false === $expiry ? '' : ( valid_rate_date( $expiry ) ?: null );
	return array(
		'mode'     => $mode,
		'from'     => $from,
		'to'       => $to,
		'currency' => text( $fields['scs_price_currency'] ?? '' ),
		'basis'    => text( $fields['scs_price_basis'] ?? '' ),
		'until'    => $until,
		'note'     => text( $fields['scs_price_note'] ?? '' ),
	);
}

function has_valid_rate( array $rate ): bool {
	return in_array( $rate['mode'], array( 'from', 'range' ), true )
		&& $rate['from'] > 0 && ( 'from' === $rate['mode'] || $rate['to'] >= $rate['from'] )
		&& 'USD' === $rate['currency'] && in_array( $rate['basis'], PRICE_BASES, true )
		&& null !== $rate['until'] && '' !== $rate['note'];
}

function has_current_rate( array $rate, string $today ): bool {
	return has_valid_rate( $rate ) && ( '' === $rate['until'] || $rate['until'] >= $today );
}

function format_amount( float $value ): string {
	return number_format_i18n( $value, floor( $value ) === $value ? 0 : 2 );
}

function price_label( array $fields, string $today ): string {
	$rate = rate( $fields );
	if ( ! has_current_rate( $rate, $today ) ) {
		return __( 'Price on request', 'scs-core' );
	}
	$basis = 'per_group' === $rate['basis'] ? __( 'per group', 'scs-core' ) : __( 'per person sharing', 'scs-core' );
	if ( 'range' === $rate['mode'] && $rate['to'] > $rate['from'] ) {
		return sprintf( __( '%1$s %2$s–%3$s %4$s', 'scs-core' ), $rate['currency'], format_amount( $rate['from'] ), format_amount( $rate['to'] ), $basis );
	}
	return sprintf( __( 'From %1$s %2$s %3$s', 'scs-core' ), $rate['currency'], format_amount( $rate['from'] ), $basis );
}

function safari_fields( int $id, array $override = array() ): array {
	$fields = array();
	foreach ( array( 'scs_duration_days', 'scs_primary_destination', 'scs_start_location', 'scs_end_location', 'scs_price_mode',
		'scs_price_from', 'scs_price_to', 'scs_price_currency', 'scs_price_basis', 'scs_price_note', 'scs_price_valid_until',
		'scs_itinerary', 'scs_highlights', 'scs_includes', 'scs_excludes', 'scs_gallery', 'scs_best_time', 'scs_related_safaris' ) as $name ) {
		$fields[ $name ] = array_key_exists( $name, $override ) ? $override[ $name ] : ( $id ? field( $name, $id ) : null );
	}
	return $fields;
}

function bullet_labels( $value ): array {
	return array_values( array_filter( array_map( static function ( array $row ): string {
		return text( $row['label'] ?? '' );
	}, rows( $value ) ) ) );
}

function publication_errors( array $fields, array $destinations, int $image, string $title = '' ): array {
	$errors = array();
	$duration = positive_int( $fields['scs_duration_days'] ?? null );
	$primary  = positive_int( $fields['scs_primary_destination'] ?? null );
	if ( '' === trim( $title ) || ! $image || ! wp_attachment_is_image( $image ) ) {
		$errors[] = __( 'Add a safari title and a featured image before publishing.', 'scs-core' );
	}
	if ( ! $duration || '' === text( $fields['scs_start_location'] ?? '' ) || '' === text( $fields['scs_end_location'] ?? '' ) ) {
		$errors[] = __( 'Enter the trip duration, starting point and ending point.', 'scs-core' );
	}
	if ( ! $primary || ! in_array( $primary, $destinations, true ) || '' === term_label( $primary ) ) {
		$errors[] = __( 'Assign the primary destination under Destinations and select it in Safari details.', 'scs-core' );
	}
	$day_error = itinerary_error( rows( $fields['scs_itinerary'] ?? array() ), $duration );
	if ( '' !== $day_error ) {
		$errors[] = $day_error;
	}
	if ( ! bullet_labels( $fields['scs_includes'] ?? array() ) || ! bullet_labels( $fields['scs_excludes'] ?? array() ) ) {
		$errors[] = __( 'List at least one inclusion and one exclusion.', 'scs-core' );
	}
	$rate = rate( $fields );
	if ( 'on_request' !== $rate['mode'] && ! has_valid_rate( $rate ) ) {
		$errors[] = __( 'Complete the advertised rate, currency, basis and pricing note, and check any entered validity date. You can also choose Price on request.', 'scs-core' );
	}
	return $errors;
}

function published_status( string $status ): bool {
	return in_array( $status, array( 'publish', 'future' ), true );
}

function current_destinations( int $id ): array {
	return $id ? ids( wp_get_object_terms( $id, DESTINATION, array( 'fields' => 'ids' ) ) ) : array();
}

function rest_write_ticket( int $id, bool $set = false ): bool {
	static $pending = null;
	if ( $set ) {
		$pending = $id;
		return true;
	}
	if ( $id !== $pending ) {
		return false;
	}
	$pending = null;
	return true;
}

function validate_rest_safari( $prepared, \WP_REST_Request $request ) {
	if ( is_wp_error( $prepared ) ) {
		return $prepared;
	}
	$id     = (int) ( $request['id'] ?? 0 );
	$status = $prepared->post_status ?? ( $id ? get_post_status( $id ) : 'draft' );
	if ( ! published_status( (string) $status ) ) {
		return $prepared;
	}
	$fields = $request->get_param( 'acf' );
	$assigned = $request->has_param( DESTINATION ) ? ids( $request[ DESTINATION ] ) : current_destinations( $id );
	$image = $request->has_param( 'featured_media' ) ? positive_int( $request['featured_media'] ) : ( $id ? (int) get_post_thumbnail_id( $id ) : 0 );
	$title = $prepared->post_title ?? ( $id ? get_post_field( 'post_title', $id ) : '' );
	$errors = publication_errors( safari_fields( $id, is_array( $fields ) ? $fields : array() ), $assigned, $image, (string) $title );
	if ( $errors ) {
		return new \WP_Error( 'scs_safari_incomplete', implode( ' ', $errors ), array( 'status' => 422 ) );
	}
	rest_write_ticket( $id, true );
	return $prepared;
}

function classic_field_values( array $values ): array {
	$normalized = array();
	foreach ( $values as $key => $value ) {
		if ( 'acfcloneindex' === $key ) {
			continue;
		}
		if ( is_string( $key ) && 0 === strpos( $key, 'field_' ) ) {
			$definition = acf_get_field( $key );
			if ( ! $definition ) {
				continue;
			}
			$key = $definition['name'];
		}
		$normalized[ $key ] = is_array( $value ) ? classic_field_values( $value ) : $value;
	}
	return $normalized;
}

function guard_safari_write( bool $empty, array $post ): bool {
	if ( $empty || SAFARI !== ( $post['post_type'] ?? '' ) || ! published_status( $post['post_status'] ?? '' ) ) {
		return $empty;
	}
	$id = (int) ( $post['ID'] ?? 0 );
	if ( rest_write_ticket( $id ) ) {
		return false;
	}

	// The Classic Editor and the block editor's separate SCF metabox request use this path.
	$classic = $id && is_admin() && current_user_can( 'edit_post', $id ) && ! empty( $_POST['_acf_nonce'] )
		&& is_string( $_POST['_acf_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_acf_nonce'] ) ), 'post' );
	$submitted = $classic && function_exists( 'acf_get_field' ) && isset( $_POST['acf'] ) && is_array( $_POST['acf'] )
		? classic_field_values( wp_unslash( $_POST['acf'] ) ) : array();
	$assigned = isset( $post['tax_input'][ DESTINATION ] ) && current_user_can( 'edit_posts' )
		? ids( $post['tax_input'][ DESTINATION ] ) : current_destinations( $id );
	$image = isset( $post['_thumbnail_id'] ) ? positive_int( $post['_thumbnail_id'] ) : ( $id ? (int) get_post_thumbnail_id( $id ) : 0 );
	$errors = publication_errors( safari_fields( $id, $submitted ), $assigned, $image, (string) ( $post['post_title'] ?? '' ) );
	if ( $errors && $classic ) {
		wp_die( esc_html( implode( ' ', $errors ) ), esc_html__( 'Safari cannot be published', 'scs-core' ), array( 'response' => 422, 'back_link' => true ) );
	}
	return (bool) $errors;
}

function itinerary_route( array $itinerary ): array {
	$names = array();
	foreach ( $itinerary as $day ) {
		$name = term_label( $day['destination'] ?? 0 );
		if ( '' !== $name && end( $names ) !== $name ) {
			$names[] = $name;
		}
	}
	return $names;
}
