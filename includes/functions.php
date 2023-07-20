<?php defined( 'PRDWC_VERSION' ) || die;

/**
 * Main functions file for Project Donations for WooCommerce.
 *
 * @package project-donations-wc
 * @link            https://github.com/magicoli/project-donations-wc
 * @version 1.5.6-rc
 */

/**
 * Checks if a product is a donation product.
 *
 * @since   1.4
 *
 * @param int $product_id The ID of the product.
 * @return bool Whether the product is a donation product or not.
 */
function prdwc_is_donation( $product_id ) {
	// return true; // let's handle this later
	return ( wc_get_product( $product_id )->get_meta( '_linkproject' ) == 'yes' );
}

/**
 * Checks if custom donation amount is allowed.
 *
 * @since   1.4.4
 *
 * @return bool Whether custom donation amount is allowed or not.
 */
function prdwc_allow_custom_amount() {
	if ( get_option( 'prdwc_custom_amount' ) == 'yes' ) {
		return true;
	}
	return false;
}
