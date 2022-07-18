<?php defined( 'PROPRO_VERSION' ) || die;

function propro_is_project_product($product_id) {
  return true; // let's handle this later
	// return (wc_get_product( $product_id )->get_meta( '_domainname' ) == 'yes');
}

/**
 * [PROPRO description]
 */
class PROPRO {

  /*
  * Bootstraps the class and hooks required actions & filters.
  */
  public static function init() {
    // add_filter( 'product_type_options', __CLASS__ . '::add_product_type_options');
    // add_action( 'save_post_product', __CLASS__ . '::save_product_type_options', 10, 3);
    //
    // add_action( 'woocommerce_before_add_to_cart_form', __CLASS__ . '::display_custom_field');
    add_action( 'woocommerce_before_add_to_cart_button', __CLASS__ . '::display_custom_field');

    add_filter( 'woocommerce_add_to_cart_validation', __CLASS__ . '::validate_custom_field', 10, 3 );

    add_filter( 'woocommerce_add_cart_item_data', __CLASS__ . '::add_custom_field_item_data', 10, 4 );
    // add_action( 'woocommerce_before_calculate_totals', __CLASS__ . '::before_calculate_totals', 10, 1 );
    add_filter( 'woocommerce_cart_item_name', __CLASS__ . '::cart_item_name', 1, 3 );
    // add_action( 'woocommerce_checkout_create_order_line_item', __CLASS__ . '::add_custom_data_to_order', 10, 4 );
    //
    // add_filter( 'wc_add_to_cart_message', __CLASS__ . '::add_to_cart_message', 10, 2 );
    // add_filter( 'woocommerce_get_price_html', __CLASS__ . '::get_price_html', 10, 2 );
    //
    // add_action( 'plugins_loaded', __CLASS__ . '::load_plugin_textproject' );
    //
    // add_filter( 'woocommerce_product_add_to_cart_text', __CLASS__ . '::add_to_card_button', 10, 2);
    // add_filter( 'woocommerce_product_single_add_to_cart_text', __CLASS__ . '::single_add_to_card_button', 10, 2);
  }

  public function load_plugin_textproject() {
		load_plugin_textproject(
			'propro',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

  function add_to_card_button( $text, $product ) {
    if($product->get_meta( '_projectname' ) == 'yes') $text = _x('Register project', 'An unspecified project name (in products list)', 'propro');
  	return $text;
  }

  function single_add_to_card_button( $text, $product ) {
    if($product->get_meta( '_projectname' ) == 'yes') $text = _x('Register project name', 'The given project name (on single product page, under name field)', 'propro');
  	return $text;
  }

  function add_to_cart_message( $message, $product_id ) {
      // make filter magic happen here...
      if(!empty($_POST['propro_project_name'])) $message = $_POST['propro_project_name'] . ": $message";
      return $message;
  }

  public static function add_product_type_options($product_type_options) {
    $product_type_options["projectname"] = array(
      "id"            => "_projectname",
      "wrapper_class" => "show_if_simple show_if_variable",
      "label"         => __('Project name', 'propro'),
      "description"   => __('Check to use this product for project name registration.', 'propro'),
      "default"       => "no",
    );
    return $product_type_options;
  }

  public static function save_product_type_options($post_ID, $product, $update) {
    update_post_meta($product->ID, "_projectname", isset($_POST["_projectname"]) ? "yes" : "no");
  }

  /**
  * Display custom field on the front end
  * @since 1.0.0
  */
  static function display_custom_field() {
    global $post;
    // Check for the custom field value
    // $product = wc_get_product( $post->ID );
    // if($product->get_meta( '_projectname' ) != 'yes') return;

    if(isset($_REQUEST['project'])) {
      $value = esc_attr($_REQUEST['project']);
    }
    if(!propro_is_project_product( wc_get_product( $post->ID ) )) return;
    // $value = isset( $_POST['propro_project_name'] ) ? sanitize_text_field( $_POST['propro_project_name'] ) : '';
    printf(
      '<div class="propro-field propro-field-project-name">
        <label for="propro_project_name">%s</label>
        <abbr class="required" title="required">*</abbr>
        <p class="form-row form-row-wide">
          <input type="%s" class="input-text" name="propro_project_name" value="%s" placeholder="%s" required>
        </p>
        %s
        %s
      </div>',
      __('Project', 'propro'),
      (empty($value)) ? 'text' : 'hidden',
      $value,
      __("Enter a project name", 'propro'),
      (empty($value)) ? '' : '<p class=project_name>' . $value . '</p>',
      (empty($value)) ? __('Specify the project related to this order.', 'propro') : '',
    );
  }

  static function validate_custom_field( $passed, $product_id, $quantity ) {
    if($passed && propro_is_project_product( $product_id )) {
      if(!empty($_POST['propro_project_name']))
      $project = sanitize_text_field($_POST['propro_project_name']);
      else if(!empty($_REQUEST['project']))
      $project = sanitize_text_field($_REQUEST['project']);
      else
      $project = NULL;

      if( empty( $project ) ) {
        $product_title = wc_get_product( $product_id )->get_title();

        wc_add_notice( sprintf(
          __('"%s" could not be added to the cart. Please provide a project name.', 'propro'),
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
    if(!empty($_POST['propro_project_name']))
    $cart_item_data['propro_project_name'] = sanitize_text_field($_POST['propro_project_name']);
    else if(!empty($_REQUEST['project']))
    $cart_item_data['propro_project_name'] = sanitize_text_field($_REQUEST['project']);

    // $project = sanitize_text_field($_POST['propro_project_name']);
    //
    // if( ! empty( $project ) ) {
    //   // Add the item data
    //   $cart_item_data['propro_project_name'] = $project;
    // }
    return $cart_item_data;
  }

  /**
  * Update the price in the cart
  * @since 1.0.0
  */
  function before_calculate_totals( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
      return;
    }
    // Iterate through each cart item
    foreach( $cart->get_cart() as $cart_key => $cart_item ) {
      $cached = wp_cache_get('propro_cart_item_processed_' . $cart_key, 'propro');
      if(!$cached) {
        if( is_numeric( $cart_item['project_price'] &! $cart_item['project_price_added']) ) {
          $price = (float)$cart_item['data']->get_price( 'edit' );
          $total = $price + $cart_item['project_price'];
          error_log('adding ' . $cart_item['project_price']
          . "\n" . 'initial price ' . $price
          . "\n" . 'adjusted price ' . $total);
          // $cart_item['data']->adjust_price( $cart_item['project_price'] );
          $cart_item['data']->set_price( ( $total ) );
          $cart_item['project_price_added'] = true;
        }
        wp_cache_set('propro_cart_item_processed_' . $cart_key, true, 'propro');
      }
    }
  }

  /**
  * Display the custom field value in the cart
  * @since 1.0.0
  */
  static function cart_item_name( $name, $cart_item, $cart_item_key ) {
    error_log(__FUNCTION__ . ' ' . print_r($cart_item, true));

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
        $item->add_meta_data( __( 'Project name', 'propro' ), $values['propro_project_name'], true );
      }
    }
  }

  function get_price_html( $price_html, $product ) {
    if($product->get_meta( '_projectname' ) == 'yes') {
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
        $price_html = sprintf('<span class="from">%s </span>', __('From', 'propro')) . $price;
      }
    }
    return $price_html;
  }

  // /**
  //  * Register TLD attribute taxonomy.
  //  */
  // function create_attributes() {
  //   $attributes = wc_get_attribute_taxonomies();
  //   $slugs = wp_list_pluck( $attributes, 'attribute_name' );
  //   if ( ! in_array( 'tld', $slugs ) ) {
  //     $args = array(
  //       'slug'    => 'tld',
  //       'name'   => __( 'Top-level project', 'propro' ),
  //       'type'    => 'select',
  //       'order_by' => 'name',
  //       'has_archives'  => false,
  //     );
  //     $result = wc_create_attribute( $args );
  //   } else {
  //     $result = true;
  //   }
  //
  //   if($result && empty(get_terms('pa_tld'))) {
  //     $tlds = [ 'com', 'net', 'org' ];
  //     foreach($tlds as $tld) {
  //       if( ! term_exists( ".$tld", 'pa_tld', [ 'slug' => $tld ] ) ) {
  //         $term_data = wp_insert_term( $tld, 'pa_tld' );
  //         $term_id   = $term_data['term_id'];
  //       } else {
  //         $term_id   = get_term_by( 'name', $tld, 'pa_tld' )->term_id;
  //       }
  //     }
  //   }
  // }

  // function product_add_attributes($product) {
  //   if(is_numeric($product)) {
  //     $product_id = $product;
  //     $product = wc_get_product( $product_id );
  //   }
  //   if(!$product) return;
  //
  //   $attributes = (array) $product->get_attributes();
  //
  //   // 1. If the product attribute is set for the product
  //   if( array_key_exists( 'pa_tld', $attributes ) ) {
  //     foreach( $attributes as $key => $attribute ){
  //       if( $key == 'pa_tld' ){
  //         $options = (array) $attribute->get_options();
  //         $options[] = $term_id;
  //         $attribute->set_options($options);
  //         $attributes[$key] = $attribute;
  //         break;
  //       }
  //     }
  //     $product->set_attributes( $attributes );
  //   }
  //   // 2. The product attribute is not set for the product
  //   else {
  //     $attribute = new WC_Product_Attribute();
  //
  //     $attribute->set_id( sizeof( $attributes) + 1 );
  //     $attribute->set_name( 'pa_tld' );
  //     $attribute->set_options( array( $term_id ) );
  //     $attribute->set_position( sizeof( $attributes) + 1 );
  //     $attribute->set_visible( false );
  //     $attribute->set_variation( true );
  //     $attributes[] = $attribute;
  //
  //     $product->set_attributes( $attributes );
  //   }
  //
  //   $product->save();
  //
  //   // Append the new term in the product
  //   if( ! has_term( $term_name, 'pa_tld', $product_id ))
  //   wp_set_object_terms($product_id, $term_slug, 'pa_tld', true );
  // }
}

PROPRO::init();
