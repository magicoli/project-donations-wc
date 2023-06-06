<?php
/**
 * Add a field group to the PRDWC project post type using Meta Box plugin.
**/

class PRDWC_Project {
  protected $post_type;

  public function __construct() {
    $this->post_type = get_option('prdwc_project_post_type');
  }

  public function init() {
    add_filter('rwmb_meta_boxes', array($this, 'register_fields'));
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
          'add_button'        => __( 'Add goal', 'project-donations-wc' ),
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
          'add_button'        => __( 'Add a goal', 'project-donations-wc' ),
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
                      $(".edit-%1$s-button").addClass("active button-primary");
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
    // return '<pre>' . print_r($goals, true) . '</pre>';



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
