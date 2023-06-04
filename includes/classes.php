<?php defined( 'PRDWC_VERSION' ) || die;

require(plugin_dir_path(__FILE__) . 'post-type-project.php');
require(plugin_dir_path(__FILE__) . 'product-options.php');

function prdwc_is_donation($product_id) {
  // return true; // let's handle this later
	return (wc_get_product( $product_id )->get_meta( '_linkproject' ) == 'yes');
}

function prdwc_allow_custom_amount() {
	if(get_option('prdwc_custom_amount') == 'yes') return true;
	return false;
}
/**
 * [PRDWC description]
 */
class PRDWC {

  /*
  * Bootstraps the class and hooks required actions & filters.
  */
  public static function init() {

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

		if(get_option('prdwc_create_project_post_type', false) ==  'yes')  add_action( 'init', 'prdwc_register_project_posttype' );

		add_action( 'plugins_loaded', __CLASS__ . '::load_plugin_textdomain' );
  }

  static function load_plugin_textdomain() {
		load_plugin_textdomain(
			'project-donations-wc',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

  static function add_to_card_button( $text, $product ) {
    if($product->get_meta( '_linkproject' ) == 'yes') $text = __('Donate', 'project-donations-wc');
  	return $text;
  }

  static function single_add_to_card_button( $text, $product ) {
    if($product->get_meta( '_linkproject' ) == 'yes') $text = __('Donate', 'project-donations-wc');
  	return $text;
  }

  static function add_to_cart_message( $message, $product_id ) {
      // make filter magic happen here...
      if(!empty($_POST['prdwc-project-name'])) $message = sanitize_text_field($_POST['prdwc-project-name']) . ": $message";
      return $message;
  }

	static function display_custom_fields_simple() {
		global $post, $product;
		if( $product->has_child() ) return; // already called by woocommerce_before_variations_form
		if(!prdwc_is_donation( $post->ID )) return;

		PRDWC::display_custom_fields();
	}

	static function display_custom_field($args) {
		if(empty($args['name'])) return;
		$value = NULL;
		$options = [];
		$label = NULL;
		$placeholder = NULL;
		$required = false;
		$disabled = false;
		$type = 'text';
		$class = '';
		$custom_attributes = [];
		extract($args);

		$name = esc_attr($name);
		$label = esc_attr($label);
		$placeholder = esc_attr($placeholder);

		$attributes = [];

		if($required) {
			$required = 'required';
			$class = "$class $required";
			$attributes[] = 'required';
		}

		if($disabled) {
			$disabled = 'disabled';
			$class = "$class $disabled";
			$attributes[] = 'disabled';
		}

		foreach($custom_attributes as $attr_key => $attr_value) {
			$attributes[] = $attr_key . '="' . $attr_value . '"';
		}
		switch($type) {
			case 'select':
			case 'multiselect':
			$input = sprintf(
			  '<select name="%1$s%2$s" id="%1$s" class="%3$s" %4$s %5$s>',
			  $name,
			  ($type == 'multiselect')? '[]' : '',
			  $class,
			  join(' ', $attributes),
			  ($type == 'multiselect') ? 'multiple="multiple"' : '',
			);
			foreach ( $options as $key => $option ) {
			  $input .= sprintf(
			    '<option id="%1$s-%2$s" value="%2$s" %4$s>%3$s</option>',
					$name,
			    $key,
			    $option,
					(is_array($value)) ? selected(in_array($key, $value, false), true) : selected( $value, $key, false ),
			  );
			}
			$input .= '</select>';
			break;

			default:
			$input = sprintf(
				'<input type="%1$s" class="input-text" name="prdwc-%2$s" value="%3$s" placeholder="%4$s" %5$s>',
				$type,
				$name,
				$value,
				$placeholder,
				join(' ', $attributes),
			);
		}
		printf(
			'<div class="prdwc-field prdwc-field-%1$s">
				<p class="form-row form-row-wide">
					<label for="prdwc-%1$s" class="%2$s">%3$s%4$s</label>
					%5$s
				</p>
			</div>',
			$name,
			$class,
			$label,
			($required) ? ' <abbr class="required" title="require">*</abbr>' : NULL,
			$input,
		);

	}

	static function get_product_project($post) {
		$project_id = get_post_meta($post->ID, 'prdwc_project_select', true);
		return (empty($project_id)) ? false : $project_id;
	}

	/**
  * Display custom field on the front end
  * @since 1.0.0
  */
  static function display_custom_fields() {
		global $post, $product;
		if(!prdwc_is_donation( $post->ID )) return;

		$project = (isset($_REQUEST['project'])) ? sanitize_text_field($_REQUEST['project']) : NULL;
		$project_post_type = (get_option('prdwc_create_project_post_type') == 'yes') ? 'project' : get_option('prdwc_project_post_type');

		$project_id = self::get_product_project($post);
		if($project_id) {
			$options = [ $project_id => esc_html(get_the_title($project_id)) ];
		} else {
			$project_id = (isset($_REQUEST['project-id'])) ? sanitize_text_field($_REQUEST['project-id']) : NULL;
			$options = ($product_project) ? [ 6037 => 'this one' ] : PRDWC::select_post_options(array(
				'post_type' => $project_post_type,
				'orderby'     => 'post_title',
			));
		}
		$price = $product->get_price();
		$amount = sanitize_text_field((isset($_REQUEST['amount'])) ? $_REQUEST['amount'] : ((isset($_REQUEST['nyp'])) ? $_REQUEST['nyp'] : NULL));

		printf('<div class="prdwc-fields" style="margin-bottom:1rem">');

		$fields[] = (empty($project_post_type)) ? array(
			'name' => 'project-name',
			'label' => __('Project', 'project-donations-wc'),
			'required' => true,
			'value' => $project,
			'placeholder' => __("Enter a project name", 'project-donations-wc'),
		) : array(
			'name' => 'project-id',
			'label' => __('Project', 'project-donations-wc'),
			'required' => true,
			'type' => 'select',
			'value' => $project_id,
			'options' => $options,
		);

		if(prdwc_allow_custom_amount()) {
			$fields[] = array(
				'name' => 'amount',
				'label' => ( ($price > 0 ) ? __('Add to fee', 'project-donations-wc') : __('Amount', 'project-donations-wc') ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'placeholder' => __('Donation amount', 'project-donations-wc'),
				'type' => 'number',
				'required' => true,
				'value' => $amount,
				'custom_attributes' => array(
					'min' => get_option('prdwc_minimum_amount', 1),
					'step' => 'any',
				)
			);
		}

		foreach($fields as $field) {
			PRDWC::display_custom_field($field);
		}


		// printf(
    //   '<div class="prdwc-field prdwc-field-project-name">
		// 		<p class="form-row form-row-wide">
    //     	<label for="prdwc-project-name" class="required">%s%s</label>
    //       <input type="%s" class="input-text" name="prdwc-project-name" value="%s" placeholder="%s" required>
    //     </p>
    //   </div>',
    //   __('Project', 'project-donations-wc'),
    //   (empty($project)) ? ' <abbr class="required" title="required">*</abbr>' : ': <span class=project_name>' . $project . '</span>',
    //   (empty($project)) ? 'text' : 'hidden',
    //   $project,
    //   __("Enter a project name", 'project-donations-wc'),
    // );

		// printf(
		//   '<div class="prdwc-field prdwc-field-amount">
		//     <p class="form-row form-row-wide">
		//       <label for="prdwc-amount" class="required">%s%s</label>
		//       <input type="number" class="input-text" name="prdwc-amount" value="%s" placeholder="%s" step="any" required>
		//     </p>
		//   </div>',
		//   ($price > 0 ) ? __('Add to fee', 'project-donations-wc') : __('Amount', 'project-donations-wc'),
		//   ' <abbr class="required" title="required">*</abbr>',
		//   $amount,
		//   ($price > 0 ) ? __("Amount to add", 'project-donations-wc') : __('Amount to pay', 'project-donations-wc'),
		// );
		printf('</div>');
  }

  static function validate_custom_field( $passed, $product_id, $quantity ) {
    if($passed && prdwc_is_donation( $product_id )) {
			if(!empty($_REQUEST['project-id'])) $project = sanitize_text_field($_REQUEST['project-id']);
      else if(!empty($_POST['prdwc-project-name'])) $project = sanitize_text_field($_POST['prdwc-project-name']);
      else if(!empty($_REQUEST['project'])) $project = sanitize_text_field($_REQUEST['project']);
      else $project = NULL;

			$product = wc_get_product( $product_id );
			$price = $product->get_price();

			if(prdwc_allow_custom_amount()) {
				if(!empty($_POST['prdwc-amount'])) $amount = sanitize_text_field($_POST['prdwc-amount']);
				else if(!empty($_REQUEST['amount'])) $amount = sanitize_text_field($_REQUEST['amount']);
				else $amount = 0;
				if(!is_numeric($amount) || $amount + $price <= 0) {
					$product_title = $product->get_title();
					wc_add_notice( sprintf(
						__('"%s" could not be added to the cart. Please provide a valid amount to pay.', 'project-donations-wc'),
						sprintf('<a href="%s">%s</a>', get_permalink($product_id), $product_title),
					), 'error' );
					return false;
				}
			}

      if( empty( $project ) ) {
        $product_title = wc_get_product( $product_id )->get_title();

        wc_add_notice( sprintf(
          __('"%s" could not be added to the cart. Please provide a project name.', 'project-donations-wc'),
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
		if(!prdwc_is_donation( wc_get_product( $product_id ) )) return $cart_item_data;

		if(!empty($_REQUEST['project-id'])) {
			$project = get_the_title(sanitize_text_field($_REQUEST['project-id']));
			if(!empty($project)) $cart_item_data['prdwc-project-id'] = sanitize_text_field($_REQUEST['project-id']);
		}
    else if(!empty($_POST['prdwc-project-name'])) $project = sanitize_text_field($_POST['prdwc-project-name']);
    else if(!empty($_REQUEST['project'])) $project = sanitize_text_field($_REQUEST['project']);
		else $project = NULL;
		$cart_item_data['prdwc-project-name'] = $project;

		if(prdwc_allow_custom_amount()) {
			if(!empty($_POST['prdwc-amount'])) $amount = sanitize_text_field($_POST['prdwc-amount']);
			else if(!empty($_REQUEST['amount'])) $amount = sanitize_text_field($_REQUEST['amount']);
			else if(!empty($_REQUEST['nyp'])) $amount = sanitize_text_field($_REQUEST['nyp']);
			else $amount = NULL;
			$cart_item_data['prdwc-amount'] = $amount;
		}

    return $cart_item_data;
  }

  /**
  * Display the custom field value in the cart
  * @since 1.0.0
  */
  static function cart_item_name( $name, $cart_item, $cart_item_key ) {

    if( isset( $cart_item['prdwc-project-name'] ) ) {
      $name = sprintf(
      '%s <span class=prdwc-project-name>"%s"</span>',
      $name,
      esc_html( $cart_item['prdwc-project-name'] ),
      );
    }
    return $name;
  }

  /**
  * Add custom field to order object
  */
  static function add_custom_data_to_order( $item, $cart_item_key, $values, $order ) {
    foreach( $item as $cart_item_key=>$values ) {
      if( isset( $values['prdwc-project-name'] ) ) {
        $item->add_meta_data( __( 'Project', 'project-donations-wc' ), $values['prdwc-project-name'], true );
				if( isset( $values['prdwc-project-id'] ) )
				$item->add_meta_data( __( 'Project ID', 'project-donations-wc' ), $values['prdwc-project-id'], true );
      }
    }
  }

  static function get_price_html( $price_html, $product ) {
    if($product->get_meta( '_linkproject' ) == 'yes') {
      $price = max($product->get_price(), get_option('prdwc-project-minimum_price', 0));
      if( $price == 0 ) {
        $price_html = apply_filters( 'woocommerce_empty_price_html', '', $product );
      } else {
        if ( $product->is_on_sale() && $product->get_price() >= $price ) {
          $price = wc_format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ),
          wc_get_price_to_display( $product ) ) . $product->get_price_suffix();
        } else {
          $price = wc_price( $price ) . $product->get_price_suffix();
        }
        $price_html = sprintf('<span class="from">%s </span>', __('From', 'project-donations-wc')) . $price;
      }
    }
    return $price_html;
  }

	static function get_variation_price_html( $data, $product, $variation ) {
		if(!prdwc_allow_custom_amount()) return $data;
		// if(prdwc_is_donation( $product->get_id() ))
		if($data['display_price'] > 0) {
			$data['price_html'] = sprintf(
				__('%s will be added to the chosen amount, the total price will be calculated before checkout.', 'project-donations-wc'),
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
		if(!prdwc_allow_custom_amount()) return;

    // Iterate through each cart item
    foreach( $cart->get_cart() as $cart_key => $cart_item ) {
      $cached = wp_cache_get('prdwc_cart_item_processed_' . $cart_key, 'project-donations');
      if(!$cached) {
        if( isset($cart_item['prdwc-amount']) &! isset($cart_item['prdwc-amount_added']) ) {
					// $cart_item['data']->adjust_price( $cart_item['prdwc-amount'] );
          $price = (float)$cart_item['data']->get_price( 'edit' );
          $total = isset($cart_item['prdwc-amount']) ? $price + $cart_item['prdwc-amount'] : $price;
          $cart_item['data']->set_price( ( $total ) );
          $cart_item['prdwc-amount_added'] = true;
        }
        wp_cache_set('prdwc_cart_item_processed_' . $cart_key, true, 'project-donations');
      }
    }
  }

	static function select_post_options($args = []) {
		$args = array_merge(array(
			'status' => 'publish',
			'order' => 'ASC',
			'limit' => -1,
		), $args);

		$posts = get_posts($args);
		if(!$posts) return [ '' => __('No posts found', 'project-posts')];

		$posts_array = array('' => __('Select a project', 'project-posts'));
		foreach($posts as $post) {
			$posts_array[$post->ID] = $post->post_title;
		}

		return $posts_array;
	}

}

PRDWC::init();
