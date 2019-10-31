<?php
/**
 * Plugin Name: Qubely Insblock
 * Description: Qubely Insblock is a Gutenberg plugin created via Instagram block.
 * Author: Themeum
 * Author URI: https://themeum.com/
 * Version: 1.0.0
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package Qubely Insblock
 */

# Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Block Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/init.php';
