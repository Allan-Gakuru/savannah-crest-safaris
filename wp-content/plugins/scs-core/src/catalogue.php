<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

const FILTERS   = array( 'where', 'experience', 'duration', 'comfort', 'sort' );
const DURATIONS = array( '1-4' => array( 1, 4 ), '5-7' => array( 5, 7 ), '8-plus' => array( 8, null ) );
const PAGE_SIZE = 12;

function inventory_terms(): array {
	static $inventory;
	if ( null !== $inventory ) {
		return $inventory;
	}
	$inventory = array( 'where' => array(), 'experience' => array(), 'comfort' => array() );
	$safaris = get_posts( array(
		'post_type' => SAFARI, 'post_status' => 'publish', 'has_password' => false,
		'numberposts' => -1, 'fields' => 'ids', 'update_post_meta_cache' => false, 'update_post_term_cache' => false,
	) );
	if ( ! $safaris ) {
		return $inventory;
	}

	foreach ( array( 'where' => DESTINATION, 'experience' => EXPERIENCE, 'comfort' => COMFORT ) as $key => $taxonomy ) {
		$terms = get_terms( array( 'taxonomy' => $taxonomy, 'object_ids' => $safaris, 'hide_empty' => false ) );
		if ( is_wp_error( $terms ) ) {
			continue;
		}
		foreach ( $terms as $term ) {
			$inventory[ $key ][ $term->slug ] = $term;
			if ( DESTINATION !== $taxonomy ) {
				continue;
			}
			foreach ( get_ancestors( $term->term_id, DESTINATION, 'taxonomy' ) as $parent ) {
				$ancestor = get_term( $parent, DESTINATION );
				if ( $ancestor instanceof \WP_Term ) {
					$inventory[ $key ][ $ancestor->slug ] = $ancestor;
				}
			}
		}
		if ( DESTINATION === $taxonomy ) {
			foreach ( DESTINATION_REGIONS as $region => $country ) {
				if ( isset( $inventory[ $key ][ $region ] ) && ! isset( $inventory[ $key ][ $country ] ) ) {
					$country_term = get_term_by( 'slug', $country, DESTINATION );
					if ( $country_term instanceof \WP_Term ) {
						$inventory[ $key ][ $country ] = $country_term;
					}
				}
			}
		}
		uasort( $inventory[ $key ], static function ( \WP_Term $first, \WP_Term $second ): int {
			return strnatcasecmp( $first->name, $second->name );
		} );
	}
	if ( count( $inventory['comfort'] ) < 2 || ! apply_filters( 'scs_enable_comfort_filter', false ) ) {
		$inventory['comfort'] = array();
	}
	return $inventory;
}

function browse_term_url( \WP_Term $term, string $filter, string $catalogue ): string {
	if ( hub_ready( $term ) ) {
		$url = get_term_link( $term );
		if ( ! is_wp_error( $url ) ) {
			return $url;
		}
	}
	return $catalogue && isset( inventory_terms()[ $filter ][ $term->slug ] )
		? add_query_arg( $filter, $term->slug, $catalogue ) : '';
}

function parse_filters( array $query, array $inventory ): array {
	$allowed = array(
		'where'      => array_keys( $inventory['where'] ),
		'experience' => array_keys( $inventory['experience'] ),
		'comfort'    => array_keys( $inventory['comfort'] ),
		'duration'   => array_keys( DURATIONS ),
		'sort'       => array( 'shortest', 'newest' ),
	);
	$values = array();
	$invalid = false;
	foreach ( FILTERS as $filter ) {
		if ( ! array_key_exists( $filter, $query ) || '' === $query[ $filter ] ) {
			continue;
		}
		$value = $query[ $filter ];
		if ( ! is_string( $value ) || ! in_array( $value, $allowed[ $filter ], true ) ) {
			$invalid = true;
			continue;
		}
		if ( 'sort' !== $filter || 'newest' !== $value ) {
			$values[ $filter ] = $value;
		}
	}
	return array( 'values' => $values, 'invalid' => $invalid );
}

function current_filters(): array {
	static $filters;
	if ( null === $filters ) {
		$filters = parse_filters( wp_unslash( $_GET ), inventory_terms() );
	}
	return $filters;
}

function has_catalogue_request(): bool {
	return (bool) array_intersect( FILTERS, array_keys( $_GET ) );
}

function public_filter_vars( array $vars ): array {
	return array_merge( $vars, FILTERS );
}

function preserve_catalogue_controls( $redirect ) {
	// For the archive, keep invalid requests visible with their reset path and preserve filters on pagination.
	return is_post_type_archive( SAFARI ) && has_catalogue_request() ? false : $redirect;
}

function filter_query_args( array $filters, array $inventory ): array {
	$args = array( 'orderby' => array( 'date' => 'DESC', 'ID' => 'DESC' ) );
	if ( $filters['invalid'] ) {
		$args['post__in'] = array( 0 );
		return $args;
	}
	$values = $filters['values'];
	$terms = array( 'relation' => 'AND' );
	foreach ( array( 'where', 'experience', 'comfort' ) as $filter ) {
		if ( isset( $values[ $filter ] ) ) {
			$terms[] = term_clause( $inventory[ $filter ][ $values[ $filter ] ] );
		}
	}
	if ( count( $terms ) > 1 ) {
		$args['tax_query'] = $terms;
	}
	if ( isset( $values['duration'] ) || 'shortest' === ( $values['sort'] ?? '' ) ) {
		$bounds = DURATIONS[ $values['duration'] ?? '8-plus' ];
		$args['meta_query'] = array(
			'duration_days' => array( 'key' => 'scs_duration_days', 'type' => 'NUMERIC', 'compare' => 'EXISTS' ),
		);
		if ( isset( $values['duration'] ) ) {
			$args['meta_query']['duration_days']['compare'] = null === $bounds[1] ? '>=' : 'BETWEEN';
			$args['meta_query']['duration_days']['value']   = null === $bounds[1] ? $bounds[0] : $bounds;
		}
		if ( 'shortest' === ( $values['sort'] ?? '' ) ) {
			$args['orderby'] = array( 'duration_days' => 'ASC', 'ID' => 'ASC' );
		}
	}
	return $args;
}

function filter_catalogue( \WP_Query $query ): void {
	if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ! $query->is_main_query() || $query->is_search() || ! $query->is_post_type_archive( SAFARI ) ) {
		return;
	}
	$query->set( 'posts_per_page', PAGE_SIZE );
	$query->set( 'has_password', false );
	$query->set( 'post_status', 'publish' );
	$query->set( 'ignore_sticky_posts', true );
	foreach ( filter_query_args( current_filters(), inventory_terms() ) as $key => $value ) {
		$query->set( $key, $value );
	}
}

function public_posts( string $type, array $args = array() ): array {
	return get_posts( array_replace( array(
		'post_type' => $type, 'post_status' => 'publish', 'has_password' => false,
		'posts_per_page' => 3, 'orderby' => array( 'date' => 'DESC', 'ID' => 'DESC' ),
	), $args ) );
}

function matching_safaris( array $terms, array $pinned = array(), int $limit = 6, array $exclude = array() ): array {
	$base_query = array(
		'posts_per_page' => $limit, 'tax_query' => $terms, 'post__not_in' => $exclude,
	);
	$pinned = array_slice( array_values( array_diff( $pinned, $exclude ) ), 0, $limit );
	$picks = $pinned ? public_posts( SAFARI, array_replace( $base_query, array( 'post__in' => $pinned, 'orderby' => 'post__in' ) ) ) : array();
	if ( count( $picks ) >= $limit ) {
		return $picks;
	}
	$base_query['posts_per_page'] = $limit - count( $picks );
	$base_query['post__not_in']   = array_merge( $exclude, wp_list_pluck( $picks, 'ID' ) );
	return array_merge( $picks, public_posts( SAFARI, $base_query ) );
}

function term_clause( \WP_Term $term ): array {
	$ids = array( $term->term_id );
	foreach ( destination_regions( $term ) as $slug ) {
		$region = get_term_by( 'slug', $slug, DESTINATION );
		if ( $region instanceof \WP_Term ) {
			$ids[] = $region->term_id;
		}
	}
	return array( 'taxonomy' => $term->taxonomy, 'field' => 'term_id', 'terms' => $ids, 'include_children' => DESTINATION === $term->taxonomy );
}
