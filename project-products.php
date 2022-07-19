<?php defined( 'ABSPATH' ) || die;
/**
 * Plugin Name:     Project Products for WooCommerce
 * Plugin URI:      https://github.com/magicoli/project-products
 * Description:     Add project field to WooCommerce products, allow clients to link their purchase to a project
 * Author:          Magiiic
 * Author URI:      https://magiiic.com/
 * Text Domain:     project-products
 * Domain Path:     /languages
 * Version:         1.0
 *
 * @package         Product_Addons_Autofill
 */

// Your code starts here.
if(!defined('PROPRO_VERSION')) {
  define('PROPRO_VERSION', '1.0');
  define('PROPRO_PLUGIN', plugin_basename(__FILE__));
  define('PROPRO_SLUG', dirname(PROPRO_PLUGIN));
  // define('PROPRO_PLUGIN_NAME', 'Project Products for WooCommerce');

  require(plugin_dir_path(__FILE__) . 'includes/classes.php');
  // if(is_admin()) require(plugin_dir_path(__FILE__) . 'admin/wc-admin-classes.php');
}
