<?php

/**
 * PRDWC Project Class
 *
 * @package project-donations-wc
 * @link            https://github.com/magicoli/project-donations-wc
 * @version 1.5.5
 * @since 1.5
 *
 * The PRDWC_Project class handles the project functionality within the Project
 * Donations for WooCommerce plugin. It registers the project post type, project
 * fields, and project-related shortcodes. It also provides methods to retrieve
 * project information such as achievements and goals.
 */

/**
 * Project class.
 *    - register project post type if needed
 *    - register project fields
 *    - register project and goals related shortcodes
 *
 * @since 1.5
 */
class PRDWC_Project {

	/**
	 * The post type for the project.
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * The project's post object.
	 *
	 * @var WP_Post
	 */
	protected $post;

	/**
	 * The project ID.
	 *
	 * @var int
	 */
	protected $project_id;

	/**
	 * Constructor for the PRDWC_Project class.
	 *
	 * @param mixed $args Project ID or post object.
	 */
	public function __construct( $args = array() ) {
		$post_id = null;
		if ( is_integer( $args ) ) {
			$post_id = $args;
		} elseif ( is_single( $args ) ) {
			$post    = get_post( $args );
			$post_id = ( $post ) ? $post->ID : null;
		}
		$this->project_id = $this->get_project_id( $post_id );
		$this->post       = get_post( $this->project_id );

	}

	/**
	 * Retrieves the project post type option value.
	 *
	 * @return string The project post type.
	 */
	public static function post_type() {
		return ( get_option( 'prdwc_create_project_post_type' ) == 'yes' ) ? 'project' : get_option( 'prdwc_project_post_type' );
	}

	/**
	 * Register the PRDWC_Project class hooks.
	 */
	public function init() {
		add_filter( 'rwmb_meta_boxes', array( $this, 'register_fields' ) );
		add_action( 'init', array( $this, 'register_shortcodes' ) );

		if ( get_option( 'prdwc_create_project_post_type', false ) == 'yes' ) {
			add_action( 'init', array( $this, 'register_post_types' ) );
		}
	}

	/**
	 * Registers the project post type.
	 */
	function register_post_types() {
		$labels = array(
			'name'               => esc_html__( 'Projects', 'project-donations-wc' ),
			'singular_name'      => esc_html__( 'Project', 'project-donations-wc' ),
			'add_new'            => esc_html__( 'Add New', 'project-donations-wc' ),
			'add_new_item'       => esc_html__( 'Add New Project', 'project-donations-wc' ),
			'edit_item'          => esc_html__( 'Edit Project', 'project-donations-wc' ),
			'new_item'           => esc_html__( 'New Project', 'project-donations-wc' ),
			'all_items'          => esc_html__( 'All Projects', 'project-donations-wc' ),
			'view_item'          => esc_html__( 'View Project', 'project-donations-wc' ),
			'search_items'       => esc_html__( 'Search Projects', 'project-donations-wc' ),
			'not_found'          => esc_html__( 'Nothing found', 'project-donations-wc' ),
			'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'project-donations-wc' ),
			'parent_item_colon'  => '',
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'can_export'         => true,
			'show_in_nav_menus'  => true,
			'query_var'          => true,
			'has_archive'        => true,
			'rewrite'            => apply_filters(
				'et_project_posttype_rewrite_args',
				array(
					'feeds'      => true,
					'slug'       => 'project',
					'with_front' => false,
				)
			),
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'menu_position'      => null,
			'menu_icon'          => 'dashicons-portfolio',
			'show_in_rest'       => true,
			'supports'           => array( 'title', 'author', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions', 'custom-fields' ),
		);
		register_post_type( 'project', apply_filters( 'et_project_posttype_args', $args ) );

		$labels = array(
			'name'              => esc_html__( 'Project Categories', 'project-donations-wc' ),
			'singular_name'     => esc_html__( 'Project Category', 'project-donations-wc' ),
			'search_items'      => esc_html__( 'Search Categories', 'project-donations-wc' ),
			'all_items'         => esc_html__( 'All Categories', 'project-donations-wc' ),
			'parent_item'       => esc_html__( 'Parent Category', 'project-donations-wc' ),
			'parent_item_colon' => esc_html__( 'Parent Category:', 'project-donations-wc' ),
			'edit_item'         => esc_html__( 'Edit Category', 'project-donations-wc' ),
			'update_item'       => esc_html__( 'Update Category', 'project-donations-wc' ),
			'add_new_item'      => esc_html__( 'Add New Category', 'project-donations-wc' ),
			'new_item_name'     => esc_html__( 'New Category Name', 'project-donations-wc' ),
			'menu_name'         => esc_html__( 'Categories', 'project-donations-wc' ),
			'not_found'         => esc_html__( "You currently don't have any project categories.", 'project-donations-wc' ),
		);

		$project_category_args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
		);
		register_taxonomy( 'project_category', array( 'project' ), $project_category_args );

		$labels = array(
			'name'              => esc_html__( 'Project Tags', 'project-donations-wc' ),
			'singular_name'     => esc_html__( 'Project Tag', 'project-donations-wc' ),
			'search_items'      => esc_html__( 'Search Tags', 'project-donations-wc' ),
			'all_items'         => esc_html__( 'All Tags', 'project-donations-wc' ),
			'parent_item'       => esc_html__( 'Parent Tag', 'project-donations-wc' ),
			'parent_item_colon' => esc_html__( 'Parent Tag:', 'project-donations-wc' ),
			'edit_item'         => esc_html__( 'Edit Tag', 'project-donations-wc' ),
			'update_item'       => esc_html__( 'Update Tag', 'project-donations-wc' ),
			'add_new_item'      => esc_html__( 'Add New Tag', 'project-donations-wc' ),
			'new_item_name'     => esc_html__( 'New Tag Name', 'project-donations-wc' ),
			'menu_name'         => esc_html__( 'Tags', 'project-donations-wc' ),
		);

		$project_tag_args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
		);
		register_taxonomy( 'project_tag', array( 'project' ), $project_tag_args );
	}

	/**
	 * Registers the project-related shortcodes.
	 */
	function register_shortcodes() {
		add_shortcode( 'achievements', array( $this, 'render_achievements' ) );
		add_shortcode( 'goals', array( $this, 'render_goals' ) );
	}

	/**
	 * Retrieves extends core get_the_ID() to include currently saved form.
	 *
	 * @return int|null The current post ID, or null if not found.
	 */
	public function get_the_ID() {
		$post_id = get_the_ID();
		if ( $post_id ) {
			return $post_id;
		}

		if ( isset( $_GET['post'] ) ) {
			return $_GET['post'];
		}

		return null;
	}

	/**
	 * Retrieves the project ID based on the post type and current post ID.
	 *
	 * @since 1.5.1
	 *
	 * @param mixed $args Project ID or post object.
	 * @return int|false The project ID if found, false otherwise.
	 */
	function get_project_id( $args = null ) {
		$post_id = ( ! empty( $this->project_id ) ) ? $this->project_id : get_the_ID( $args );
		if ( empty( $post_id ) && empty( $args ) ) {
			return false;
		}

		$debug      = ( $post_id === 1420 );
		$project_id = null;

		if ( is_integer( $args ) ) {
			$post_id = $args;
		} elseif ( is_object( $args ) && is_single( $args ) ) {
			$post    = $args;
			$post_id = ( $post ) ? $post->ID : null;
		} elseif ( is_array( $args ) ) {
			$project_id = isset( $args['project_id'] ) ? $args['project_id'] : $post_id;
		}

		// Check if the current post type matches the defined project post type
		if ( get_post_type( $post_id ) === self::post_type() ) {
			$project_id = $post_id;
		} else {
			// For other post types, retrieve the project ID from the product meta field
			$project_id = get_post_meta( $post_id, 'prdwc_project_id', true );
			if ( get_post_type( $project_id ) !== self::post_type() ) {
				// Invalid project ID
				return false;
			}
		}

		if ( empty( $project_id ) ) {
			// No project found
			return false;
		}

		// Check if the project ID is valid

		return $project_id;
	}

	/**
	 * Retrieves the project achievements.
	 *
	 * @since 1.5.1
	 *
	 * @param array $atts Shortcode attributes.
	 * @return array The project achievements.
	 */
	function get_achievements( $atts = array() ) {
		// Check if project_id is set in shortcode attributes
		$project_id = $this->get_project_id( $atts );

		// Get the product IDs associated with the project
		$product_ids = wc_get_products(
			array(
				'status'     => 'publish',
				'meta_key'   => 'prdwc_project_id',
				'meta_value' => $project_id,
				'return'     => 'ids',
			)
		);

		// Get the number and total amount of sales for the products
		$sales_count = 0;
		$sales_total = 0;

		foreach ( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id );

			if ( $product ) {
				$product_sales = 0;

				// Loop through orders to calculate the sales for each product
				$orders = wc_get_orders(
					array(
						'status'       => 'completed',
						'return'       => 'ids',
						'limit'        => -1,
						'date_created' => '>=' . date( 'Y-m-d', strtotime( '-30 days' ) ), // Example: Retrieve orders from the last 30 days
						'meta_query'   => array(
							array(
								'key'     => '_product_id',
								'value'   => $product_id,
								'compare' => '=',
							),
						),
					)
				);

				// Calculate the sales count and total amount
				foreach ( $orders as $order_id ) {
						$order = wc_get_order( $order_id );            // $product_id = $item->get_product_id();

					if ( $order ) {
						foreach ( $order->get_items() as $item ) {
							if ( $item->get_product_id() === $product_id ) {
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
		$goals     = get_post_meta( $project_id, 'goals' );
		$next_goal = null;

		if ( $goals && is_array( $goals ) ) {
			foreach ( $goals as $goal ) {
				$goal['amount'] = ( empty( $goal['amount'] ) ) ? 0 : $goal['amount'];
				$goal['name']   = ( empty( $goal['description'] ) ) ? wc_price( $goal['amount'] ) : $goal['description'];
				$next_goal      = $goal;
				if ( $sales_total < $goal['amount'] ) {
					break;
				}
			}
		}

		return array(
			'next_goal'   => $next_goal,
			'sales_total' => $sales_total,
		);
	}

	/**
	 * Renders the achievements shortcode.
	 *
	 * @since 1.5
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string The rendered achievements output.
	 */
	function render_achievements( $atts = array() ) {
		$achievements   = $this->get_achievements( $atts );
		$progress_bar   = isset( $atts['progress_bar'] ) ? $atts['progress_bar'] : get_option( 'prdwc_progress_bar', true );
		$show_goal_name = isset( $atts['show_goal_name'] ) ? $atts['show_goal_name'] : get_option( 'prdwc_show_goal_name', true );

		$next_goal   = $achievements['next_goal'];
		$sales_total = $achievements['sales_total'];

		// Prepare the output HTML
		$output = '';

		if ( $next_goal ) {

			// Check if progress bar is enabled
			// $progress_bar = get_option('prdwc_achievement_progress_bar');

			$exceeded            = ( $sales_total > $next_goal['amount'] );
			$progress_percentage = ( $exceeded ) ? ( $next_goal['amount'] / $sales_total ) * 100 : ( $sales_total / $next_goal['amount'] ) * 100;

			$bar_title = ( ( $sales_total >= $next_goal['amount'] )
			? __( 'Goal achieved: ', 'project-donations-wc' )
			: __( 'Goal: ', 'project-donations-wc' )
			) . $next_goal['name'];
			// Output the progress bar
			$output .= sprintf(
				'<div class="progress-box %s">
        %s
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
				( $show_goal_name ) ? '<h4 class="goal-name">' . $bar_title . '</h4>' : '',
				$exceeded ? $progress_percentage . '%' : '100%',
				$exceeded ? '100%' : $progress_percentage . '%',
				( $exceeded ) ? wc_price( $next_goal['amount'] ) : wc_price( $sales_total ),
				( $exceeded ) ? null : wc_price( $next_goal['amount'] ),
				( $exceeded ) ? wc_price( $sales_total ) : null,
			);

		} else {
			$sales_count = isset( $sales_count ) ? $sales_count : 0;
			$output     .= '<p>' . sprintf(
				_n(
					'Collected: %1$s (%2$s sale)',
					'Collected: %1$s (%2$s sales)',
					$sales_count,
					'project-donations-wc'
				),
				wc_price( $sales_total ),
				$sales_count,
			) . '</p>';
		}

		// Output the text-based achievement
		// $output .= '<p>' . $bar_title . ': ' . wc_price($sales_total) . ' / ' . wc_price($next_goal['amount']) . '</p>';

		return $output;
	}

	/**
	 * Registers the project fields using Meta Box plugin.
	 *
	 * @param array $meta_boxes The meta boxes array.
	 * @return array The modified meta boxes array.
	 */
	function register_fields( $meta_boxes ) {
		$prefix = '';

		$meta_boxes[] = array(
			'title'      => __( 'Project goals and counterparts', 'project-donations-wc' ),
			'id'         => 'goals',
			'post_types' => array( self::post_type() ),
			'autosave'   => true,
			'fields'     => array(
				array(
					'id'                => $prefix . 'goals',
					'type'              => 'group',
					'clone'             => true,
					'clone_as_multiple' => true,
					'add_button'        => __( 'Add a goal', 'project-donations-wc' ),
					'columns'           => 6,
					'class'             => 'goals-edit',
					'before'            => $this->render_goals(
						array(
							'edit_button' => true,
							'title'       => __( 'Goals', 'project-donations-wc' ),
						)
					),
					// 'after'             => __( 'after', 'project-donations-wc' ),
					'fields'            => array(
						array(
							'name'    => __( 'Amount', 'project-donations-wc' ),
							'id'      => $prefix . 'amount',
							'type'    => 'number',
							'min'     => 0,
							'step'    => 'any',
							'columns' => 3,
						),
						array(
							'name'    => __( 'Description', 'project-donations-wc' ),
							'id'      => $prefix . 'description',
							'type'    => 'textarea',
							'rows'    => 3,
							'columns' => 9,
						),
					),
				),
				array(
					// 'name'              => __( 'Counterparts', 'project-donations-wc' ),
					'id'                => $prefix . 'counterparts',
					'type'              => 'group',
					'clone'             => true,
					'clone_as_multiple' => true,
					'add_button'        => __( 'Add a counterpart', 'project-donations-wc' ),
					'columns'           => 6,
					'class'             => 'counterparts-edit',
					'before'            => $this->render_counterparts(),

					'fields'            => array(
						array(
							'name'    => __( 'Price', 'project-donations-wc' ),
							'id'      => $prefix . 'price',
							'type'    => 'number',
							'min'     => 0,
							'step'    => 'any',
							'columns' => 3,
						),
						array(
							'name'    => __( 'Description', 'project-donations-wc' ),
							'id'      => $prefix . 'description',
							'type'    => 'textarea',
							'rows'    => 3,
							'columns' => 6,
						),
						array(
							'name'    => __( 'Threshold', 'project-donations-wc' ),
							'id'      => $prefix . 'threshold',
							'type'    => 'number',
							'min'     => 0,
							'step'    => 'any',
							'columns' => 3,
						),
					),
				),
			),
		);

		return $meta_boxes;
	}

	/**
	 * Renders the edit button for a field group.
	 *
	 * @since 1.5
	 *
	 * @param string $field_group_name The name of the field group.
	 * @return string The rendered edit button HTML.
	 */
	function render_edit_button( $field_group_name ) {
		$html = '';

		// Button HTML
		$buttonText = __( 'Edit', 'project-donations-wc' );
		$buttonHTML = sprintf( ' <button class="page-title-action edit-%s-button rwmb-button button-secondary">%s</button> ', $field_group_name, $buttonText );
		$html      .= $buttonHTML;

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

		$jsHTML = sprintf( $jsTemplate, $field_group_name );
		$html  .= $jsHTML;

		return $html;
	}

	/**
	 * Renders the goals shortcode.
	 *
	 * @since 1.5
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string The rendered goals output.
	 */
	function render_goals( $atts = array() ) {
		$progress_bar = isset( $atts['progress_bar'] ) ? $atts['progress_bar'] : get_option( 'prdwc_progress_bar', true );
		$edit_button  = isset( $atts['edit_button'] ) ? $atts['edit_button'] : false;
		$title        = isset( $atts['title'] ) ? $atts['title'] : '';
		$show_headers = isset( $atts['show_headers'] ) ? $atts['show_headers'] : false;
		$title       .= ( $edit_button ) ? $this->render_edit_button( 'goals' ) : null;

		$achievements = $this->get_achievements( $atts );
		$next_goal    = $achievements['next_goal'];
		$sales_total  = $achievements['sales_total'];
		$post_id      = $this->get_the_ID();

		if ( ! $post_id ) {
			return null;
		}

		$goals = get_post_meta( $post_id, 'goals' );

		$html = '';

		$html .= ( ! empty( $title ) ) ? '<h3 class="goals-title">' . $title . '</h3>' : '';

		$html .= '<div class="goals-summary"><table class="goals-table">';
		// $html .= ( $progress_bar ? '<caption>' . $this->render_achievements( [ 'show_goal_name' => false ] ) . '</caption>' : '' );

		$html .= ( $show_headers ) ? sprintf(
			'<thead><tr><th>%s</th><th align=left colspan=2>%s</th></tr></thead>',
			__( 'Amount', 'project-donations-wc' ),
			__( 'Description', 'project-donations-wc' ),
		) : '';
		$html .= '<tbody>';

		if ( $goals ) {
			foreach ( $goals as $goal ) {
				$class       = '';
				$bar         = '';
				$amount      = ( empty( $goal['amount'] ) ) ? '' : wc_price( $goal['amount'] );
				$description = esc_html( $goal['description'] );

				$achieved = ( $sales_total >= @$goal['amount'] );
				if ( $achieved ) {
					$class .= 'achieved ';
				} elseif ( $progress_bar ) {
					// $bar = $this->render_achievements( [ 'show_goal_name' => false ] );
					$description  = $this->render_achievements();
					$progress_bar = false;
				}

				$html .= sprintf(
					'<tr class="goal %s">
          <td class="right price">%s</td>
          <td class="description" colspan="%s">%s</td>
          %s
          </tr>',
					$class,
					$amount,
					$achieved ? 1 : 2,
					$description . $bar,
					$achieved ? '<td class="status"><span class="dashicons dashicons-yes-alt"></span></td>' : '',
				);
			}
		}
		$html .= '</tbody>
    </table></div>';

		return $html;
	}

	/**
	 * Renders the counterparts table.
	 *
	 * @since 1.5
	 *
	 * @return string The rendered counterparts table HTML.
	 */
	function render_counterparts() {
		$post_id = $this->get_the_ID();

		if ( ! $post_id ) {
			return null;
		}
		$counterparts = get_post_meta( $post_id, 'counterparts' );

		$html  = '<h3>' . __( 'Counterparts', 'project-donations-wc' ) . $this->render_edit_button( 'counterparts' ) . '</h3>';
		$html .= '<table class="counterparts-summary">';
		$html .= '<thead><tr><th>' . __( 'Price', 'project-donations-wc' ) . '</th><th align=left>' . __( 'Description', 'project-donations-wc' ) . '</th><th>' . __( 'Threshold', 'project-donations-wc' ) . '</th></tr></thead>';
		$html .= '<tbody>';

		if ( $counterparts ) {
			foreach ( $counterparts as $counterpart ) {
				$counterpart = array_merge(
					array(
						'price'       => null,
						'description' => null,
						'threshold'   => null,
					),
					$counterpart
				);
				$html       .= '<tr>';
				$html       .= '<td class="right price">' . wc_price( $counterpart['price'] ) . '</td>';
				$html       .= '<td>' . esc_html( $counterpart['description'] ) . '</td>';
				$html       .= '<td class="right price">' . wc_price( $counterpart['threshold'] ) . '</td>';
				$html       .= '</tr>';
			}
		}

		$html .= '</tbody>';
		$html .= '</table>';

		return $html;
	}
}

$prdwc_project = new PRDWC_Project();
$prdwc_project->init();
