<?php defined( 'PROPRO_VERSION' ) || die;

function propro_register_project_posttype() {
	$labels = array(
		'name'               => esc_html__( 'Projects', 'project-products' ),
		'singular_name'      => esc_html__( 'Project', 'project-products' ),
		'add_new'            => esc_html__( 'Add New', 'project-products' ),
		'add_new_item'       => esc_html__( 'Add New Project', 'project-products' ),
		'edit_item'          => esc_html__( 'Edit Project', 'project-products' ),
		'new_item'           => esc_html__( 'New Project', 'project-products' ),
		'all_items'          => esc_html__( 'All Projects', 'project-products' ),
		'view_item'          => esc_html__( 'View Project', 'project-products' ),
		'search_items'       => esc_html__( 'Search Projects', 'project-products' ),
		'not_found'          => esc_html__( 'Nothing found', 'project-products' ),
		'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'project-products' ),
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
		'name'              => esc_html__( 'Project Categories', 'project-products' ),
		'singular_name'     => esc_html__( 'Project Category', 'project-products' ),
		'search_items'      => esc_html__( 'Search Categories', 'project-products' ),
		'all_items'         => esc_html__( 'All Categories', 'project-products' ),
		'parent_item'       => esc_html__( 'Parent Category', 'project-products' ),
		'parent_item_colon' => esc_html__( 'Parent Category:', 'project-products' ),
		'edit_item'         => esc_html__( 'Edit Category', 'project-products' ),
		'update_item'       => esc_html__( 'Update Category', 'project-products' ),
		'add_new_item'      => esc_html__( 'Add New Category', 'project-products' ),
		'new_item_name'     => esc_html__( 'New Category Name', 'project-products' ),
		'menu_name'         => esc_html__( 'Categories', 'project-products' ),
		'not_found'         => esc_html__( "You currently don't have any project categories.", 'project-products' ),
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
		'name'              => esc_html__( 'Project Tags', 'project-products' ),
		'singular_name'     => esc_html__( 'Project Tag', 'project-products' ),
		'search_items'      => esc_html__( 'Search Tags', 'project-products' ),
		'all_items'         => esc_html__( 'All Tags', 'project-products' ),
		'parent_item'       => esc_html__( 'Parent Tag', 'project-products' ),
		'parent_item_colon' => esc_html__( 'Parent Tag:', 'project-products' ),
		'edit_item'         => esc_html__( 'Edit Tag', 'project-products' ),
		'update_item'       => esc_html__( 'Update Tag', 'project-products' ),
		'add_new_item'      => esc_html__( 'Add New Tag', 'project-products' ),
		'new_item_name'     => esc_html__( 'New Tag Name', 'project-products' ),
		'menu_name'         => esc_html__( 'Tags', 'project-products' ),
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
