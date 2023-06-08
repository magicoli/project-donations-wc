<?php
/**
 * Add a field group to the PRDWC project post type using Meta Box plugin.
**/

class PRDWC_Project {
  protected $post_type;
  protected $post;
  protected $project_id;

  public function __construct($args = []) {
    $this->post_type = get_option('prdwc_project_post_type');

    $post_id = null;
    if(is_integer($args)) {
      $post_id = $args;
    } else if (is_single($args)) {
      $post = get_post($args);
      $post_id = ($post) ? $post->ID : null;
    }
    $this->project_id = $this->get_project_id($post_id);
    $this->post = get_post($this->project_id);

  }

  public function init() {
    add_filter('rwmb_meta_boxes', array($this, 'register_fields'));
    add_action('init', array($this, 'register_shortcodes'));
    add_action( 'wp_enqueue_scripts', array($this, 'enqueue_custom_styles' ));
    add_action( 'admin_enqueue_scripts', array($this, 'enqueue_custom_styles' ));
  }

  function enqueue_custom_styles() {
    wp_enqueue_style( 'custom-progress-bar', plugin_dir_url(__FILE__) . 'project-donations-wc.css', [], PRDWC_VERSION . time() );
  }

  function register_shortcodes() {
    add_shortcode('achievements', array($this, 'render_achievements'));
  }

  // Retrieve project id based on post type and current post ID
  function get_project_id($args = null) {
    $post_id = (!empty($this->project_id)) ? $this->project_id : get_the_ID($args);
    if(empty($post_id) && empty($args)) return false;

    $debug = ($post_id === 1420);
    $project_id = null;

    if(is_integer($args)) {
      $post_id = $args;
    } else if (is_object($args) && is_single($args)) {
      $post = $args;
      $post_id = ($post) ? $post->ID : null;
    } else if (is_array($args)) {
      $project_id = isset($args['project_id']) ? $args['project_id'] : $post_id;
    }

    // Check if the current post type matches the defined project post type
    if (get_post_type($post_id) === $this->post_type) {
      $project_id = $post_id;
    } else {
      // For other post types, retrieve the project ID from the product meta field
      $project_id = get_post_meta($post_id, 'prdwc_project_id', true);
      if (get_post_type($project_id) !== $this->post_type) {
        // Invalid project ID
        return false;
      }
    }

    if (empty($project_id)) {
      // No project found
      return false;
    }

    // Check if the project ID is valid

    return $project_id;
  }

  function get_achievements($atts = []) {
    // Check if project_id is set in shortcode attributes
    $project_id = $this->get_project_id($atts);

    // Get the product IDs associated with the project
    $product_ids = wc_get_products(array(
      'status'      => 'publish',
      'meta_key'    => 'prdwc_project_id',
      'meta_value'  => $project_id,
      'return'      => 'ids',
    ));

    // Get the number and total amount of sales for the products
    $sales_count = 0;
    $sales_total = 0;

    foreach ($product_ids as $product_id) {
      $product = wc_get_product($product_id);

      if ($product) {
        $product_sales = 0;

        // Loop through orders to calculate the sales for each product
        $orders = wc_get_orders(array(
          'status'       => 'completed',
          'return'       => 'ids',
          'limit'        => -1,
          'date_created' => '>=' . date('Y-m-d', strtotime('-30 days')), // Example: Retrieve orders from the last 30 days
          'meta_query'   => array(
            array(
            'key'     => '_product_id',
            'value'   => $product_id,
            'compare' => '=',
            ),
          ),
        ));

        // Calculate the sales count and total amount
        foreach ($orders as $order_id) {
          $order = wc_get_order($order_id);            // $product_id = $item->get_product_id();

          if ($order) {
            foreach ($order->get_items() as $item) {
              if ($item->get_product_id() === $product_id) {
                $sales_count++;
                $sales_total += $item->get_subtotal();
              }
            }
          }
        }

        $sales_count += $product_sales;
        $sales_total += $product_sales;
      }
    }

    // Get the next goal for the project
    $goals = get_post_meta($project_id, 'goals');
    $next_goal = null;

    if ($goals && is_array($goals)) {
      foreach ($goals as $goal) {
        $goal['amount'] = (empty($goal['amount'])) ? 0 : $goal['amount'];
        $goal['name'] = (empty($goal['description'])) ? wc_price($goal['amount']) : $goal['description'];
        $next_goal = $goal;
        if ($sales_total < $goal['amount']) {
          break;
        }
      }
    }

    return array(
      'next_goal' => $next_goal,
      'sales_total' => $sales_total,
    );
  }

  function render_achievements($atts = []) {
    $achievements = $this->get_achievements($atts);
    $progress_bar = isset($atts['progress_bar']) ? $atts['progress_bar'] : get_option('prdwc_progress_bar', true);

    $next_goal = $achievements['next_goal'];
    $sales_total = $achievements['sales_total'];

    // Prepare the output HTML
    $output = '';

    if ($next_goal) {

      // Check if progress bar is enabled
      // $progress_bar = get_option('prdwc_achievement_progress_bar');

      $exceeded = ( $sales_total > $next_goal['amount'] );
      $progress_percentage = ( $exceeded ) ? ( $next_goal['amount'] / $sales_total ) * 100 : ($sales_total / $next_goal['amount']) * 100;;

      $bar_title = ( ( $sales_total >= $next_goal['amount'] )
      ? __('Goal achieved: ', 'project-donations-wc')
      : __('Next goal: ', 'project-donations-wc')
      ) . $next_goal['name'];
      // Output the progress bar
      $output .= sprintf(
      '<div class="progress-box %s">
        <h4 class="goal-name">%s</h4>
        <div class="progress-bar">
          <div class="progress-goal" style="width: %s">
            <div class="progress" style="width: %s">
              <span class="amount">%s</span>
            </div>
            <span class="amount">%s</span>
          </div>
          <span class="amount">%s</span>
        </div>
      </div>',
      $exceeded ? 'achieved' : '',
      $bar_title,
      $exceeded ? $progress_percentage . '%' : '100%',
      $exceeded ? '100%' : $progress_percentage . '%',
      ($exceeded) ? wc_price($next_goal['amount']) : wc_price($sales_total),
      ($exceeded) ? null: wc_price($next_goal['amount']),
      ($exceeded) ? wc_price($sales_total) : null,
      );

    } else {
      $output .= '<p>' . sprintf( _n(
        'Collected: %s (%s sale)',
        'Collected: %s (%s sales)',
        $sales_count,
        'project-donations-wc'
      ),
      wc_price($sales_total),
      $sales_count,
      ) . '</p>';
    }

    // Output the text-based achievement
    // $output .= '<p>' . $bar_title . ': ' . wc_price($sales_total) . ' / ' . wc_price($next_goal['amount']) . '</p>';

    return $output;
  }

  function register_fields( $meta_boxes ) {
    $prefix = '';

    $meta_boxes[] = [
      'title'      => __( 'Project goals and counterparts', 'project-donations-wc' ),
      'id'         => 'goals',
      'post_types' => ['records'],
      'autosave'   => true,
      'fields'     => [
        [
          // 'name'              => __( 'Goals', 'project-donations-wc' ),
          'id'                => $prefix . 'goals',
          'type'              => 'group',
          'clone'             => true,
          'clone_as_multiple' => true,
          'add_button'        => __( 'Add a goal', 'project-donations-wc' ),
          'columns'           => 6,
          'class' => 'goals-edit',
          'before'            => $this->render_goals(),
          // 'after'             => __( 'after', 'project-donations-wc' ),
          'fields'            => [
            [
              'name'    => __( 'Amount', 'project-donations-wc' ),
              'id'      => $prefix . 'amount',
              'type'    => 'number',
              'min'     => 0,
              'step'    => 'any',
              'columns' => 3,
            ],
            [
              'name'    => __( 'Description', 'project-donations-wc' ),
              'id'      => $prefix . 'description',
              'type'    => 'textarea',
              'rows'    => 3,
              'columns' => 9,
            ],
          ],
        ],
        [
          // 'name'              => __( 'Counterparts', 'project-donations-wc' ),
          'id'                => $prefix . 'counterparts',
          'type'              => 'group',
          'clone'             => true,
          'clone_as_multiple' => true,
          'add_button'        => __( 'Add a counterpart', 'project-donations-wc' ),
          'columns'           => 6,
          'class'             => 'counterparts-edit',
          'before'            => $this->render_counterparts(),

          'fields'            => [
            [
              'name'    => __( 'Price', 'project-donations-wc' ),
              'id'      => $prefix . 'price',
              'type'    => 'number',
              'min'     => 0,
              'step'    => 'any',
              'columns' => 3,
            ],
            [
              'name'    => __( 'Description', 'project-donations-wc' ),
              'id'      => $prefix . 'description',
              'type'    => 'textarea',
              'rows'    => 3,
              'columns' => 6,
            ],
            [
              'name'    => __( 'Threshold', 'project-donations-wc' ),
              'id'      => $prefix . 'threshold',
              'type'    => 'number',
              'min'     => 0,
              'step'    => 'any',
              'columns' => 3,
            ],
          ],
        ],
      ],
    ];

    return $meta_boxes;
  }

  public function get_the_ID() {
    $post_id = get_the_ID();
    if ($post_id) {
      return $post_id;
    }

    if (isset($_GET['post'])) {
      return $_GET['post'];
    }

    return null;
  }

  function render_edit_button($field_group_name) {
      $html = '';

      // Button HTML
      $buttonText = __('Edit', 'project-donations-wc');
      $buttonHTML = sprintf(' <button class="edit-%s-button rwmb-button button-secondary">%s</button> ', $field_group_name, $buttonText);
      $html .= $buttonHTML;

      // JavaScript code
      $jsTemplate = '
          <script>
          (function($) {
              $(document).ready(function() {
                  var %1$sEmpty = $(".%1$s-summary tbody tr").length === 0;
                  if (%1$sEmpty) {
                      $(".edit-%1$s-button").hide();
                      $(".%1$s-summary").hide();
                      $(".%1$s-edit").show();
                  } else {
                    $(".%1$s-edit").hide();
                    $(".%1$s-summary").show();
                  }

                  $(".edit-%1$s-button").click(function(event) {
                      event.preventDefault();
                      var isActive = $(this).hasClass("active");
                      if (isActive) {
                          $(this).removeClass("active button-primary");
                          $(this).addClass("button-secondary");
                          $(".%1$s-summary").show();
                          $(".%1$s-edit").hide();
                      } else {
                          $(this).addClass("active button-primary");
                          $(this).removeClass("button-secondary");
                          $(".%1$s-summary").hide();
                          $(".%1$s-edit").show();
                      }
                  });
              });
          })(jQuery);
          </script>
      ';

      $jsHTML = sprintf($jsTemplate, $field_group_name);
      $html .= $jsHTML;

      return $html;
  }

  function render_goals() {
    $post_id = $this->get_the_ID();

    if( ! $post_id ) return null;

    $goals = get_post_meta($post_id, 'goals');

    $html = '<h3>';
    $html .= __('Goals', 'project-donations-wc');
    $html .= $this->render_edit_button('goals');
    $html .= '</h3>';

    $html .= '<div class="goals-summary">';
    $html .= '<table class="goals-table">';
    $html .= '<thead><tr><th>' . __('Amount', 'project-donations-wc') . '</th><th align=left>' . __('Description', 'project-donations-wc') . '</th></tr></thead>';
    $html .= '<tbody>';

    if ($goals) {
      foreach ($goals as $goal) {
        $amount = (empty($goal['amount'])) ? '-' : wc_price($goal['amount']);
        $description = esc_html($goal['description']);

        $goal = array_merge(
          array(
            'amount' => '-',
            'description' => null,
          ), $goal
        );

        $html .= '<tr>';
        $html .= '<td class="right price">' . $amount . '</td>';
        $html .= '<td>' . $description . '</td>';
        $html .= '</tr>';
      }
    }

    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '</div>';

    return $html;
  }

  function render_counterparts() {
    $post_id = $this->get_the_ID();

    if( ! $post_id ) return null;
    $counterparts = get_post_meta($post_id, 'counterparts' );

    $html = '<h3>' . __('Counterparts', 'project-donations-wc') . $this->render_edit_button('counterparts') . '</h3>';
    $html .= '<table class="counterparts-summary">';
    $html .= '<thead><tr><th>' . __('Price', 'project-donations-wc') . '</th><th align=left>' . __('Description', 'project-donations-wc') . '</th><th>' . __('Threshold', 'project-donations-wc') . '</th></tr></thead>';
    $html .= '<tbody>';

    if ($counterparts) {
      foreach ($counterparts as $counterpart) {
        $counterpart = array_merge(
          array(
            'price' => null,
            'description' => null,
            'threshold' => null,
          ), $counterpart
        );
        $html .= '<tr>';
        $html .= '<td class="right price">' . wc_price($counterpart['price']) . '</td>';
        $html .= '<td>' . esc_html($counterpart['description']) . '</td>';
        $html .= '<td class="right price">' . wc_price($counterpart['threshold']) . '</td>';
        $html .= '</tr>';
      }
    }

    $html .= '</tbody>';
    $html .= '</table>';

    return $html;
  }
}

$prdwc_project = new PRDWC_Project();
$prdwc_project->init();
