<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/** Enable plugin updates **/
require_once plugin_dir_path( dirname(__FILE__) ) . 'lib/wp-package-updater/class-wp-package-updater.php';
$prdwc_updater = new WP_Package_Updater(
	'https://magiiic.com',
	wp_normalize_path( plugin_dir_path( dirname(__FILE__) ) . "/project-donations.php" ),
	wp_normalize_path( plugin_dir_path( dirname(__FILE__) ) )
);
