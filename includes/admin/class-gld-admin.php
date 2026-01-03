<?php
/**
 * Admin Class
 *
 * @package    Gamplify_GLD
 * @subpackage Gamplify_GLD/admin
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * GLD_Admin Class
 */
class GLD_Admin {

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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}
	
	/**
	 * Enqueue admin styles
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		$screen = get_current_screen();
		
		if ( strpos( $screen->id, 'gld' ) !== false ) {
			wp_enqueue_style(
				'gld-admin',
				GLD_URL . 'assets/css/admin/gld-admin.css',
				array(),
				$this->version
			);
			
			wp_enqueue_style(
				'gld-dashboard',
				GLD_URL . 'assets/css/admin/gld-dashboard.css',
				array(),
				$this->version
			);
		}
	}
	
	/**
	 * Enqueue admin scripts
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		
		if ( strpos( $screen->id, 'gld' ) !== false ) {
			wp_enqueue_script( 'jquery' );
			
			wp_enqueue_script(
				'chart-js',
				'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
				array(),
				'4.4.0',
				true
			);
			
			wp_enqueue_script(
				'gld-admin',
				GLD_URL . 'assets/js/admin/gld-admin.js',
				array( 'jquery' ),
				$this->version,
				true
			);
			
			wp_localize_script(
				'gld-admin',
				'gld_admin',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'gld_admin_nonce' ),
				)
			);
		}
	}
	
	/**
	 * Add admin menu
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'GLD Analytics', 'gamplify-gld' ),
			__( 'GLD Analytics', 'gamplify-gld' ),
			'manage_options',
			'gld-analytics',
			array( $this, 'display_admin_page' ),
			'dashicons-chart-area',
			30
		);
	}
	
	/**
	 * Display main admin page with tabs
	 *
	 * @return void
	 */
	public function display_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'gamplify-gld' ) );
		}
		
		include GLD_DIR . '/includes/admin/views/admin-page.php';
	}
	
	/**
	 * Register settings
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting( 'gld_settings_group', 'gld_settings' );
	}
}
