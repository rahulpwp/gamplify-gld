<?php
/**
 * Main Plugin Class
 *
 * @package    Gamplify_GLD
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * GLD Main Class
 */
class GLD {

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	protected $version;
	
	/**
	 * Admin instance
	 *
	 * @var GLD_Admin
	 */
	protected $admin;
	
	/**
	 * Public instance
	 *
	 * @var GLD_Public
	 */
	protected $public;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->version = GLD_VERSION;
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->schedule_events();
	}
	
	/**
	 * Load dependencies
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function load_dependencies() {
		// Dependencies are already loaded in main plugin file
	}
	
	/**
	 * Define admin hooks
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function define_admin_hooks() {
		if ( is_admin() ) {
			$this->admin = new GLD_Admin( $this->version );
		}
	}
	
	/**
	 * Define public hooks
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function define_public_hooks() {
		$this->public = new GLD_Public( $this->version );
	}
	
	/**
	 * Schedule cron events
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function schedule_events() {
		// Schedule daily cleanup
		if ( ! wp_next_scheduled( 'gld_cleanup_old_events' ) ) {
			wp_schedule_event( time(), 'daily', 'gld_cleanup_old_events' );
		}
		
		// Schedule hourly report checks
		if ( ! wp_next_scheduled( 'gld_send_scheduled_reports' ) ) {
			wp_schedule_event( time(), 'hourly', 'gld_send_scheduled_reports' );
		}
		
		// Hook cleanup function
		add_action( 'gld_cleanup_old_events', array( $this, 'cleanup_old_events' ) );
	}
	
	/**
	 * Cleanup old events
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function cleanup_old_events() {
		$retention_days = gld_get_setting( 'data_retention_days', 90 );
		gld_cleanup_old_events( $retention_days );
	}
	
	/**
	 * Run the plugin
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function run() {
		// Plugin is running
		do_action( 'gld_loaded' );
	}
	
	/**
	 * Get plugin version
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}
}
