<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

const PLANNER_SLUG = 'plan-your-safari';
const ENQUIRY_SLUG = 'enquire';
const CONTEXT_TYPES = array( 'trip', 'destination', 'experience' );
const FORM_OPTION   = 'scs_planner_form';

add_action( 'admin_menu', __NAMESPACE__ . '\add_planner_settings' );
add_filter( 'forminator_render_form_submit_markup', __NAMESPACE__ . '\planner_hidden_context', 10, 2 );
add_filter( 'forminator_custom_form_submit_field_data', __NAMESPACE__ . '\planner_entry_context', 10, 2 );

function planner_url( string $kind = '', string $slug = '' ): string {
	$url = page_url( PLANNER_SLUG );
	return $url && $slug && in_array( $kind, CONTEXT_TYPES, true ) ? add_query_arg( $kind, $slug, $url ) : $url;
}

function ensure_enquiry_page(): void {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'publish_pages' ) ) {
		return;
	}
	$page_option = 'scs_enquiry_page_provisioned';
	if ( ! add_option( $page_option, '1', '', false ) ) {
		return;
	}
	if ( get_page_by_path( ENQUIRY_SLUG, OBJECT, array( 'page' ) ) ) {
		return;
	}
	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_name'   => ENQUIRY_SLUG,
		'post_title'  => __( 'Enquire', 'scs-core' ),
		'post_status' => 'publish',
	), true );
	if ( ! $page_id || is_wp_error( $page_id ) ) {
		delete_option( $page_option );
	}
}

function context_from_query( array $request ): array {
	$keys = array_intersect( CONTEXT_TYPES, array_keys( $request ) );
	if ( 1 !== count( $keys ) ) {
		return array();
	}
	$kind = reset( $keys );
	$slug = $request[ $kind ];
	if ( ! is_string( $slug ) || '' === $slug || strlen( $slug ) > 200 || sanitize_title_for_query( $slug ) !== $slug ) {
		return array();
	}
	if ( 'trip' === $kind ) {
		$post = get_page_by_path( $slug, OBJECT, SAFARI );
		if ( ! $post instanceof \WP_Post || 'publish' !== $post->post_status || '' !== $post->post_password ) {
			return array();
		}
		return array( 'kind' => $kind, 'slug' => $post->post_name, 'id' => (int) $post->ID, 'title' => $post->post_title );
	}
	$taxonomy = 'destination' === $kind ? DESTINATION : EXPERIENCE;
	$term = get_term_by( 'slug', $slug, $taxonomy );
	if ( ! $term instanceof \WP_Term || ! hub_ready( $term ) ) {
		return array();
	}
	return array( 'kind' => $kind, 'slug' => $term->slug, 'id' => (int) $term->term_id, 'title' => $term->name );
}

function active_context(): array {
	static $context;
	if ( null === $context ) {
		$context = context_from_query( wp_unslash( $_GET ) );
	}
	return $context;
}

function exclude_enquiry_forms_from_cache(): void {
	if ( ! is_page( array( PLANNER_SLUG, ENQUIRY_SLUG ) ) ) {
		return;
	}
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}
	nocache_headers();
}

function form_settings( $inputs ): array {
	$inputs = is_array( $inputs ) ? $inputs : array();
	$config = array( 'id' => positive_int( $inputs['id'] ?? null ) );
	foreach ( array( 'context_kind', 'context_id', 'context_title' ) as $key ) {
		$value = text( $inputs[ $key ] ?? '' );
		$config[ $key ] = preg_match( '/^hidden-[1-9][0-9]*$/D', $value ) ? $value : '';
	}
	return $config;
}

function configured_form(): array {
	$settings = form_settings( get_option( FORM_OPTION ) );
	$fields   = array( $settings['context_kind'], $settings['context_id'], $settings['context_title'] );
	return $settings['id'] && ! in_array( '', $fields, true ) && 3 === count( array_unique( $fields ) ) ? $settings : array();
}

function register_planner_settings(): void {
	register_setting( FORM_OPTION, FORM_OPTION, array( 'type' => 'array', 'sanitize_callback' => __NAMESPACE__ . '\form_settings' ) );
}

function add_planner_settings(): void {
	add_options_page( __( 'Safari Planner', 'scs-core' ), __( 'Safari Planner', 'scs-core' ), 'manage_options', FORM_OPTION, __NAMESPACE__ . '\planner_settings_page' );
}

function planner_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$settings = form_settings( get_option( FORM_OPTION ) );
	$fields   = array(
		'id'            => __( 'Published Forminator form ID', 'scs-core' ),
		'context_kind'  => __( 'Context type hidden field', 'scs-core' ),
		'context_id'    => __( 'Context ID hidden field', 'scs-core' ),
		'context_title' => __( 'Context title hidden field', 'scs-core' ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Safari Planner', 'scs-core' ); ?></h1>
		<p><?php esc_html_e( 'Use three distinct Hidden fields in the planner form. Enter their field IDs, such as hidden-1. Configure entries, notifications and spam protection in Forminator.', 'scs-core' ); ?></p>
		<form action="options.php" method="post">
			<?php settings_fields( FORM_OPTION ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( $fields as $key => $label ) : ?>
					<tr><th scope="row"><label for="scs-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
						<td><input class="regular-text" type="<?php echo 'id' === $key ? 'number' : 'text'; ?>" id="scs-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( FORM_OPTION . '[' . $key . ']' ); ?>" value="<?php echo esc_attr( (string) $settings[ $key ] ); ?>"></td></tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

function planner_hidden_context( string $html, $form_id ): string {
	$settings = configured_form();
	if ( ! $settings || $settings['id'] !== (int) $form_id || ! is_page( PLANNER_SLUG ) ) {
		return $html;
	}
	$context = active_context();
	$value = $context ? $context['kind'] . ':' . $context['slug'] : '';
	return $html . '<input type="hidden" name="_scs_planner_context" value="' . esc_attr( $value ) . '">';
}

function context_from_token( $raw ): array {
	if ( ! is_string( $raw ) || false === strpos( $raw, ':' ) ) {
		return array();
	}
	list( $kind, $slug ) = explode( ':', $raw, 2 );
	return context_from_query( array( $kind => $slug ) );
}

function planner_entry_context( array $data, $form_id ): array {
	$settings = configured_form();
	if ( ! $settings || $settings['id'] !== (int) $form_id ) {
		return $data;
	}
	$context = context_from_token( wp_unslash( $_POST['_scs_planner_context'] ?? '' ) );
	$replacement = array(
		$settings['context_kind']  => $context['kind'] ?? 'general',
		$settings['context_id']    => isset( $context['id'] ) ? (string) $context['id'] : '',
		$settings['context_title'] => sanitize_text_field( $context['title'] ?? '' ),
	);
	foreach ( $data as $index => $entry ) {
		$name = $entry['name'] ?? '';
		if ( isset( $replacement[ $name ] ) ) {
			$data[ $index ]['value'] = $replacement[ $name ];
			unset( $replacement[ $name ] );
		}
	}
	foreach ( $replacement as $name => $value ) {
		$data[] = array( 'name' => $name, 'value' => $value );
	}
	return $data;
}
