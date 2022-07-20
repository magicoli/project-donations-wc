<?php defined( 'ABSPATH' ) || die;
/**
 * Plugin Name:     Project Products for WooCommerce
 * Plugin URI:      https://magiiic.com/wordpress/plugins/project-products/
 * Description:     Add project field to WooCommerce products, allow clients to link their purchase to a project
 * Author:          Magiiic
 * Author URI:      https://magiiic.com/
 * Text Domain:     project-products
 * Domain Path:     /languages
 * Version:         1.1.1
 *
 * @package         Product_Addons_Autofill
 *
 * Icon1x: https://github.com/magicoli/project-products/raw/master/assets/icon-128x128.png
 * Icon2x: https://github.com/magicoli/project-products/raw/master/assets/icon-256x256.png
 * BannerHigh: https://github.com/magicoli/project-products/raw/master/assets/banner-1544x500.jpg
 * BannerLow: https://github.com/magicoli/project-products/raw/master/assets/banner-772x250.jpg
 */

// Your code starts here.
if(!defined('PROPRO_VERSION')) {
  define('PROPRO_VERSION', '1.0');
  define('PROPRO_PLUGIN', plugin_basename(__FILE__));
  define('PROPRO_SLUG', dirname(PROPRO_PLUGIN));
  // define('PROPRO_PLUGIN_NAME', 'Project Products for WooCommerce');

  require(plugin_dir_path(__FILE__) . 'includes/classes.php');
  // if(is_admin()) require(plugin_dir_path(__FILE__) . 'admin/wc-admin-classes.php');

  if(file_exists(plugin_dir_path( __FILE__ ) . 'lib/package-updater.php'))
  include_once plugin_dir_path( __FILE__ ) . 'lib/package-updater.php';
}
