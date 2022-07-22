<?php defined( 'ABSPATH' ) || die;
/**
 * Plugin Name:     Project Donations for WooCommerce
 * Plugin URI:      https://magiiic.com/wordpress/plugins/project-donations/
 * Description:     Add project field to WooCommerce products, allow clients to link their purchase to a project
 * Author:          Magiiic
 * Author URI:      https://magiiic.com/
 * Text Domain:     project-donations-wc
 * Domain Path:     /languages
 * Version:         1.4.1
 *
 * @package         Product_Addons_Autofill
 *
 * Icon1x: https://github.com/magicoli/project-donations-wc/raw/master/assets/icon-128x128.jpg
 * Icon2x: https://github.com/magicoli/project-donations-wc/raw/master/assets/icon-256x256.jpg
 * BannerHigh: https://github.com/magicoli/project-donations-wc/raw/master/assets/banner-1544x500.jpg
 * BannerLow: https://github.com/magicoli/project-donations-wc/raw/master/assets/banner-772x250.jpg
 */

// Your code starts here.
if(!defined('PRDWC_VERSION')) {
  define('PRDWC_VERSION', '1.4.1');
  define('PRDWC_PLUGIN', plugin_basename(__FILE__));
  define('PRDWC_SLUG', dirname(PRDWC_PLUGIN));
  // define('PRDWC_PLUGIN_NAME', 'Project Donations for WooCommerce');

  require(plugin_dir_path(__FILE__) . 'includes/classes.php');
  // if(is_admin()) require(plugin_dir_path(__FILE__) . 'admin/wc-admin-classes.php');

  if(is_admin()) require_once(__DIR__ . '/admin/wc-admin-classes.php');

  if(file_exists(plugin_dir_path( __FILE__ ) . 'lib/package-updater.php'))
  include_once plugin_dir_path( __FILE__ ) . 'lib/package-updater.php';
}
