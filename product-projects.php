<?php defined( 'ABSPATH' ) || die;
/**
 * Plugin Name:     Product Projects
 * Plugin URI:      https://github.com/magicoli/product-projects
 * Description:     Add project field to product page to allow clients to assign their purchase to a project
 * Author:          Magiiic
 * Author URI:      https://magiiic.com/
 * Text Domain:     product-projects
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Product_Addons_Autofill
 */

// Your code starts here.
if(!defined('PROPRO_VERSION')) {
  define('PROPRO_VERSION', '0.1.0');
  define('PROPRO_PLUGIN', plugin_basename(__FILE__));
  define('PROPRO_SLUG', dirname(PROPRO_PLUGIN));
  // define('PROPRO_PLUGIN_NAME', 'Product Projects');

  require(plugin_dir_path(__FILE__) . 'includes/classes.php');
}
