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
if(!defined('WCPAA_VERSION')) {
  define('WCPAA_VERSION', '0.1.0');
  define('WCPAA_PLUGIN_NAME', 'WooCommerce Product Add-ons Autofill');
  define('WCPAA_PLUGIN', plugin_basename(__FILE__));
  define('WCPAA_SLUG', dirname(WCPAA_PLUGIN));

  require(plugin_dir_path(__FILE__) . 'vendor/autoload.php');
  if(is_admin()) require(plugin_dir_path(__FILE__) . 'admin/settings.php');
}
