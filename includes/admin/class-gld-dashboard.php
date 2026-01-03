<?php
/**
 * Dashboard Class
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
 * GLD_Dashboard Class
 */
class GLD_Dashboard {

	/**
	 * Get dashboard widgets
	 *
	 * @return array
	 */
	public static function get_widgets() {
		return array(
			'total_events'    => __( 'Total Events', 'gamplify-gld' ),
			'page_views'      => __( 'Page Views', 'gamplify-gld' ),
			'unique_users'    => __( 'Unique Users', 'gamplify-gld' ),
			'avg_session_time' => __( 'Avg. Session Time', 'gamplify-gld' ),
		);
	}
}
