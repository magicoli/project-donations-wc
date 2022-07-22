<?php
/*
 * @package         PROPRO
 */

class PROPRO_WC_Admin {

  /*
  * Bootstraps the class and hooks required actions & filters.
  *
  */
  public static function init() {
    add_action( 'woocommerce_settings_tabs_propro', __CLASS__ . '::settings_tab' );
    add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
    add_action( 'woocommerce_update_options_propro', __CLASS__ . '::update_settings' );

    add_filter( 'plugin_action_links_' . PROPRO_PLUGIN, __CLASS__ . '::add_action_links' );

    // add_filter( "woocommerce_admin_settings_sanitize_option_propro_openprovider_hash", __CLASS__ . '::check_credentials', 10, 3 );
    // add_filter( "woocommerce_admin_settings_sanitize_option_propro_openprovider_username", __CLASS__ . '::check_credentials', 10, 3 );
  }

  public static function add_action_links ( $actions ) {
    $actions = array_merge( $actions, array(
      '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=propro' ) . '">' . __('Settings', 'project-products') . '</a>',
    ));
    return $actions;
  }

  /*
  * Add a new settings tab to the WooCommerce settings tabs array.
  *
  * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
  * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
  */
  public static function add_settings_tab( $settings_tabs ) {
    $settings_tabs['propro'] = __( 'Project Products', 'project-products' );
    return $settings_tabs;
  }

  /*
  * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
  *
  * @uses woocommerce_admin_fields()
  * @uses self::get_settings()
  */
  public static function settings_tab() {
    woocommerce_admin_fields( self::get_settings() );
  }


  /**
  * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
  *
  * @uses woocommerce_update_options()
  * @uses self::get_settings()
  */
  public static function update_settings() {
    woocommerce_update_options( self::get_settings() );
  }

  static function can_disable_custom_backend_projects() {
    if (get_option('propro_custom_user_projects') == 'yes') return false;
    if ( get_option('propro_create_project_post_type') != 'yes'
    && empty(get_option('propro_project_post_type')) ) return false;
    return true;
  }
  /*
  * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
  *
  * @return array Array of settings for @see woocommerce_admin_fields() function.
  */
  public static function get_settings() {

    $settings = array(
      array(
        'name'     => __( 'Default settings', 'project-products' ),
        'type'     => 'title',
        'id'       => 'propro_section_defaults',
        'desc' => __( 'These settings can be overridden for each product.', 'project-products' ),
      ),
      array(
        'name' => __( 'Add project post type', 'project-products' ),
        'type' => 'checkbox',
        'id'   => 'propro_create_project_post_type',
        'desc' => sprintf(
          '%s <span class=description>%s</span>',
          __( 'Create a project post type</span>', 'project-products' ),
          __( '(enable only if no other plugin implements it)', 'project-products' ),
        ),
        'callback' => 'nocallback',
      ),
      array(
        'name' => __( 'Get projects from post type', 'project-products' ),
        'type' => (get_option('propro_create_project_post_type', false) ==  'yes') ? 'disabled' : 'select',
        'id'   => 'propro_project_post_type',
        'options' => PROPRO_WC_Admin::select_post_type_options(),
        // 'desc_tip' => __( 'Default product for project support.', 'project-products' ),
      ),
      // array(
      //   'name' => __( 'Customer defined projects', 'project-products' ),
      //   'type' => 'checkbox',
      //   'id'   => 'propro_custom_user_projects',
      //   'desc' => __( 'Allow customer to enter abritrary project names', 'project-products' ),
      //   'custom_attributes' => (
      //     get_option('propro_create_project_post_type') != 'yes'
      //     && empty(get_option('propro_project_post_type'))
      //   ) ? [ 'disabled' => 'disabled' ] : [],
      //   'desc_tip' => __( 'Always enabled if project post type is not set', 'project-products' ),
      // ),
      // array(
      //   'name' => __( 'Back end defined projects', 'project-products' ),
      //   'type' => 'checkbox',
      //   'id'   => 'propro_custom_backend_projects',
      //   'desc' => __( 'Allow custom project name passed as parameter', 'project-products' ),
      //   'desc_tip' => __( 'Always enabled if customer defined projects are enabled or project post type is not set', 'project-products' ),
      //   'class' => (PROPRO_WC_Admin::can_disable_custom_backend_projects()) ? '' : 'disabled',
      //   'custom_attributes' => (PROPRO_WC_Admin::can_disable_custom_backend_projects()) ? '' : [ 'disabled' => 'disabled' ] ,
      // ),
      // array(
      //   'name' => __( 'Customer defined amount', 'project-products' ),
      //   'type' => 'checkbox',
      //   'id'   => 'propro_custom_amount',
      //   'desc' => sprintf(
      //     '%s <span class=description>%s</span>',
      //     __( 'Allow customer to choose the amount to pay', 'project-products' ),
      //     __( '(enable only if no other plugin implements it)', 'project-products' ),
      //   ),
      // ),
      // array(
      //   'id' => 'propro_minimum_amount',
      //   'name' => sprintf(__('Minimum donation amount (%s)', 'project-products'), get_woocommerce_currency_symbol()),
      //   'type' => 'number',
      //   'default' => 0,
      //   // 'custom_attributes' => (get_option('propro_custom_amount') != 'yes') ? [ 'disabled' => 'disabled' ] : [],
      //   'custom_attributes' => array(
      //     'min' => 0,
      //     'size' => 3,
      //     'step' => 'any',
      //     (get_option('propro_custom_amount') != 'yes') ? 'disabled' : '' => 1,
      //   ),
      // ),
      array(
        'type' => 'sectionend',
        'id' => 'propro_section_projects_end'
      ),

    //   array(
    //     'name'     => __( 'Enable globally', 'project-products' ),
    //     'type'     => 'title',
    //     'id'       => 'propro_section_global',
    //   ),
    //   array(
    //     'name' => __( 'Default project product', 'project-products' ),
    //     'type' => 'select',
    //     'id'   => 'propro_default_product',
    //     'options' => PROPRO_WC_Admin::select_product_options(),
    //     // 'desc_tip' => __( 'Default product for project support.', 'project-products' ),
    //   ),
    //   array(
    //     'name' => __( 'Enable categories', 'project-products' ),
    //     'type' => 'multiselect',
    //     'id'   => 'propro_enable_categories',
    //     'options' => PROPRO_WC_Admin::select_category_options(),
    //     // 'desc_tip' => __( 'Default product for project support.', 'project-products' ),
    //   ),
    //   array(
    //     'name' => __( 'Enable tags', 'project-products' ),
    //     'type' => 'multiselect',
    //     'id'   => 'propro_enable_tags',
    //     'options' => PROPRO_WC_Admin::select_taxonomy_options(),
    //     // 'desc_tip' => __( 'Default product for project support.', 'project-products' ),
    //   ),
    //   array(
    //     'type' => 'sectionend',
    //     'id' => 'propro_section_global_end'
    //   ),
    //
    );
    return apply_filters( 'propro_settings', $settings );
  }

  static function select_product_options($args = []) {
    $args = array_merge(array(
      // 'category' => array( 'hoodies' ),
      'status' => 'publish',
      'orderby'  => 'name',
  		'limit' => -1,
    ), $args);

    $products = wc_get_products($args);
    if(!$products) return [ '' => __('No products found', 'project-products')];

    $products_array = array('' => _x('None', 'Select product', 'project-products'));
    foreach($products as $product) {
      $products_array[$product->id] = $product->get_formatted_name();
    }

    return $products_array;
  }

  static function select_category_options($cat_args = []) {
    $cat_args = array_merge(array(
      // 'status' => 'publish',
      'orderby'  => 'name',
  		'limit' => -1,
      'hide_empty' => false,
    ), $cat_args);
    $product_categories = get_terms( 'product_cat', $cat_args );
    if(empty($product_categories)) return [ '' => __('No categories found', 'project-products')];

    $categories_array = array('' => _x('None', 'Select category', 'project-products'));
    foreach ($product_categories as $key => $category) {
      $categories_array[$category->term_id] = $category->name;
    }

    return $categories_array;
  }

  static function select_taxonomy_options($tax_args = []) {
    $tax_args = array_merge(array(
      // 'status' => 'publish',
      'taxonomy' => 'product_tag',
      'orderby'  => 'name',
      'limit' => -1,
      'hide_empty' => false,
    ), $tax_args);
    $product_taxonomies = get_terms( $tax_args );
    if(empty($product_taxonomies)) return [ '' => __('No taxonomies found', 'project-products')];
    $taxonomies_array = array('' => _x('None', 'Select taxonomy', 'project-products'));
    foreach ($product_taxonomies as $key => $taxonomy) {
      $taxonomies_array[$taxonomy->term_id] = $taxonomy->name;
    }

    return $taxonomies_array;
  }

  static function select_post_type_options($tax_args = []) {
    $args = array(
      'public'   => true,
      // '_builtin' => false
    );
    // $output = 'names'; // 'names' or 'objects' (default: 'names')
    // $operator = 'and'; // 'and' or 'or' (default: 'and')
    $post_types = get_post_types($args, 'objects');
    if(empty($post_types)) return [ '' => __('No post types found, wich is tretty weird.', 'project-products')];

    $post_types_array = array('' => _x('None', 'Select post type', 'project-products'));
    foreach ($post_types as $key => $post_type) {
      $post_types_array[$post_type->name] = $post_type->label;
    }

    return $post_types_array;
  }
}

PROPRO_WC_Admin::init();
