<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

const TRIP_PRIORITY_FIELD = 'radio-2';
const TRIP_EXPERIENCE_FIELD = 'checkbox-1';
const TRIP_DESTINATION_BREAK = 'page-break-5';
const TRIP_DESTINATION_FIELDS = array( 'checkbox-2' => 'checkbox', 'text-2' => 'text' );
const TRAVEL_DATE_FIELD = 'text-3';
const FORMINATOR_FORM_META = 'forminator_form_meta';

add_filter( 'get_post_metadata', __NAMESPACE__ . '\tailor_planner_form', 10, 4 );
add_filter( 'forminator_field_markup', __NAMESPACE__ . '\planner_field_markup', 10, 3 );

function safari_priority_choices( array $trip ): array {
	$id = (int) $trip['id'];
	$manual = preg_split( '/\R/u', html_entity_decode( text( field( 'scs_enquiry_priorities', $id ) ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) ?: array();
	$help = __( 'I’d like your advice', 'scs-core' );
	$manual = array_unique( array_filter( array_map( 'sanitize_text_field', $manual ) ) );
	$manual = array_slice( array_values( array_diff( $manual, array( $help ) ) ), 0, 5 );
	if ( $manual ) {
		return array_merge( $manual, array( $help ) );
	}

	$routes = require __DIR__ . '/../content/safari-priorities.php';
	$choices = $routes[ $trip['slug'] ] ?? array();
	if ( ! $choices ) {
		$experiences = wp_get_post_terms( $id, EXPERIENCE, array( 'fields' => 'slugs' ) );
		$experiences = is_wp_error( $experiences ) ? array() : $experiences;
		$topics = array(
			'game-drives'             => __( 'More time on game drives', 'scs-core' ),
			'maasai-community-visits' => __( 'Time with local communities', 'scs-core' ),
			'walking-cycling'         => __( 'More time walking or cycling', 'scs-core' ),
		);
		$choices = array_values( array_intersect_key( $topics, array_fill_keys( $experiences, true ) ) );
		$choices[] = __( 'More time at my favourite stops', 'scs-core' );
		$choices[] = __( 'A more relaxed pace', 'scs-core' );
		$choices[] = __( 'A special occasion or personal touch', 'scs-core' );
		$choices = array_slice( $choices, 0, 5 );
	}
	return array_merge( $choices, array( $help ) );
}

function planner_choice_options( array $labels ): array {
	// Keep selected answers readable in entries and notifications after the choices change.
	return array_map( static function ( string $label ): array {
		$escaped = esc_html( $label );
		return array( 'label' => $escaped, 'value' => $escaped, 'default' => '', 'calculation' => 0 );
	}, $labels );
}

function planner_form_trip( int $form_id ): array {
	if ( isset( $_POST['_scs_planner_context'] ) ) {
		if ( $form_id !== positive_int( $_POST['form_id'] ?? null ) ||
			( 'forminator_submit_form_custom-forms' !== ( $_POST['action'] ?? '' ) && ! is_page( PLANNER_SLUG ) ) ) {
			return array();
		}
		$context = context_from_token( wp_unslash( $_POST['_scs_planner_context'] ) );
	} elseif ( is_page( PLANNER_SLUG ) ) {
		$context = active_context();
	} else {
		return array();
	}
	return 'trip' === ( $context['kind'] ?? '' ) ? $context : array();
}

function tailor_planner_form( $value, int $form_id, string $key, bool $single ) {
	if ( null !== $value || FORMINATOR_FORM_META !== $key || ! $single ) {
		return $value;
	}
	$config = configured_form();
	if ( $form_id !== ( $config['id'] ?? 0 ) ) {
		return $value;
	}
	$is_submission = 'forminator_submit_form_custom-forms' === ( $_POST['action'] ?? '' ) &&
		$form_id === positive_int( $_POST['form_id'] ?? null );
	if ( ! $is_submission && ! is_page( array( PLANNER_SLUG, ENQUIRY_SLUG ) ) ) {
		return $value;
	}

	// Read all metadata to get the stored definition without this key-specific filter.
	// Shape the fields before Forminator builds and caches its validation model.
	$stored = get_post_meta( $form_id );
	$meta = maybe_unserialize( $stored[ FORMINATOR_FORM_META ][0] ?? null );
	if ( ! is_array( $meta['fields'] ?? null ) ) {
		return $value;
	}

	$meta['fields'] = planner_question_fields( $meta['fields'] );
	$trip = planner_form_trip( $form_id );
	if ( $trip ) {
		$meta['fields'] = trip_form_fields( $meta['fields'], $trip );
	}
	return array( $meta );
}

function planner_question_fields( array $fields ): array {
	foreach ( $fields as $index => $field ) {
		$id = $field['id'] ?? '';
		$type = $field['type'] ?? '';
		if ( 'email-1' === $id && 'email' === $type ) {
			$fields[ $index ]['field_label'] = __( 'What is your best email?', 'scs-core' );
		}
		if ( 'text-1' === $id && 'text' === $type ) {
			$fields[ $index ]['description'] = __( 'Enter their ages separated by commas, for example, 6, 12.', 'scs-core' );
			$fields[ $index ]['placeholder'] = __( '6, 12', 'scs-core' );
		}
		if ( 'phone-1' === $id && 'phone' === $type && 'Optional. Add your country code if you’d like a call.' === ( $field['description'] ?? '' ) ) {
			$fields[ $index ]['description'] = __( 'Add your country code if you’d like a call.', 'scs-core' );
		}
		if ( 'textarea-1' === $id && 'textarea' === $type && 'Optional. We’ll use your answers to respond to your enquiry.' === ( $field['description'] ?? '' ) ) {
			$fields[ $index ]['description'] = __( 'We’ll use your answers to respond to your enquiry.', 'scs-core' );
		}
		if ( TRAVEL_DATE_FIELD === $id && 'text' === $type ) {
			// Keep the field ID for entries and notification merge fields.
			$fields[ $index ] = array_replace( $field, array(
				'type'             => 'date',
				'field_type'       => 'picker',
				'date_format'      => 'yy-mm-dd',
				'default_date'     => 'none',
				'icon'             => 'false',
				'start-date'       => 'today',
				'required'         => false,
				'placeholder'      => __( 'Select a date', 'scs-core' ),
				'description'      => __( 'Choose your preferred start date. Leave it blank if your dates are flexible.', 'scs-core' ),
				'restrict_message' => __( 'Choose today or a future date.', 'scs-core' ),
			) );
		}
	}
	return $fields;
}

function planner_field_markup( string $html, array $field, $renderer ): string {
	$config = configured_form();
	if ( empty( $config['id'] ) || (int) ( $renderer->model->id ?? 0 ) !== $config['id'] ||
		! is_page( array( PLANNER_SLUG, ENQUIRY_SLUG ) ) ) {
		return $html;
	}
	if ( ! empty( $field['field_label'] ) && ! filter_var( $field['required'] ?? false, FILTER_VALIDATE_BOOLEAN ) ) {
		$html = preg_replace_callback(
			'~(<(label|span|legend)\b[^>]*\bclass="[^"]*\bforminator-label\b[^"]*"[^>]*>.*?)(</\2>)~s',
			static function ( array $label ): string {
				return $label[1] . ' <em class="scs-question-optional">' . esc_html__( '(optional)', 'scs-core' ) . '</em>' . $label[3];
			},
			$html,
			1
		) ?? $html;
	}
	if ( TRAVEL_DATE_FIELD === ( $field['element_id'] ?? '' ) && 'date' === ( $field['type'] ?? '' ) ) {
		$html .= '<div class="scs-date-picker" hidden><div class="scs-date-calendar"></div>' .
			'<button class="scs-date-clear" type="button">' . esc_html__( 'Clear date', 'scs-core' ) . '</button></div>';
	}
	return $html;
}

function trip_form_fields( array $fields, array $trip ): array {
	$types = array_column( $fields, 'type', 'id' );
	if ( count( $types ) !== count( $fields ) ) {
		return $fields;
	}
	$fields = array_values( $fields );
	$field_ids = array_keys( $types );
	if ( 'radio' === ( $types[ TRIP_PRIORITY_FIELD ] ?? '' ) ) {
		$priority = array_search( TRIP_PRIORITY_FIELD, $field_ids, true );
		$fields[ $priority ]['options'] = planner_choice_options( safari_priority_choices( $trip ) );
	}
	$page_start = array_search( TRIP_DESTINATION_BREAK, $field_ids, true );
	if ( false !== $page_start && 'page-break' === $types[ TRIP_DESTINATION_BREAK ] ) {
		$page = array_slice( $types, $page_start + 1, null, true );
		$page_end = array_search( 'page-break', array_values( $page ), true );
		$page = false === $page_end ? array() : array_slice( $page, 0, $page_end, true );
		ksort( $page );
		if ( TRIP_DESTINATION_FIELDS === $page ) {
			// Forminator stores each page's progress label on its closing break.
			array_splice( $fields, $page_start + 1, count( $page ) + 1 );
		}
	}
	if ( 'checkbox' === ( $types[ TRIP_EXPERIENCE_FIELD ] ?? '' ) ) {
		$experience = array_search( TRIP_EXPERIENCE_FIELD, array_column( $fields, 'id' ), true );
		$only_question = 'page-break' === ( $fields[ $experience - 1 ]['type'] ?? '' ) &&
			'page-break' === ( $fields[ $experience + 1 ]['type'] ?? '' );
		array_splice( $fields, $experience, $only_question ? 2 : 1 );
	}
	return $fields;
}
