<?php defined( 'PRDWC_VERSION' ) || die;

function prdwc_register_project_posttype() {
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
		'menu_icon'					 => 'dashicons-portfolio',
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
