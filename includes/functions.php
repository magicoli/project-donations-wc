<?php defined( 'PRDWC_VERSION' ) || die;

function prdwc_is_donation($product_id) {
  // return true; // let's handle this later
	return (wc_get_product( $product_id )->get_meta( '_linkproject' ) == 'yes');
}

function prdwc_allow_custom_amount() {
	if(get_option('prdwc_custom_amount') == 'yes') return true;
	return false;
}
