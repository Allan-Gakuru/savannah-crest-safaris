<?php
/** Exercises real domain helpers with standalone WordPress boundaries. */
if ( 'cli' !== PHP_SAPI ) {
	exit;
}
define( 'ABSPATH', __DIR__ );
define( 'OBJECT', 'OBJECT' );

class WP_Term {
	public $name;
	public $slug;
	public $term_id;
	public $taxonomy;
	public $description = 'Approved destination guidance.';

	public function __construct( int $id, string $name, string $slug, string $taxonomy = 'scs_destination' ) {
		$this->term_id  = $id;
		$this->name     = $name;
		$this->slug     = $slug;
		$this->taxonomy = $taxonomy;
	}
}

class WP_Post {
	public $ID;
	public $post_title;
	public $post_name;
	public $post_status;
	public $post_password = '';

	public function __construct( int $id, string $title, string $slug, string $status ) {
		$this->ID          = $id;
		$this->post_title  = $title;
		$this->post_name   = $slug;
		$this->post_status = $status;
	}
}

function __( $value, $domain = '' ) {
	return $value;
}
function wp_strip_all_tags( $value ) {
	return strip_tags( $value );
}
function wp_attachment_is_image( $id ) {
	return 10 === $id;
}
function number_format_i18n( $value, $decimals ) {
	return number_format( $value, $decimals );
}
function get_term( $id, $taxonomy ) {
	return 2 === $id ? new WP_Term( 2, 'Mara', 'mara', $taxonomy ) : null;
}
function get_term_by( $field, $slug, $taxonomy ) {
	if ( 'mara' === $slug || 'unfinished' === $slug ) {
		return new WP_Term( 'mara' === $slug ? 2 : 3, 'Destination', $slug, $taxonomy );
	}
	return false;
}
function get_term_meta( $id, $key ) {
	return 'scs_term_ready' === $key && 2 === $id ? '1' : '';
}
function sanitize_title_for_query( $value ) {
	return strtolower( preg_replace( '/[^a-zA-Z0-9-]/', '', $value ) );
}
function get_page_by_path( $slug, $output, $type ) {
	if ( 'enquire' === $slug && isset( $GLOBALS['enquiry_page'] ) ) {
		return $GLOBALS['enquiry_page'];
	}
	if ( 'approved' === $slug || 'internal-draft' === $slug ) {
		return new WP_Post( 8, 'Reviewed safari', $slug, 'approved' === $slug ? 'publish' : 'draft' );
	}
	if ( 'password-only' === $slug ) {
		$post = new WP_Post( 9, 'Protected safari', $slug, 'publish' );
		$post->post_password = 'private';
		return $post;
	}
	return null;
}
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function current_user_can( $capability ) {
	return in_array( $capability, $GLOBALS['enquiry_capabilities'] ?? array(), true );
}
function add_option( $key, $value, $deprecated, $autoload ) {
	if ( isset( $GLOBALS['enquiry_options'][ $key ] ) ) {
		return false;
	}
	$GLOBALS['enquiry_options'][ $key ] = $value;
	return true;
}
function delete_option( $key ) {
	unset( $GLOBALS['enquiry_options'][ $key ] );
}
function is_wp_error( $value ) {
	return false;
}
function wp_insert_post( $page, $report_errors ) {
	$GLOBALS['enquiry_insert_attempts']++;
	if ( ! empty( $GLOBALS['enquiry_insert_fails'] ) ) {
		return 0;
	}
	$GLOBALS['enquiry_page'] = new WP_Post( 80, $page['post_title'], $page['post_name'], $page['post_status'] );
	return 80;
}
function get_option( $name ) {
	return array( 'id' => 77, 'context_kind' => 'hidden-1', 'context_id' => 'hidden-2', 'context_title' => 'hidden-3' );
}
function wp_unslash( $value ) {
	return $value;
}
function sanitize_text_field( $value ) {
	return strip_tags( $value );
}

$plugin = dirname( __DIR__ ) . '/wp-content/plugins/scs-core/src/';
require $plugin . 'content.php';
require $plugin . 'safari.php';
require $plugin . 'catalogue.php';
require $plugin . 'planner.php';

use function SavannahCrest\Core\context_from_query;
use function SavannahCrest\Core\context_from_token;
use function SavannahCrest\Core\ensure_enquiry_page;
use function SavannahCrest\Core\filter_query_args;
use function SavannahCrest\Core\form_settings;
use function SavannahCrest\Core\itinerary_error;
use function SavannahCrest\Core\itinerary_route;
use function SavannahCrest\Core\parse_filters;
use function SavannahCrest\Core\planner_entry_context;
use function SavannahCrest\Core\price_label;
use function SavannahCrest\Core\publication_errors;
use function SavannahCrest\Core\valid_rate_date;

$count = 0;
function check( $actual, $expected, string $message ): void {
	global $count;
	++$count;
	if ( $actual !== $expected ) {
		throw new RuntimeException( $message . ': ' . var_export( $actual, true ) );
	}
}

$day = array( 'heading' => 'Start', 'narrative' => '<p>Travel</p>', 'destination' => 2 );
$days = array(
	array( 'day_from' => 1, 'day_to' => 2 ) + $day,
	array( 'day_from' => 3, 'day_to' => '' ) + $day,
);
$fields = array(
	'scs_duration_days' => '3', 'scs_primary_destination' => '2', 'scs_start_location' => 'Start',
	'scs_end_location' => 'End', 'scs_itinerary' => $days, 'scs_includes' => array( array( 'label' => 'Included' ) ),
	'scs_excludes' => array( array( 'label' => 'Excluded' ) ),
	'scs_price_mode' => 'range', 'scs_price_from' => '2150', 'scs_price_to' => '3100',
	'scs_price_currency' => 'USD', 'scs_price_basis' => 'per_person_sharing', 'scs_price_note' => 'Example terms.',
	'scs_price_valid_until' => '20261121',
);
check( publication_errors( $fields, array( 2 ), 10, 'Test safari' ), array(), 'Complete safari should publish' );
check( count( publication_errors( array(), array(), 0 ) ), 5, 'Incomplete safari has specific publish failures' );
check( count( publication_errors( $fields, array(), 10, 'Test safari' ) ), 1, 'Primary place must be assigned' );
check( itinerary_error( $days, 3 ), '', 'Grouped and single days cover the trip' );
check( itinerary_error( $days, 4 ) !== '', true, 'Reject gaps at the end' );
check( itinerary_error( array( $days[1], $days[0] ), 3 ) !== '', true, 'Reject out-of-order days' );
check( itinerary_error( array( array_replace( $days[0], array( 'day_to' => 3 ) ), $days[1] ), 3 ) !== '', true, 'Reject overlap' );
check( itinerary_error( array( array_replace( $days[0], array( 'destination' => 88 ) ), $days[1] ), 3 ) !== '', true, 'Reject unknown day destination' );
check( itinerary_route( $days ), array( 'Mara' ), 'Adjacent identical destinations collapse' );
check( price_label( $fields, '20261121' ), 'USD 2,150–3,100 per person sharing', 'Rate remains active through expiry date' );
check( price_label( $fields, '20261122' ), 'Price on request', 'Expired rate resolves on read' );
$undated_fields = $fields;
unset( $undated_fields['scs_price_valid_until'] );
check( publication_errors( $undated_fields, array( 2 ), 10, 'Test safari' ), array(), 'A missing validity date permits publication' );
check( price_label( $undated_fields, '20261122' ), 'USD 2,150–3,100 per person sharing', 'Undated range stays visible' );
$undated_from = array_replace( $fields, array( 'scs_price_mode' => 'from', 'scs_price_valid_until' => '' ) );
check( publication_errors( $undated_from, array( 2 ), 10, 'Test safari' ), array(), 'A blank validity date permits publication' );
check( price_label( $undated_from, '20261122' ), 'From USD 2,150 per person sharing', 'Undated starting price stays visible' );
$invalid_expiry = array_replace( $fields, array( 'scs_price_valid_until' => '20270229' ) );
check( count( publication_errors( $invalid_expiry, array( 2 ), 10, 'Test safari' ) ), 1, 'Reject an invalid supplied expiry date' );
check( price_label( $invalid_expiry, '20261122' ), 'Price on request', 'An invalid date cannot turn a rate into an undated one' );
check( price_label( array_replace( $undated_fields, array( 'scs_price_mode' => 'on_request' ) ), '20261122' ), 'Price on request', 'Quote-only mode ignores stored rates' );
check( price_label( array_replace( $fields, array( 'scs_price_to' => 900 ) ), '20261121' ), 'Price on request', 'Reversed range stays hidden' );
check( price_label( array_replace( $fields, array( 'scs_price_mode' => 'from', 'scs_price_to' => -1 ) ), '20261121' ), 'From USD 2,150 per person sharing', 'Inactive upper bound is ignored' );
check( valid_rate_date( '2028-02-29' ), '20280229', 'Valid leap day' );
check( valid_rate_date( '2027-02-29' ), '', 'Invalid calendar dates are rejected' );

$inventory = array(
	'where' => array( 'mara' => get_term( 2, 'scs_destination' ) ),
	'experience' => array( 'family' => new WP_Term( 4, 'Family', 'family', 'scs_experience' ) ),
	'comfort' => array(),
);
$controls = parse_filters( array( 'where' => 'mara', 'experience' => 'family', 'duration' => '5-7', 'sort' => 'shortest' ), $inventory );
$args = filter_query_args( $controls, $inventory );
check( $args['tax_query']['relation'], 'AND', 'Facets combine with AND' );
check( $args['tax_query'][0]['include_children'], true, 'Geography includes descendants' );
check( $args['tax_query'][1]['include_children'], false, 'Experiences are exact matches' );
check( $args['meta_query']['duration_days']['value'], array( 5, 7 ), 'Numeric boundaries are inclusive' );
check( $args['orderby'], array( 'duration_days' => 'ASC', 'ID' => 'ASC' ), 'Sort has a stable tie-breaker' );
check( parse_filters( array( 'sort' => 'newest' ), $inventory )['values'], array(), 'Default sort is omitted' );
check( filter_query_args( parse_filters( array( 'duration' => '8-plus' ), $inventory ), $inventory )['meta_query']['duration_days']['value'], 8, 'Long duration has a lower bound' );
check( parse_filters( array( 'comfort' => 'luxury' ), $inventory )['invalid'], true, 'Disabled comfort input is rejected' );
check( filter_query_args( parse_filters( array( 'where' => array( 'mara' ) ), $inventory ), $inventory )['post__in'], array( 0 ), 'Array filter cannot broaden the query' );

check( context_from_query( array( 'trip' => 'approved' ) )['id'], 8, 'Accept public itinerary context' );
check( context_from_query( array( 'trip' => 'internal-draft' ) ), array(), 'Draft context is hidden' );
check( context_from_query( array( 'trip' => 'password-only' ) ), array(), 'Password-protected context is hidden' );
check( context_from_query( array( 'trip' => 'approved', 'destination' => 'mara' ) ), array(), 'Ambiguous context is rejected' );
check( context_from_query( array( 'destination' => 'unfinished' ) ), array(), 'Unready term context is hidden' );
check( context_from_token( 'destination:mara' )['id'], 2, 'Resolve a ready destination' );
check( context_from_token( array( 'trip:approved' ) ), array(), 'Reject malformed submitted token' );
check( form_settings( array( 'id' => '12', 'context_kind' => 'email-1' ) )['context_kind'], '', 'Only hidden fields configurable' );
$_POST['_scs_planner_context'] = 'trip:approved';
$entry = array( array( 'name' => 'email-1', 'value' => 'private@example.test' ), array( 'name' => 'hidden-3', 'value' => 'Spoof' ) );
$saved = array_column( planner_entry_context( $entry, 77 ), 'value', 'name' );
check( $saved['hidden-3'], 'Reviewed safari', 'Server replaces attacker-controlled title' );
check( $saved['hidden-2'], '8', 'Server supplies the authoritative ID' );
check( planner_entry_context( $entry, 88 ), $entry, 'Other forms are untouched' );
$_POST['_scs_planner_context'] = 'trip:internal-draft';
check( array_column( planner_entry_context( $entry, 77 ), 'value', 'name' )['hidden-3'], '', 'Unpublished context stays redacted at submission' );

$GLOBALS['enquiry_insert_attempts'] = 0;
ensure_enquiry_page();
check( $GLOBALS['enquiry_insert_attempts'], 0, 'Unprivileged visits do not create the Enquire page' );
$GLOBALS['enquiry_capabilities'] = array( 'manage_options', 'publish_pages' );
$GLOBALS['enquiry_insert_fails'] = true;
ensure_enquiry_page();
$GLOBALS['enquiry_insert_fails'] = false;
ensure_enquiry_page();
ensure_enquiry_page();
check( $GLOBALS['enquiry_insert_attempts'], 2, 'Provisioning can retry after failure and does not duplicate a successful insert' );
check( $GLOBALS['enquiry_page']->post_name, 'enquire', 'Provision the dedicated Enquire route' );
check( $GLOBALS['enquiry_page']->post_status, 'publish', 'The provisioned Enquire page is public' );
delete_option( 'scs_enquiry_page_provisioned' );
$GLOBALS['enquiry_page'] = new WP_Post( 81, 'Existing enquiry', 'enquire', 'draft' );
ensure_enquiry_page();
check( $GLOBALS['enquiry_insert_attempts'], 2, 'Preserve an existing page with the Enquire slug' );
check( $GLOBALS['enquiry_page']->post_status, 'draft', 'Leave the existing enquiry publication status to its editor' );

echo "Standalone safari regression checks passed: {$count}.\n";
