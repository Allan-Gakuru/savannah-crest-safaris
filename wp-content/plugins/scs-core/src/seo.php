<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

function queried_destination_guide(): array {
	if ( ! is_tax( DESTINATION ) ) {
		return array();
	}
	$term = get_queried_object();
	return $term instanceof \WP_Term ? destination_guide( $term ) : array();
}

function destination_document_title( array $parts ): array {
	$guide = queried_destination_guide();
	if ( $guide ) {
		$parts['title'] = $guide['title'];
	}
	return $parts;
}

function destination_meta_description( string $description ): string {
	$guide = queried_destination_guide();
	return '' === $description && $guide ? $guide['teaser'] : $description;
}

function destination_core_meta(): void {
	if ( defined( 'WPSEO_VERSION' ) ) {
		return;
	}
	$guide = queried_destination_guide();
	if ( ! $guide ) {
		return;
	}
	$canonical = get_term_link( get_queried_object() );
	if ( ! is_wp_error( $canonical ) ) {
		printf( "<link rel=\"canonical\" href=\"%s\">\n", esc_url( $canonical ) );
	}
	printf( "<meta name=\"description\" content=\"%s\">\n", esc_attr( $guide['teaser'] ) );
}

function noindex_route(): bool {
	if ( is_page( array( PLANNER_SLUG, ENQUIRY_SLUG, 'thank-you' ) ) || is_search() || ( is_post_type_archive( SAFARI ) && has_catalogue_request() ) ) {
		return true;
	}
	if ( is_tax( array( DESTINATION, EXPERIENCE ) ) ) {
		$term = get_queried_object();
		return $term instanceof \WP_Term && ! hub_ready( $term );
	}
	return false;
}

function robots( array $rules ): array {
	if ( ! noindex_route() ) {
		return $rules;
	}
	if ( current_filter() === 'wpseo_robots_array' ) {
		$rules['index']  = 'noindex';
		$rules['follow'] = 'follow';
	} else {
		unset( $rules['index'], $rules['nofollow'] );
		$rules['noindex'] = true;
		$rules['follow']  = true;
	}
	return $rules;
}

function term_sitemap( array $args, string $taxonomy ): array {
	if ( ! in_array( $taxonomy, array( DESTINATION, EXPERIENCE ), true ) ) {
		return $args;
	}
	$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
	$excluded = ids( $args['exclude'] ?? array() );
	foreach ( is_wp_error( $terms ) ? array() : $terms as $term ) {
		if ( ! hub_ready( $term ) ) {
			$excluded[] = $term->term_id;
		}
	}
	$args['exclude'] = array_unique( $excluded );
	$args['hide_empty'] = false;
	return $args;
}

function page_sitemap( array $args, string $post_type ): array {
	if ( 'page' !== $post_type ) {
		return $args;
	}
	$excluded = ids( $args['post__not_in'] ?? array() );
	foreach ( array( PLANNER_SLUG, ENQUIRY_SLUG, 'thank-you' ) as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page instanceof \WP_Post ) {
			$excluded[] = $page->ID;
		}
	}
	$args['post__not_in'] = array_unique( $excluded );
	return $args;
}

function yoast_sitemap_entry( $entry, $type, $object ) {
	if ( $object instanceof \WP_Term && in_array( $object->taxonomy, array( DESTINATION, EXPERIENCE ), true ) && ! hub_ready( $object ) ) {
		return false;
	}
	if ( $object instanceof \WP_Post && 'page' === $object->post_type && in_array( $object->post_name, array( PLANNER_SLUG, ENQUIRY_SLUG, 'thank-you' ), true ) ) {
		return false;
	}
	return $entry;
}

function yoast_sitemap_hide_empty_terms( bool $hide_empty, string $taxonomy ): bool {
	// The sitemap entry filter still screens destination guides for readiness.
	return DESTINATION === $taxonomy ? false : $hide_empty;
}
