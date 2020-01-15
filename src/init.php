<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 */

# Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'INSBLOCK_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

function insblockinsblock_block_assets() {
	wp_enqueue_style(
		'insblockinsblock-style-css',
		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ),
		array( 'wp-editor' )
	);
} 
add_action( 'enqueue_block_assets', 'insblockinsblock_block_assets' );

function insblockinsblock_editor_assets() {
	wp_enqueue_script(
		'insblockinsblock-block-js',
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ),
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
		true 
	);

	wp_enqueue_style(
		'insblockinsblock-block-editor-css',
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ),
		array( 'wp-edit-blocks' ) 
	);
}
add_action( 'enqueue_block_editor_assets', 'insblockinsblock_editor_assets' );


require_once( INSBLOCK_PLUGIN_PATH . './insblock-insblock.php' );

