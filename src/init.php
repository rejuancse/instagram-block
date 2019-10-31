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

define( 'QUBELY_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

function qubelyinsblock_block_assets() {
	wp_enqueue_style(
		'qubelyinsblock-style-css',
		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ),
		array( 'wp-editor' )
	);
} 
add_action( 'enqueue_block_assets', 'qubelyinsblock_block_assets' );

function qubelyinsblock_editor_assets() {
	wp_enqueue_script(
		'qubelyinsblock-block-js',
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ),
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
		true 
	);

	wp_enqueue_style(
		'qubelyinsblock-block-editor-css',
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ),
		array( 'wp-edit-blocks' ) 
	);
}
add_action( 'enqueue_block_editor_assets', 'qubelyinsblock_editor_assets' );


require_once( QUBELY_PLUGIN_PATH . './qubely-insblock.php' );

