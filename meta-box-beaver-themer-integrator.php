<?php
/**
 * Plugin Name:      Meta Box - Beaver Themer Integrator
 * Plugin URI:       https://metabox.io/plugins/meta-box-beaver-themer-integrator/
 * Description:      Integrates Meta Box and Beaver Themer.
 * Author:           MetaBox.io
 * Author URI:       https://metabox.io
 * Text Domain:      meta-box-beaver-themer-integrator
 * Domain Path:      /languages
 * Version:          2.1.3
 * Requires Plugins: meta-box
 *
 * @package    Meta Box
 * @subpackage Meta Box Beaver Themer Integrator
 */

// Prevent loading this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

if ( file_exists( __DIR__ . '/vendor' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

new MBBTI\Posts;
new MBBTI\Terms;
new MBBTI\Settings;
new MBBTI\Authors;
new MBBTI\Users;
new MBBTI\ConditionalLogic;
