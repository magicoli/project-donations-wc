<?php defined( 'WCPAA_VERSION' ) || die;

add_action( 'plugin_action_links_' . WCPAA_PLUGIN, 'wpcaa_add_action_links' );
function wpcaa_add_action_links ( $actions ) {
  $admin_url = admin_url( 'edit.php?post_type=product&page=wcpaa-settings' );
  $actions = array_merge( $actions, array(
    '<a href="' . $admin_url . '">' . __('Settings', 'wcpaa') . '</a>',
  ));
  return $actions;
}

add_filter( 'mb_settings_pages', 'wcpaa_settings_page' );
function wcpaa_settings_page( $settings_pages ) {
  $settings_pages[] = [
    'menu_title' => __( 'Add-ons Autofill', 'product-addons-autofill' ),
    'id'         => 'wcpaa-settings',
    'position'   => 15,
    'parent'     => 'edit.php?post_type=product',
    'capability' => 'manage_woocommerce',
    'style'      => 'no-boxes',
    'columns'    => 1,
    // 'icon_url'   => 'dashicons-admin-generic',
  ];

  // $settings_pages['wc-settings']['tabs']['wcpaa'] = __('Add-ons Autofill', 'product-addons-autofill');
  // error_log(print_r($settings_pages, true));
  return $settings_pages;
}

add_filter( 'rwmb_meta_boxes', 'wcpaa_fields' );
function wcpaa_fields( $meta_boxes ) {
  $prefix = '';

  $meta_boxes[] = [
    'title'          => __( 'Product Add-ons Autofill', 'product-addons-autofill' ),
    'id'             => 'wcpaa',
    'settings_pages' => ['wcpaa-settings'],
    'fields'         => [
      [
        'name'   => __( 'Autofill rules', 'product-addons-autofill' ),
        'id'     => $prefix . 'autofill_rules',
        'type'   => 'group',
        'clone'  => true,
        'fields' => [
          [
            'name'    => __( 'URL parameter', 'product-addons-autofill' ),
            'id'      => $prefix . 'url_parameter',
            'type'    => 'text',
            'columns' => 3,
          ],
          [
            'name'    => __( 'Add-on field', 'product-addons-autofill' ),
            'id'      => $prefix . 'addon_field',
            'type'    => 'text',
            'columns' => 3,
          ],
        ],
      ],
    ],
  ];

  return $meta_boxes;
}
