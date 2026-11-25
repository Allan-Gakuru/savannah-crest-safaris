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

function safari_experience_choices( array $trip ): array {
	$routes = require __DIR__ . '/../content/safari-experiences.php';
	if ( isset( $routes[ $trip['slug'] ] ) ) {
		return $routes[ $trip['slug'] ];
	}
	$experiences = wp_get_post_terms( (int) $trip['id'], EXPERIENCE, array( 'fields' => 'names' ) );
	return ! is_wp_error( $experiences ) && $experiences
		? $experiences
		: array( __( 'I’d like suggestions for this itinerary', 'scs-core' ) );
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

	$meta['fields'] = planner_date_picker( $meta['fields'] );
	$trip = planner_form_trip( $form_id );
	if ( $trip ) {
		$meta['fields'] = trip_form_fields( $meta['fields'], $trip );
	}
	return array( $meta );
}

function planner_date_picker( array $fields ): array {
	foreach ( $fields as $index => $field ) {
		if ( TRAVEL_DATE_FIELD !== ( $field['id'] ?? '' ) || 'text' !== ( $field['type'] ?? '' ) ) {
			continue;
		}
		// Keep the existing field ID so entries and notification merge fields stay compatible.
		$fields[ $index ] = array_replace( $field, array(
			'type'             => 'date',
			'field_type'       => 'picker',
			'date_format'      => 'yy-mm-dd',
			'default_date'     => 'none',
			'icon'             => 'true',
			'start-date'       => 'today',
			'required'         => false,
			'placeholder'      => __( 'Select a date', 'scs-core' ),
			'description'      => __( 'Choose your preferred start date. Leave it blank if your dates are flexible.', 'scs-core' ),
			'restrict_message' => __( 'Choose today or a future date.', 'scs-core' ),
		) );
		break;
	}
	return $fields;
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
	if ( 'checkbox' === ( $types[ TRIP_EXPERIENCE_FIELD ] ?? '' ) ) {
		$experience = array_search( TRIP_EXPERIENCE_FIELD, $field_ids, true );
		$fields[ $experience ]['options'] = planner_choice_options( safari_experience_choices( $trip ) );
	}

	$page_start = array_search( TRIP_DESTINATION_BREAK, $field_ids, true );
	if ( false === $page_start || 'page-break' !== $types[ TRIP_DESTINATION_BREAK ] ) {
		return $fields;
	}
	// The destination page starts at this break and ends at the next one.
	$page = array_slice( $types, $page_start + 1, null, true );
	$page_end = array_search( 'page-break', array_values( $page ), true );
	if ( false === $page_end ) {
		return $fields;
	}
	$page = array_slice( $page, 0, $page_end, true );
	ksort( $page );
	if ( TRIP_DESTINATION_FIELDS !== $page ) {
		return $fields;
	}
	array_splice( $fields, $page_start, count( $page ) + 1 );
	return $fields;
}
