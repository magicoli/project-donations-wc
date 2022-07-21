<?php defined( 'PROPRO_VERSION' ) || die;

function propro_is_project_product($product_id) {
  // return true; // let's handle this later
	return (wc_get_product( $product_id )->get_meta( '_linkproject' ) == 'yes');
}

/**
 * [PROPRO description]
 */
class PROPRO {

  /*
  * Bootstraps the class and hooks required actions & filters.
  */
  public static function init() {
		// Add project option to product edit page
    add_filter( 'product_type_options', __CLASS__ . '::add_product_type_options');
    add_action( 'save_post_product', __CLASS__ . '::save_product_type_options', 10, 3);

		// Add project field to product page
		add_action( 'woocommerce_before_add_to_cart_button', __CLASS__ . '::display_custom_fields_simple');
		add_action( 'woocommerce_before_variations_form', __CLASS__ . '::display_custom_fields');

		// Update product name in cart
    add_filter( 'woocommerce_add_to_cart_validation', __CLASS__ . '::validate_custom_field', 10, 3 );
    add_filter( 'woocommerce_add_cart_item_data', __CLASS__ . '::add_custom_field_item_data', 10, 4 );
    add_filter( 'woocommerce_cart_item_name', __CLASS__ . '::cart_item_name', 1, 3 );
		// add_filter( 'wc_add_to_cart_message', __CLASS__ . '::add_to_cart_message', 10, 2 );

		add_filter( 'woocommerce_get_price_html', __CLASS__ . '::get_price_html', 10, 2 );
		// add_filter( 'woocommerce_variation_sale_price_html', __CLASS__ . '::get_variation_price_html', 10, 2 );
		// add_filter( 'woocommerce_variation_price_html', __CLASS__ . '::get_variation_price_html', 10, 2 );
		add_filter( 'woocommerce_available_variation', __CLASS__ . '::get_variation_price_html', 10, 3);

		add_action( 'woocommerce_before_calculate_totals', __CLASS__ . '::before_calculate_totals', 10, 1 );

		add_action( 'woocommerce_checkout_create_order_line_item', __CLASS__ . '::add_custom_data_to_order', 10, 4 );

		// Set pay button text
    add_filter( 'woocommerce_product_add_to_cart_text', __CLASS__ . '::add_to_card_button', 10, 2);
    add_filter( 'woocommerce_product_single_add_to_cart_text', __CLASS__ . '::single_add_to_card_button', 10, 2);

		add_action( 'plugins_loaded', __CLASS__ . '::load_plugin_textdomain' );
  }

  static function load_plugin_textdomain() {
		load_plugin_textdomain(
			'project-products',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

  static function add_to_card_button( $text, $product ) {
    if($product->get_meta( '_linkproject' ) == 'yes') $text = __('Support project', 'project-products');
  	return $text;
  }

  static function single_add_to_card_button( $text, $product ) {
    if($product->get_meta( '_linkproject' ) == 'yes') $text = __('Support project', 'project-products');
  	return $text;
  }

  static function add_to_cart_message( $message, $product_id ) {
      // make filter magic happen here...
      if(!empty($_POST['propro_project_name'])) $message = $_POST['propro_project_name'] . ": $message";
      return $message;
  }

  static function add_product_type_options($product_type_options) {
    $product_type_options['linkproject'] = array(
      "id"            => "_linkproject",
      "wrapper_class" => "show_if_simple show_if_variable",
      "label"         => __('Project Product', 'project-products'),
      "description"   => __('Check to add a project field to product page.', 'project-products'),
      "default"       => "no",
    );
    return $product_type_options;
  }

  public static function save_product_type_options($post_ID, $product, $update) {
    update_post_meta($product->ID, "_linkproject", isset($_POST["_linkproject"]) ? "yes" : "no");
  }

	static function display_custom_fields_simple() {
		global $post, $product;
		if( $product->has_child() ) return; // already called by woocommerce_before_variations_form
		if(!propro_is_project_product( $post->ID )) return;

		PROPRO::display_custom_fields();
	}

  /**
  * Display custom field on the front end
  * @since 1.0.0
  */
  static function display_custom_fields() {
		global $post, $product;
		if(!propro_is_project_product( $post->ID )) return;

    $project = (isset($_REQUEST['project'])) ? esc_attr($_REQUEST['project']) : NULL;
		$price = $product->get_price();
		$amount = (isset($_REQUEST['amount'])) ? esc_attr($_REQUEST['amount']) : ((isset($_REQUEST['nyp'])) ? esc_attr($_REQUEST['nyp']) : NULL);

		printf('<div class="propro-fields" style="margin-bottom:1rem">');
		printf(
      '<div class="propro-field propro-field-project-name">
				<p class="form-row form-row-wide">
        	<label for="propro_project_name" class="required">%s%s</label>
          <input type="%s" class="input-text" name="propro_project_name" value="%s" placeholder="%s" required>
        </p>
      </div>',
      __('Project', 'project-products'),
      (empty($project)) ? ' <abbr class="required" title="required">*</abbr>' : ': <span class=project_name>' . $project . '</span>',
      (empty($project)) ? 'text' : 'hidden',
      $project,
      __("Enter a project name", 'project-products'),
    );

		printf(
		  '<div class="propro-field propro-field-amount">
		    <p class="form-row form-row-wide">
		      <label for="propro_amount" class="required">%s%s</label>
		      <input type="number" class="input-text" name="propro_amount" value="%s" placeholder="%s" step="any" required>
		    </p>
		  </div>',
		  ($price > 0 ) ? __('Add to fee', 'lodgify-link') : __('Amount', 'lodgify-link'),
		  ' <abbr class="required" title="required">*</abbr>',
		  $amount,
		  ($price > 0 ) ? __("Amount to add", 'lodgify-link') : __("Amount to pay", 'lodgify-link'),
		);
		printf('</div>');
  }

  static function validate_custom_field( $passed, $product_id, $quantity ) {
    if($passed && propro_is_project_product( $product_id )) {
      if(!empty($_POST['propro_project_name'])) $project = sanitize_text_field($_POST['propro_project_name']);
      else if(!empty($_REQUEST['project'])) $project = sanitize_text_field($_REQUEST['project']);
      else $project = NULL;

			if(!empty($_POST['propro_amount'])) $amount = sanitize_text_field($_POST['propro_amount']);
      else if(!empty($_REQUEST['amount'])) $amount = sanitize_text_field($_REQUEST['amount']);
      else $amount = 0;

			$product = wc_get_product( $product_id );
			$price = $product->get_price();

			if(!is_numeric($amount) || $amount + $price <= 0) {
				$product_title = $product->get_title();
				wc_add_notice( sprintf(
          __('"%s" could not be added to the cart. Please provide a valid amount to pay.', 'lodgify-link'),
          sprintf('<a href="%s">%s</a>', get_permalink($product_id), $product_title),
        ), 'error' );
        return false;
			}

      if( empty( $project ) ) {
        $product_title = wc_get_product( $product_id )->get_title();

        wc_add_notice( sprintf(
          __('"%s" could not be added to the cart. Please provide a project name.', 'project-products'),
          sprintf('<a href="%s">%s</a>', get_permalink($product_id), $product_title),
        ), 'error' );
        return false;
      }
    }
    return $passed;
  }

  /**
  * Add the text field as item data to the cart object
  * @since 1.0.0
  * @param Array $cart_item_data Cart item meta data.
  * @param Integer $product_id Product ID.
  * @param Integer $variation_id Variation ID.
  * @param Boolean $quantity Quantity
  */
  static function add_custom_field_item_data( $cart_item_data, $product_id, $variation_id, $quantity ) {
		if(!propro_is_project_product( wc_get_product( $product_id ) )) return $cart_item_data;

    if(!empty($_POST['propro_project_name'])) $cart_item_data['propro_project_name'] = sanitize_text_field($_POST['propro_project_name']);
    else if(!empty($_REQUEST['project'])) $cart_item_data['propro_project_name'] = sanitize_text_field($_REQUEST['project']);

		if(!empty($_POST['propro_amount'])) $cart_item_data['propro_amount'] = sanitize_text_field($_POST['propro_amount']);
    else if(!empty($_REQUEST['amount'])) $cart_item_data['propro_amount'] = sanitize_text_field($_REQUEST['amount']);
		else if(!empty($_REQUEST['nyp'])) $cart_item_data['propro_amount'] = sanitize_text_field($_REQUEST['nyp']);

    return $cart_item_data;
  }

  /**
  * Display the custom field value in the cart
  * @since 1.0.0
  */
  static function cart_item_name( $name, $cart_item, $cart_item_key ) {
    // error_log(__FUNCTION__ . ' ' . print_r($cart_item, true));

    if( isset( $cart_item['propro_project_name'] ) ) {
      $name = sprintf(
      '%s <span class=propro-project-name>"%s"</span>',
      $name,
      esc_html( $cart_item['propro_project_name'] ),
      );
    }
    return $name;
  }

  /**
  * Add custom field to order object
  */
  function add_custom_data_to_order( $item, $cart_item_key, $values, $order ) {
    foreach( $item as $cart_item_key=>$values ) {
      if( isset( $values['propro_project_name'] ) ) {
        $item->add_meta_data( __( 'Project', 'project-products' ), $values['propro_project_name'], true );
      }
    }
  }

  static function get_price_html( $price_html, $product ) {
    if($product->get_meta( '_linkproject' ) == 'yes') {
      $price = max($product->get_price(), get_option('propro_project_minimum_price', 0));
      if( $price == 0 ) {
        $price_html = apply_filters( 'woocommerce_empty_price_html', '', $product );
      } else {
        if ( $product->is_on_sale() && $product->get_price() >= $price ) {
          $price = wc_format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ),
          wc_get_price_to_display( $product ) ) . $product->get_price_suffix();
        } else {
          $price = wc_price( $price ) . $product->get_price_suffix();
        }
        $price_html = sprintf('<span class="from">%s </span>', __('From', 'project-products')) . $price;
      }
    }
    return $price_html;
  }

	static function get_variation_price_html( $data, $product, $variation ) {
		// if(propro_is_project_product( $product->get_id() ))
		if($data['display_price'] > 0) {
			$data['price_html'] = sprintf(
				__('%s will be added to the chosen amount, the total price will be calculated before checkout.', 'project-products'),
				wp_strip_all_tags($data['price_html']),
			);
		} else {
			$data['price_html'] = " ";
		}
		// $data['price_html'] = $data['price_html'] . "<pre>" . print_r($data, true) . "</pre>";

		return $data;
	}

	/**
  * Update the price in the cart
  * @since 1.0.0
  */
  static function before_calculate_totals( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
      return;
    }
    // Iterate through each cart item
    foreach( $cart->get_cart() as $cart_key => $cart_item ) {
      $cached = wp_cache_get('propro_cart_item_processed_' . $cart_key, 'project-products');
      if(!$cached) {
        if( isset($cart_item['propro_amount']) &! isset($cart_item['propro_amount_added']) ) {
					// $cart_item['data']->adjust_price( $cart_item['propro_amount'] );
          $price = (float)$cart_item['data']->get_price( 'edit' );
          $total = isset($cart_item['propro_amount']) ? $price + $cart_item['propro_amount'] : $price;
          $cart_item['data']->set_price( ( $total ) );
          $cart_item['propro_amount_added'] = true;
        }
        wp_cache_set('propro_cart_item_processed_' . $cart_key, true, 'project-products');
      }
    }
  }
}

PROPRO::init();
