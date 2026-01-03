<?php
/**
 * Settings Page
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
 * GLD_Settings Class
 */
class GLD_Settings {

	/**
	 * Render settings page
	 *
	 * @return void
	 */
	public function render() {
		include GLD_DIR . '/includes/admin/views/settings.php';
	}
	
	/**
	 * Get settings tabs
	 *
	 * @return array
	 */
	public function get_tabs() {
		return array(
			'general'  => __( 'General', 'gamplify-gld' ),
			'tracking' => __( 'Tracking', 'gamplify-gld' ),
			'reports'  => __( 'Reports', 'gamplify-gld' ),
			'privacy'  => __( 'Privacy', 'gamplify-gld' ),
		);
	}
}
