<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

require_once DIR . '/src/presentation.php';

const VIEWS = array( 'site-action', 'home-browser', 'catalogue', 'safari-detail', 'hub', 'directory', 'stay-details', 'guide-notes', 'planner' );

function register_blocks(): void {
	wp_register_script( 'scs-block-editor', plugins_url( 'assets/editor.js', FILE ), array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-data', 'wp-dom-ready', 'wp-i18n' ), VERSION, true );
	$definitions = array();
	foreach ( VIEWS as $view ) {
		$block = register_block_type( DIR . '/blocks/' . $view . '/block.json', array(
			'render_callback' => static function ( array $attributes, string $content, \WP_Block $block ) use ( $view ): string {
				ob_start();
				require DIR . '/views/' . $view . '.php';
				return ob_get_clean();
			},
		) );
		if ( $block instanceof \WP_Block_Type ) {
			$definitions[] = array(
				'name' => $block->name,
				'title' => $block->title,
				'description' => $block->description,
				'category' => $block->category,
				'attributes' => (object) $block->attributes,
				'supports' => (object) $block->supports,
			);
		}
	}
	wp_add_inline_script( 'scs-block-editor', 'window.scsBlocks = ' . wp_json_encode( $definitions, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
}

function block_post( \WP_Block $block, string $type ): ?\WP_Post {
	$id = (int) ( $block->context['postId'] ?? get_queried_object_id() );
	$post = $id ? get_post( $id ) : null;
	if ( ! $post instanceof \WP_Post || $type !== $post->post_type || post_password_required( $post ) ) {
		return null;
	}
	if ( 'publish' !== $post->post_status && ! current_user_can( 'edit_post', $id ) ) {
		return null;
	}
	return $post;
}
