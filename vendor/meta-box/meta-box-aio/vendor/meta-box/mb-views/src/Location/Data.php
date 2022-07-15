<?php
namespace MBViews\Location;

use WP_REST_Server;
use WP_REST_Request;
use RWMB_Post_Field;
use RWMB_Taxonomy_Field;

class Data {
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes() {
		$params = [
			'method'              => WP_REST_Server::READABLE,
			'permission_callback' => [ $this, 'has_permission' ],
			'args'                => [
				'name'     => [
					'sanitize_callback' => 'sanitize_text_field',
				],
				'term'     => [
					'sanitize_callback' => 'sanitize_text_field',
				],
				'selected' => [
					'sanitize_callback' => 'sanitize_text_field',
				],
			],
		];
		register_rest_route( 'mbv/location', 'terms', array_merge( $params, [
			'callback' => [ $this, 'get_terms' ],
		] ) );
		register_rest_route( 'mbv/location', 'posts', array_merge( $params, [
			'callback' => [ $this, 'get_posts' ],
		] ) );
	}

	public function get_terms( WP_REST_Request $request ) {
		$search_term        = $request->get_param( 'term' );
		$name               = $request->get_param( 'name' );
		list( , $taxonomy ) = explode( ':', $name );

		$field = [
			'query_args' => [
				'taxonomy'   => $taxonomy,
				'name__like' => $search_term,
				'orderby'    => 'name',
				'number'     => 10,
			],
		];
		$data  = RWMB_Taxonomy_Field::query( null, $field );

		return array_values( $data );
	}

	public function get_posts( WP_REST_Request $request ) {
		$search_term       = $request->get_param( 'term' );
		$name              = $request->get_param( 'name' );
		list( $post_type ) = explode( ':', $name );

		$field = [
			'query_args' => [
				's'              => $search_term,
				'post_type'      => $post_type,
				'post_status'    => 'any',
				'posts_per_page' => 10,
				'orderby'        => 'post_title',
				'order'          => 'ASC',
			],
		];
		$data  = RWMB_Post_Field::query( null, $field );

		return array_values( $data );
	}

	public function has_permission() {
		return current_user_can( 'manage_options' );
	}
}
