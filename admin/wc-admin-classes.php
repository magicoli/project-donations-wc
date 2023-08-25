<?php
/*
 * @package         PRDWC
 */

class PRDWC_WC_Admin {

	/*
	* Bootstraps the class and hooks required actions & filters.
	*
	*/
	public static function init() {
		add_action( 'woocommerce_settings_tabs_project-donations-wc', __CLASS__ . '::settings_tab' );
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
		add_action( 'woocommerce_update_options_project-donations-wc', __CLASS__ . '::update_settings' );

		add_filter( 'plugin_action_links_' . PRDWC_PLUGIN, __CLASS__ . '::add_action_links' );

	}

	public static function add_action_links( $actions ) {
		$actions = array_merge(
			array(
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=project-donations-wc' ) . '">' . __( 'Settings', 'project-donations-wc' ) . '</a>',
			),
			$actions
		);
		return $actions;
	}

	/*
	* Add a new settings tab to the WooCommerce settings tabs array.
	*
	* @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
	* @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
	*/
	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs['project-donations-wc'] = __( 'Project Donations', 'project-donations-wc' );
		return $settings_tabs;
	}

	/*
	* Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
	*
	* @uses woocommerce_admin_fields()
	* @uses self::get_wc_admin_fields()
	*/
	public static function settings_tab() {
		woocommerce_admin_fields( self::get_wc_admin_fields() );
	}


	/**
	 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
	 *
	 * @uses woocommerce_update_options()
	 * @uses self::get_wc_admin_fields()
	 */
	public static function update_settings() {
		woocommerce_update_options( self::get_wc_admin_fields() );
	}

	static function can_disable_custom_backend_projects() {
		if ( get_option( 'prdwc_custom_user_projects' ) == 'yes' ) {
			return false;
		}
		if ( empty( PRDWC_Project::post_type() ) ) {
			return false;
		}
		return true;
	}
	/*
	* Get all the settings for this plugin for @see woocommerce_admin_fields() function.
	*
	* @return array Array of settings for @see woocommerce_admin_fields() function.
	*/
	public static function get_wc_admin_fields() {

		$settings = array(
			array(
				'name' => __( 'Default settings', 'project-donations-wc' ),
				'type' => 'title',
				'id'   => 'prdwc_section_defaults',
				'desc' => __( 'These settings can be overridden for each product.', 'project-donations-wc' ),
			),
			array(
				'name'     => __( 'Add project post type', 'project-donations-wc' ),
				'type'     => 'checkbox',
				'id'       => 'prdwc_create_project_post_type',
				'desc'     => sprintf(
					'%s <span class=description>%s</span>',
					__( 'Create a project post type', 'project-donations-wc' ),
					__( '(enable only if no other plugin implements it)', 'project-donations-wc' ),
				),
				'callback' => 'nocallback',
			),
			array(
				'name'    => __( 'Get projects from post type', 'project-donations-wc' ),
				'type'    => ( get_option( 'prdwc_create_project_post_type', false ) == 'yes' ) ? 'disabled' : 'select',
				'id'      => 'prdwc_project_post_type',
				'options' => self::select_post_type_options(),
			// 'desc_tip' => __( 'Default product for project support.', 'project-donations-wc' ),
			),
			// array(
			// 'name' => __( 'Customer defined projects', 'project-donations-wc' ),
			// 'type' => 'checkbox',
			// 'id'   => 'prdwc_custom_user_projects',
			// 'desc' => __( 'Allow customer to enter abritrary project names', 'project-donations-wc' ),
			// 'custom_attributes' => (
			// get_option('prdwc_create_project_post_type') != 'yes'
			// && empty(PRDWC_Project::post_type())
			// ) ? [ 'disabled' => 'disabled' ] : [],
			// 'desc_tip' => __( 'Always enabled if project post type is not set', 'project-donations-wc' ),
			// ),
			// array(
			// 'name' => __( 'Back end defined projects', 'project-donations-wc' ),
			// 'type' => 'checkbox',
			// 'id'   => 'prdwc_custom_backend_projects',
			// 'desc' => __( 'Allow custom project name passed as parameter', 'project-donations-wc' ),
			// 'desc_tip' => __( 'Always enabled if customer defined projects are enabled or project post type is not set', 'project-donations-wc' ),
			// 'class' => (PRDWC_WC_Admin::can_disable_custom_backend_projects()) ? '' : 'disabled',
			// 'custom_attributes' => (PRDWC_WC_Admin::can_disable_custom_backend_projects()) ? '' : [ 'disabled' => 'disabled' ] ,
			// ),
			array(
				'name' => __( 'Customer defined amount', 'project-donations-wc' ),
				'type' => 'checkbox',
				'id'   => 'prdwc_custom_amount',
				'desc' => sprintf(
					'%s <span class=description>%s</span>',
					__( 'Allow customer to choose the amount to pay', 'project-donations-wc' ),
					__( '(enable only if no other plugin implements it)', 'project-donations-wc' ),
				),
			),
			array(
				'id'                => 'prdwc_minimum_amount',
				'name'              => sprintf( __( 'Minimum donation amount (%s)', 'project-donations-wc' ), get_woocommerce_currency_symbol() ),
				'type'              => 'number',
				'default'           => 0,
				// 'custom_attributes' => (get_option('prdwc_custom_amount') != 'yes') ? [ 'disabled' => 'disabled' ] : [],
				'custom_attributes' => array(
					'min'  => 0,
					'size' => 3,
					'step' => 'any',
					( get_option( 'prdwc_custom_amount' ) != 'yes' ) ? 'disabled' : '' => 1,
				),
			),
			array(
				'id'                => 'prdwc_donate_button_label',
				'name'              => __( 'Donate button label', 'project-donations-wc' ),
				'type'              => 'text',
				'std'           => __( 'Donate', 'project-donations-wc' ),
				'placeholder'           => __( 'Donate', 'project-donations-wc' ),
				// 'custom_attributes' => (get_option('prdwc_custom_amount') != 'yes') ? [ 'disabled' => 'disabled' ] : [],
			),
			array(
				'id'                => 'prdwc_donate_field_placeholder',
				'name'              => __( 'Donation field placeholder', 'project-donations-wc' ),
				'type'              => 'text',
				'std'           => __( 'Donation amount', 'project-donations-wc' ),
				'placeholder'           => __( 'Donation amount', 'project-donations-wc' ),
				// 'custom_attributes' => (get_option('prdwc_custom_amount') != 'yes') ? [ 'disabled' => 'disabled' ] : [],
			),
			array(
				'type' => 'sectionend',
				'id'   => 'prdwc_section_projects_end',
			),

		// array(
		// 'name'     => __( 'Enable globally', 'project-donations-wc' ),
		// 'type'     => 'title',
		// 'id'       => 'prdwc_section_global',
		// ),
		// array(
		// 'name' => __( 'Default project donations', 'project-donations-wc' ),
		// 'type' => 'select',
		// 'id'   => 'prdwc_default_product',
		// 'options' => PRDWC_WC_Admin::select_product_options(),
		// 'desc_tip' => __( 'Default product for project support.', 'project-donations-wc' ),
		// ),
		// array(
		// 'name' => __( 'Enable categories', 'project-donations-wc' ),
		// 'type' => 'multiselect',
		// 'id'   => 'prdwc_enable_categories',
		// 'options' => PRDWC_WC_Admin::select_category_options(),
		// 'desc_tip' => __( 'Default product for project support.', 'project-donations-wc' ),
		// ),
		// array(
		// 'name' => __( 'Enable tags', 'project-donations-wc' ),
		// 'type' => 'multiselect',
		// 'id'   => 'prdwc_enable_tags',
		// 'options' => PRDWC_WC_Admin::select_taxonomy_options(),
		// 'desc_tip' => __( 'Default product for project support.', 'project-donations-wc' ),
		// ),
		// array(
		// 'type' => 'sectionend',
		// 'id' => 'prdwc_section_global_end'
		// ),
		//
		);
		return apply_filters( 'prdwc_settings', $settings );
	}

	static function select_product_options( $args = array() ) {
		$args = array_merge(
			array(
				'status'  => 'publish',
				'orderby' => 'name',
				'limit'   => -1,
			),
			$args
		);

		$products = wc_get_products( $args );
		if ( ! $products ) {
			return array( '' => __( 'No products found', 'project-donations-wc' ) );
		}

		$products_array = array( '' => _x( 'None', 'Select product', 'project-donations-wc' ) );
		foreach ( $products as $product ) {
			$products_array[ $product->id ] = $product->get_formatted_name();
		}

		return $products_array;
	}

	static function select_category_options( $cat_args = array() ) {
		$cat_args           = array_merge(
			array(
				// 'status' => 'publish',
				'orderby'    => 'name',
				'limit'      => -1,
				'hide_empty' => false,
			),
			$cat_args
		);
		$product_categories = get_terms( 'product_cat', $cat_args );
		if ( empty( $product_categories ) ) {
			return array( '' => __( 'No categories found', 'project-donations-wc' ) );
		}

		$categories_array = array( '' => _x( 'None', 'Select category', 'project-donations-wc' ) );
		foreach ( $product_categories as $key => $category ) {
			$categories_array[ $category->term_id ] = $category->name;
		}

		return $categories_array;
	}

	static function select_taxonomy_options( $tax_args = array() ) {
		$tax_args           = array_merge(
			array(
				// 'status' => 'publish',
				'taxonomy'   => 'product_tag',
				'orderby'    => 'name',
				'limit'      => -1,
				'hide_empty' => false,
			),
			$tax_args
		);
		$product_taxonomies = get_terms( $tax_args );
		if ( empty( $product_taxonomies ) ) {
			return array( '' => __( 'No taxonomies found', 'project-donations-wc' ) );
		}
		$taxonomies_array = array( '' => _x( 'None', 'Select taxonomy', 'project-donations-wc' ) );
		foreach ( $product_taxonomies as $key => $taxonomy ) {
			$taxonomies_array[ $taxonomy->term_id ] = $taxonomy->name;
		}

		return $taxonomies_array;
	}

	static function select_post_type_options( $tax_args = array() ) {
		$args = array(
			'public' => true,
		// '_builtin' => false
		);
		$post_types = get_post_types( $args, 'objects' );
		if ( empty( $post_types ) ) {
			return array( '' => __( 'No post types found, wich is tretty weird.', 'project-donations-wc' ) );
		}

		$post_types_array = array( '' => _x( 'None', 'Select post type', 'project-donations-wc' ) );
		foreach ( $post_types as $key => $post_type ) {
			$post_types_array[ $post_type->name ] = $post_type->label;
		}

		return $post_types_array;
	}
}

PRDWC_WC_Admin::init();
