<?php

class PRDWC_WooCommerce {

  public function __construct() {
    add_filter('woocommerce_product_data_tabs', array($this, 'add_donations_tab'));
    add_filter( 'product_type_options', __CLASS__ . '::add_product_type_options');
    add_action('woocommerce_product_data_panels', array($this, 'add_donations_tab_content'));
    add_action('admin_enqueue_scripts', array($this, 'prdwc_enqueue_scripts'));
  }

  function prdwc_enqueue_scripts() {
    $post_type = get_post_type();
    $screen = get_current_screen();

    if ($screen && $screen->base === 'post' && $screen->post_type === 'product') {
      wp_enqueue_script('prdwc-script', plugin_dir_url(__FILE__) . 'product-options.js', array('jquery'), PRDWC_VERSION . '-' . time(), true);

      // Localize script data
      $data = array(
          'linkProjectChecked' => get_post_meta(get_the_ID(), '_linkproject', true),
      );
      wp_localize_script('prdwc-script', 'prdwcData', $data);

    }

  }

  static function add_product_type_options($product_type_options) {
    $product_type_options['linkproject'] = array(
      "id"            => "_linkproject",
      "wrapper_class" => "show_if_simple show_if_variable",
      "label"         => __('Project Donation', 'project-donations-wc'),
      "description"   => __('Check to add a project field to product page.', 'project-donations-wc'),
      "default"       => "no",
    );
    return $product_type_options;
  }


  public function add_donations_tab($tabs) {
    $tabs['donations'] = array(
      'label'    => __('Project Donations', 'project-donations-wc'),
      'target'   => 'donations_options',
      'priority' => 80,
    );

    return $tabs;
  }

  public function add_donations_tab_content() {
    global $post;

    echo '<div id="donations_options" class="panel woocommerce_options_panel hidden">';
    echo '<div class="options_group">';

    $project_post_type = get_option('prdwc_project_post_type');
    $projects = new WP_Query(array(
      'post_type'      => $project_post_type,
      'posts_per_page' => -1,
    ));

    if ($projects->have_posts()) {

      echo '<p class="form-field">';
      echo '<label for="prdwc_project_select">' . __('Default project', 'project-donations-wc') . '</label>';
      echo '<select name="prdwc_project_select" id="prdwc_project_select">';
      echo '<option value="">' . __('Select a project', 'project-donations-wc') . '</option>';
      while ($projects->have_posts()) {
        $projects->the_post();
        echo '<option value="' . esc_attr(get_the_ID()) . '">' . esc_html(get_the_title()) . '</option>';
        // echo '<option value="' . esc_attr(get_the_ID()) . '">' . esc_html(get_the_title()) . '</option>';
        // echo '<br/>' . esc_html(get_the_title());
      }
      echo '</select>';
      echo '</p>';
      wp_reset_postdata();

      // echo '<p class="form-field">';
      woocommerce_wp_checkbox(array(
        'id'            => 'prdwc_allow_choose_project',
        'label'         => __('Allow to choose', 'project-donations-wc'),
        'description'   => __('Check this box to allow users to select another project.', 'project-donations-wc'),
        'desc_tip'      => true,
        // 'wrapper_class' => 'hide_if_simple hide_if_external',
      ));

    } else {
      echo '<p>' . __('Create at least one project first', 'project-donations-wc');
    }

    // Add your custom fields related to project donations here

    echo '</div>';
    echo '</div>';
  }
}

new PRDWC_WooCommerce();
