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
	public $parent = 0;
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

class WP_Error {}

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
	if ( 'scs_destination' === $taxonomy && isset( $GLOBALS['destination_test_terms'][ $slug ] ) ) {
		return $GLOBALS['destination_test_terms'][ $slug ];
	}
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
	if ( 'scs_safari' === $type && isset( $GLOBALS['planner_test_trips'][ $slug ] ) ) {
		return $GLOBALS['planner_test_trips'][ $slug ];
	}
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
	return $value instanceof WP_Error;
}
function get_posts( $args ) {
	return array( 8 );
}
function get_terms( $args ) {
	return 'scs_destination' === $args['taxonomy'] ? $GLOBALS['destination_test_inventory'] : array();
}
function get_ancestors( $id, $taxonomy, $type ) {
	return array();
}
function get_term_link( WP_Term $term ) {
	foreach ( $GLOBALS['destination_test_terms'] ?? array() as $parent ) {
		if ( $parent->term_id === $term->parent ) {
			return get_term_link( $parent ) . $term->slug . '/';
		}
	}
	return '/destinations/' . $term->slug . '/';
}
function wp_insert_term( $name, $taxonomy, $options ) {
	$term = new WP_Term( 100 + count( $GLOBALS['destination_test_terms'] ), $name, $options['slug'], $taxonomy );
	$term->parent = $options['parent'];
	$GLOBALS['destination_test_terms'][ $term->slug ] = $term;
	return array( 'term_id' => $term->term_id );
}
function has_term( $term_id, $taxonomy, $post_id ) {
	return in_array( $term_id, $GLOBALS['curated_test_assignments'][ $post_id ] ?? array(), true );
}
function wp_add_object_terms( $post_id, $term_id, $taxonomy ) {
	if ( ( $GLOBALS['curated_test_failed_post'] ?? 0 ) === $post_id ) {
		return new WP_Error();
	}
	$GLOBALS['curated_test_assignments'][ $post_id ][] = $term_id;
	return array( $term_id );
}
function wp_insert_post( $page, $report_errors ) {
	$GLOBALS['enquiry_insert_attempts']++;
	if ( ! empty( $GLOBALS['enquiry_insert_fails'] ) ) {
		return 0;
	}
	$GLOBALS['enquiry_page'] = new WP_Post( 80, $page['post_title'], $page['post_name'], $page['post_status'] );
	return 80;
}
function get_option( $name, $default = false ) {
	return 'scs_planner_form' === $name
		? array( 'id' => 77, 'context_kind' => 'hidden-1', 'context_id' => 'hidden-2', 'context_title' => 'hidden-3' )
		: ( $GLOBALS['curated_test_options'][ $name ] ?? $default );
}
function update_option( $name, $value, $autoload ) {
	$GLOBALS['curated_test_options'][ $name ] = $value;
}
function wp_unslash( $value ) {
	return $value;
}
function sanitize_text_field( $value ) {
	return trim( strip_tags( $value ) );
}
function is_page( $slug ) {
	return $slug === ( $GLOBALS['planner_test_page'] ?? '' );
}
function get_field( $name, $id ) {
	return $GLOBALS['planner_test_fields'][ $id ][ $name ] ?? false;
}
function get_post_meta( $id ) {
	return $GLOBALS['planner_test_form_meta'][ $id ] ?? array();
}
function maybe_unserialize( $value ) {
	return is_string( $value ) ? unserialize( $value ) : $value;
}
function wp_get_post_terms( $id, $taxonomy, $args ) {
	return $GLOBALS['planner_test_experiences'][ $id ][ $args['fields'] ] ?? array();
}
function esc_html( $value ) {
	return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8', false );
}

$plugin = dirname( __DIR__ ) . '/wp-content/plugins/scs-core/src/';
require $plugin . 'content.php';
require $plugin . 'safari.php';
require $plugin . 'catalogue.php';
require $plugin . 'destinations.php';
require $plugin . 'planner.php';
require $plugin . 'planner-trip.php';
require $plugin . 'seo.php';

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
use function SavannahCrest\Core\safari_experience_choices;
use function SavannahCrest\Core\safari_priority_choices;
use function SavannahCrest\Core\tailor_planner_form;
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

$GLOBALS['destination_test_terms'] = array(
	'botswana' => new WP_Term( 20, 'Botswana', 'botswana' ),
	'kenya' => new WP_Term( 21, 'Kenya', 'kenya' ),
	'tanzania' => new WP_Term( 22, 'Tanzania', 'tanzania' ),
	'zanzibar' => new WP_Term( 23, 'Zanzibar', 'zanzibar' ),
	'uganda' => new WP_Term( 24, 'Uganda', 'uganda' ),
);
$GLOBALS['destination_test_inventory'] = array_intersect_key( $GLOBALS['destination_test_terms'], array_flip( array( 'botswana', 'kenya', 'zanzibar' ) ) );
$countries = SavannahCrest\Core\country_guides();
check( array_keys( $countries ), array( 'kenya', 'tanzania', 'botswana' ), 'List stocked countries once, with Kenya first and Zanzibar included in Tanzania' );
check( SavannahCrest\Core\hub_ready( $countries['kenya'] ), true, 'A stocked country with a versioned guide can be indexed and linked' );
check( SavannahCrest\Core\hub_ready( $GLOBALS['destination_test_terms']['uganda'] ), false, 'A country guide without live inventory does not enable a hub' );
check( SavannahCrest\Core\browse_term_url( $countries['kenya'], 'where', '/safaris/' ), '/destinations/kenya/', 'A country card links to its guide rather than the filtered catalogue' );
check( SavannahCrest\Core\term_clause( $countries['tanzania'] )['terms'], array( 22, 23 ), 'Tanzania trips include the existing Zanzibar term' );
check( SavannahCrest\Core\term_clause( $GLOBALS['destination_test_terms']['zanzibar'] )['terms'], array( 23 ), 'Zanzibar retains its own narrower trip query' );
check( context_from_query( array( 'destination' => 'tanzania' ) )['id'], 22, 'A country guide starts an enquiry with destination context' );

$GLOBALS['destination_test_terms']['south-africa'] = new WP_Term( 25, 'South Africa', 'south-africa' );
$mara_term = new WP_Term( 26, 'Existing Maasai Mara', 'maasai-mara' );
$mara_term->parent = 21;
$GLOBALS['destination_test_terms']['maasai-mara'] = $mara_term;
$guides = SavannahCrest\Core\curated_destination_guides();
check( array_keys( $guides ), array( 'kruger-national-park', 'serengeti-national-park', 'victoria-falls', 'okavango-delta', 'maasai-mara' ), 'The curated homepage order matches the intended routes' );
$kruger = $guides['kruger-national-park']['seed_trip'];
$serengeti = $guides['serengeti-national-park']['seed_trip'];
$okavango = $guides['okavango-delta']['seed_trip'];
$GLOBALS['planner_test_trips'] = array(
	$kruger => new WP_Post( 101, 'Kruger', $kruger, 'publish' ),
	$serengeti => new WP_Post( 102, 'Serengeti', $serengeti, 'publish' ),
	$okavango => new WP_Post( 103, 'Okavango', $okavango, 'draft' ),
);
$GLOBALS['curated_test_assignments'] = array( 101 => array( 25 ), 102 => array( 22 ), 103 => array( 20 ) );
$GLOBALS['curated_test_failed_post'] = 102;
SavannahCrest\Core\provision_curated_destinations();
$kruger_term = get_term_by( 'slug', 'kruger-national-park', 'scs_destination' );
check( $kruger_term->parent, 25, 'Kruger is created beneath South Africa' );
check( $GLOBALS['curated_test_assignments'][101], array( 25, $kruger_term->term_id ), 'Initial setup appends the park while preserving the country assignment' );
check( $GLOBALS['curated_test_assignments'][102], array( 22 ), 'A failed assignment is left for retry' );
check( $GLOBALS['curated_test_assignments'][103], array( 20 ), 'Setup never attaches a draft itinerary' );
check( get_term_by( 'slug', 'maasai-mara', 'scs_destination' ), $mara_term, 'Setup preserves the existing Mara term' );
$falls = get_term_by( 'slug', 'victoria-falls', 'scs_destination' );
check( $falls->parent, 0, 'Victoria Falls has a cross-border destination route' );
check( context_from_query( array( 'destination' => 'victoria-falls' ) )['id'], $falls->term_id, 'Victoria Falls accepts contextual enquiries without a listed tour' );
check( SavannahCrest\Core\browse_term_url( $kruger_term, 'where', '/safaris/' ), '/destinations/south-africa/kruger-national-park/', 'Curated links resolve to their hierarchical guide' );
check( SavannahCrest\Core\yoast_sitemap_hide_empty_terms( true, 'scs_destination' ), false, 'An unstocked curated destination is eligible for the sitemap' );
check( SavannahCrest\Core\yoast_sitemap_entry( array( 'loc' => '/destinations/victoria-falls/' ), 'term', $falls ), array( 'loc' => '/destinations/victoria-falls/' ), 'Victoria Falls passes the published guide sitemap rule' );
$GLOBALS['curated_test_failed_post'] = 0;
$GLOBALS['planner_test_trips'][ $okavango ]->post_status = 'publish';
SavannahCrest\Core\provision_curated_destinations();
check( count( $GLOBALS['curated_test_assignments'][102] ), 2, 'A failed attachment can be retried' );
check( count( $GLOBALS['curated_test_assignments'][103] ), 2, 'An itinerary can be assigned once it becomes public' );
$GLOBALS['curated_test_assignments'][101] = array( 25 );
SavannahCrest\Core\provision_curated_destinations();
check( $GLOBALS['curated_test_assignments'][101], array( 25 ), 'Subsequent requests preserve later editorial edits' );

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

function planner_test_form_meta( bool $extra_question = false ): array {
	$schema = array(
		'radio-1' => 'radio', 'page-break-1' => 'page-break', 'number-1' => 'number',
		'page-break-2' => 'page-break', 'number-2' => 'number', 'page-break-3' => 'page-break', 'radio-2' => 'radio',
		'page-break-4' => 'page-break', 'checkbox-1' => 'checkbox',
		'page-break-5' => 'page-break', 'checkbox-2' => 'checkbox', 'text-2' => 'text',
	);
	if ( $extra_question ) {
		$schema['text-99'] = 'text';
	}
	$schema += array( 'page-break-6' => 'page-break', 'date-1' => 'date', 'email-1' => 'email' );
	$fields = array();
	foreach ( $schema as $id => $type ) {
		$fields[ $id ] = array( 'id' => $id, 'type' => $type );
	}
	$fields['radio-2']['required'] = true;
	$fields['radio-2']['options'] = array( array( 'label' => 'Wildlife', 'value' => 'wildlife' ) );
	$fields['checkbox-1']['required'] = false;
	$fields['checkbox-1']['options'] = array( array( 'label' => 'Walking safaris', 'value' => 'walking' ) );
	$fields['page-break-1']['conditions'] = array( array( 'element_id' => 'radio-1', 'value' => 'solo' ) );
	return array( 'fields' => array_values( $fields ), 'settings' => array( 'pagination' => 'show' ) );
}

$amboseli = '3-day-amboseli-private-escapade';
$rwanda = '4-day-rwanda-gorilla-and-golden-monkey-trekking-safari';
$GLOBALS['planner_test_trips'] = array(
	$amboseli => new WP_Post( 91, 'Amboseli Private Escapade', $amboseli, 'publish' ),
	$rwanda => new WP_Post( 92, 'Rwanda Gorilla and Golden Monkey Trekking Safari', $rwanda, 'publish' ),
);
$general = planner_test_form_meta();
$GLOBALS['planner_test_form_meta'][77] = array( 'forminator_form_meta' => array( serialize( $general ) ) );
$_POST = array();
$_GET = array( 'trip' => $amboseli );
$GLOBALS['planner_test_page'] = 'enquire';
check( tailor_planner_form( null, 77, 'forminator_form_meta', true ), null, 'The general enquiry uses the original form' );

$GLOBALS['planner_test_page'] = 'plan-your-safari';
$tour = tailor_planner_form( null, 77, 'forminator_form_meta', true )[0];
$tour_fields = array_column( $tour['fields'], null, 'id' );
$priority = $tour_fields['radio-2'];
$amboseli_choices = array_column( $priority['options'], 'value' );
check( $amboseli_choices, array( 'More time watching elephants', 'Wildlife photography', 'A Maasai community visit', 'I’d like your advice' ), 'Tour links show the matching trip priorities' );
check( $priority['required'], true, 'The tailored priority question stays required' );
check( array_column( $tour_fields['checkbox-1']['options'], 'value' ), array( 'Game drives in Amboseli', 'Elephant watching', 'Photographing Kilimanjaro when skies are clear', 'Maasai village visit' ), 'The optional checklist offers specific activities from Amboseli' );
check( $tour_fields['checkbox-1']['required'], false, 'The tailored experience question stays optional' );
check(
	array_column( $tour['fields'], 'id' ),
	array( 'radio-1', 'page-break-1', 'number-1', 'page-break-2', 'number-2', 'page-break-3', 'radio-2', 'page-break-4', 'checkbox-1', 'page-break-6', 'date-1', 'email-1' ),
	'Remove exactly the destination page and its questions'
);
check( $tour['fields'][1], $general['fields'][1], 'The solo skip rule stays intact' );
check( $tour['settings'], $general['settings'], 'Tailoring leaves Forminator settings intact' );
check( get_post_meta( 77 )['forminator_form_meta'][0], serialize( $general ), 'Tailoring leaves the stored form intact' );
check( tailor_planner_form( null, 88, 'forminator_form_meta', true ), null, 'Leave unrelated forms untouched' );

$GLOBALS['planner_test_page'] = '';
$_POST = array( 'action' => 'forminator_submit_form_custom-forms', 'form_id' => '77', '_scs_planner_context' => 'trip:' . $rwanda );
$submitted = tailor_planner_form( null, 77, 'forminator_form_meta', true )[0];
$submitted_fields = array_column( $submitted['fields'], null, 'id' );
$rwanda_choices = array_column( $submitted_fields['radio-2']['options'], 'value' );
$rwanda_experiences = array_column( $submitted_fields['checkbox-1']['options'], 'value' );
check( in_array( 'Golden monkey trekking', $rwanda_choices, true ), true, 'AJAX validation gets the options for the submitted itinerary' );
check( array_intersect( $amboseli_choices, $rwanda_choices ), array( 3 => 'I’d like your advice' ), 'Different itineraries offer different choices' );
check( $rwanda_experiences, array( 'Mountain gorilla trekking', 'Golden monkey trekking', 'Visiting the Kigali Genocide Memorial' ), 'AJAX validation gets Rwanda’s experience choices' );
$selected_experiences = array_slice( $rwanda_experiences, 0, 2 );
$entry = array(
	array( 'name' => 'radio-2', 'value' => $rwanda_choices[1] ),
	array( 'name' => 'checkbox-1', 'value' => $selected_experiences ),
);
$saved = array_column( planner_entry_context( $entry, 77 ), 'value', 'name' );
check( $saved['radio-2'], 'Golden monkey trekking', 'The saved entry retains the selected text' );
check( $saved['checkbox-1'], $selected_experiences, 'The saved entry retains both selected experiences' );
check( $saved['hidden-3'], 'Rwanda Gorilla and Golden Monkey Trekking Safari', 'The form saves the authoritative tour title' );

$changed = planner_test_form_meta( true );
$GLOBALS['planner_test_form_meta'][77]['forminator_form_meta'][0] = serialize( $changed );
$tailored = tailor_planner_form( null, 77, 'forminator_form_meta', true )[0];
check( array_column( $tailored['fields'], 'id' ), array_column( $changed['fields'], 'id' ), 'Preserve a destination page if an editor added an unrelated question' );

foreach ( array( 'trip:internal-draft', 'trip:unknown', array( 'trip:' . $amboseli ) ) as $token ) {
	$_POST['_scs_planner_context'] = $token;
	check( tailor_planner_form( null, 77, 'forminator_form_meta', true ), null, 'Invalid tour context keeps the general questions' );
}
$_POST = array();
$_GET = array();

$trip_data = context_from_query( array( 'trip' => $amboseli ) );
$GLOBALS['planner_test_fields'][91]['scs_enquiry_priorities'] = "A private &lt;b&gt;photography&lt;/b&gt; drive\r\n\r\nA private photography drive\nBush breakfast\nI’d like your advice";
check( safari_priority_choices( $trip_data ), array( 'A private photography drive', 'Bush breakfast', 'I’d like your advice' ), 'The editor can replace the suggestions with distinct plain text choices' );
$new_trip = context_from_query( array( 'trip' => 'approved' ) );
check( safari_experience_choices( $new_trip ), array( 'I’d like suggestions for this itinerary' ), 'An itinerary without tags still offers a relevant option' );
$GLOBALS['planner_test_experiences'][8] = array( 'slugs' => array( 'walking-cycling' ), 'names' => array( 'Walking and cycling' ) );
check( safari_experience_choices( $new_trip ), array( 'Walking and cycling' ), 'New itineraries use their assigned experiences' );
check( safari_priority_choices( $new_trip )[0], 'More time walking or cycling', 'New itineraries get priorities based on their experiences' );

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
