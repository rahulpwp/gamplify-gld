<?php
/**
 * Public Class
 *
 * @package    Gamplify_GLD
 * @subpackage Gamplify_GLD/public
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * GLD_Public Class
 */
class GLD_Public {

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Constructor
	 *
	 * @param string $version Plugin version
	 */
	public function __construct( $version ) {
		$this->version = $version;
		$this->init_hooks();
	}
	
	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( $this, 'track_page_view' ) );
	}
	
	/**
	 * Enqueue public styles
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'gld-public',
			GLD_URL . 'assets/css/public/gld-public.css',
			array(),
			$this->version
		);
	}
	
	/**
	 * Enqueue public scripts
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( ! gld_get_setting( 'enable_tracking', true ) ) {
			return;
		}
		
		wp_enqueue_script(
			'gld-tracker',
			GLD_URL . 'assets/js/public/gld-tracker.js',
			array( 'jquery' ),
			$this->version,
			true
		);
		
		wp_localize_script(
			'gld-tracker',
			'gld_public',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'gld_public_nonce' ),
			)
		);

		wp_enqueue_script(
			'gld-public',
			GLD_URL . 'assets/js/public/gld-public.js',
			array( 'jquery' ),
			$this->version,
			true
		);
	}
	
	/**
	 * Track page view
	 *
	 * @return void
	 */
	public function track_page_view() {
		if ( ! gld_get_setting( 'enable_tracking', true ) ) {
			return;
		}
		
		// Don't track admins if setting is disabled
		if ( ! gld_get_setting( 'track_admins', false ) && current_user_can( 'manage_options' ) ) {
			return;
		}
		
		$tracker = new GLD_Tracker();
		$tracker->track_page_view();
	}
}
