<?php defined( 'PRDWC_VERSION' ) || die;

/**
 * Main class, used to load global stypes and scripts, and other classes.
 */
class PRDWC {

	/*
	* Bootstraps the class and hooks required actions & filters.
	*/
	public function init() {
		require_once plugin_dir_path( __FILE__ ) . 'functions.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-project.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-woocommerce.php';

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );

		add_action( 'plugins_loaded', __CLASS__ . '::load_plugin_textdomain' );
	}

	function enqueue_admin_styles() {
		wp_enqueue_script( 'prdwc-admin-script', plugin_dir_url( __DIR__ ) . 'admin/admin.js', array( 'jquery' ), PRDWC_VERSION . '-' . time(), true );
		$data = array(
			'linkProjectChecked' => get_post_meta( get_the_ID(), '_linkproject', true ),
		);
		wp_localize_script( 'prdwc-script', 'prdwcData', $data );

		wp_enqueue_style( 'prdwc-admin-style', plugin_dir_url( __DIR__ ) . 'admin/admin.css', array(), PRDWC_VERSION . time() );
	}

	function enqueue_public_scripts() {
		wp_enqueue_style( 'prdwc-style', plugin_dir_url( __DIR__ ) . 'public/public.css', array(), PRDWC_VERSION . time() );
	}

	static function load_plugin_textdomain() {
		load_plugin_textdomain(
			'project-donations-wc',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

	static function select_post_options( $args = array() ) {
		$args = array_merge(
			array(
				'status' => 'publish',
				'order'  => 'ASC',
				'limit'  => -1,
			),
			$args
		);

		$posts = get_posts( $args );
		if ( ! $posts ) {
			return array( '' => __( 'No posts found', 'project-donations-wc' ) );
		}

		$posts_array = array( '' => __( 'Select a project', 'project-donations-wc' ) );
		foreach ( $posts as $post ) {
			$posts_array[ $post->ID ] = $post->post_title;
		}

		return $posts_array;
	}

}

$prdwc = new PRDWC();
$prdwc->init();
