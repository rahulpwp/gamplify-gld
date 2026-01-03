<?php
/**
 * Plugin Deactivator
 *
 * @package    Gamplify_GLD
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * GLD_Deactivator Class
 */
class GLD_Deactivator {

	/**
	 * Deactivate the plugin
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function deactivate() {
		// Flush rewrite rules
		flush_rewrite_rules();
		
		// Clear scheduled events
		wp_clear_scheduled_hook( 'gld_cleanup_old_events' );
		wp_clear_scheduled_hook( 'gld_send_scheduled_reports' );
		
		// Log deactivation
		gld_log_error( 'Plugin deactivated' );
	}
}
