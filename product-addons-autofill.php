<?php defined( 'ABSPATH' ) || die;

/**
 * Plugin Name:     WooCommerce Product Add-ons Autofill
 * Plugin URI:      https://github.com/magicoli/product-addons-autofill
 * Description:     Autofill WooCommerce product add-ons fields with values passed from the URL
 * Author:          Magiiic
 * Author URI:      https://magiiic.com/
 * Text Domain:     product-addons-autofill
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Product_Addons_Autofill
 */

// Your code starts here.
if(!defined('WPAA_VERSION')) {
  define('WPAA_VERSION', '0.1.0');
  define('WPAA_PLUGIN_NAME', 'WooCommerce Product Add-ons Autofill');
  define('WPAA_PLUGIN_SLUG', 'product-addons-autofill');

  require(plugin_dir_path(__FILE__) . 'vendor/autoload.php');
}
