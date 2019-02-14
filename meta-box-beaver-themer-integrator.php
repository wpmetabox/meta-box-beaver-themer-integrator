<?php
/**
 * Plugin Name: Meta Box - Beaver Themer Integrator
 * Plugin URI:  https://metabox.io/plugins/meta-box-beaver-themer-integrator/
 * Description: Integrates Meta Box and Beaver Themer.
 * Author:      MetaBox.io
 * Author URI:  https://metabox.io
 * Text Domain: meta-box-beaver-themer-integrator
 * Domain Path: /languages
 * Version:     1.3.1
 *
 * @package    Meta Box
 * @subpackage Meta Box Beaver Themer Integrator
 */

require 'class-mbbti-base.php';

require 'class-mbbti-posts.php';
new MBBTI_Posts();

require 'class-mbbti-terms.php';
new MBBTI_Terms();

require 'class-mbbti-settings.php';
new MBBTI_Settings();

/**
 * Add support for Beaver Builder Conditional Logic.
 */
function mbbti_logic() {
	require 'class-mbbti-logic.php';
	$logic = new MBBTI_Logic();
	$logic->init();
}
add_action( 'bb_logic_init', 'mbbti_logic' );
