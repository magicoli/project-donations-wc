<?php
/**
 * Plugin Name: Meta Box Template
 * Plugin URI:  http://metabox.io/plugins/meta-box-template
 * Description: Configure meta boxes easily via YAML templates.
 * Version:     1.2.0
 * Author:      MetaBox.io
 * Author URI:  https://metabox.io
 * License:     GPL2+
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || die;

if ( file_exists( __DIR__ . '/vendor' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

define( 'MB_TEMPLATE_URL', plugin_dir_url( __FILE__ ) );

if ( is_admin() ) {
	new MBTemplate\Settings;
}
new MBTemplate\Register;
