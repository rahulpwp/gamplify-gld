<?php
/**
 * Plugin Activator
 *
 * @package    Gamplify_GLD
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * GLD_Activator Class
 */
class GLD_Activator {

	/**
	 * Activate the plugin
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function activate() {
		// Create database tables
		gld_create_tables();
		
		// Set default options
		self::set_default_options();
		
		// Set plugin version
		update_option( 'gld_version', GLD_VERSION );
		
		// Flush rewrite rules
		flush_rewrite_rules();
		
		// Log activation
		gld_log_error( 'Plugin activated successfully' );
	}
	
	/**
	 * Set default plugin options
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function set_default_options() {
		$default_settings = array(
			'enable_tracking'       => true,
			'track_logged_in_users' => true,
			'track_admins'          => false,
			'anonymize_ip'          => true,
			'data_retention_days'   => 90,
			'enable_page_tracking'  => true,
			'enable_click_tracking' => false,
			'enable_form_tracking'  => false,
		);
		
		$existing_settings = get_option( 'gld_settings', array() );
		
		// Only set defaults for new installations
		if ( empty( $existing_settings ) ) {
			update_option( 'gld_settings', $default_settings );
		}
	}
}
