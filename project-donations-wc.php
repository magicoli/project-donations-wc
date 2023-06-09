<?php defined( 'ABSPATH' ) || die;
/**
 * Plugin Name:     Project Donations for WooCommerce
 * Plugin URI:      https://wordpress.org/plugins/project-donations-wc/
 * Description:     Add project field to WooCommerce products, allow clients to link their purchase to a project
 * Author:          Magiiic
 * Author URI:      https://magiiic.com/
 * Text Domain:     project-donations-wc
 * Domain Path:     /languages
 * Version:         1.5.2
 *
 * @package         project-donations-wc
 *
 * Icon1x: https://ps.w.org/project-donations-wc/assets/icon-128x128.jpg
 * Icon2x: https://ps.w.org/project-donations-wc/assets/icon-256x256.jpg
 * BannerHigh: https://ps.w.org/project-donations-wc/assets/banner-1544x500.jpg
 * BannerLow: https://ps.w.org/project-donations-wc/assets/banner-772x250.jpg
 */

// Your code starts here.
if(!defined('PRDWC_VERSION')) {
  define('PRDWC_VERSION', '1.5.2');
  define('PRDWC_PLUGIN', plugin_basename(__FILE__));
  define('PRDWC_SLUG', dirname(PRDWC_PLUGIN));
  // define('PRDWC_PLUGIN_NAME', 'Project Donations for WooCommerce');

  require(plugin_dir_path(__FILE__) . 'includes/classes.php');
  // if(is_admin()) require(plugin_dir_path(__FILE__) . 'admin/wc-admin-classes.php');

  if(is_admin()) require_once(__DIR__ . '/admin/wc-admin-classes.php');

  if( file_exists(__DIR__ . '/lib/wp-package-updater-lib/package-updater.php') ) {
    $wppul_server="https://magiiic.com";
    include_once( __DIR__ . '/lib/wp-package-updater-lib/package-updater.php' );
  }
}
