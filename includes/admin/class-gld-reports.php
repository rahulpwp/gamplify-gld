<?php
/**
 * Reports Class
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
 * GLD_Reports Class
 */
class GLD_Reports {

	/**
	 * Get report types
	 *
	 * @return array
	 */
	public static function get_report_types() {
		return array(
			'page_views'     => __( 'Page Views Report', 'gamplify-gld' ),
			'user_activity'  => __( 'User Activity Report', 'gamplify-gld' ),
			'events'         => __( 'Events Report', 'gamplify-gld' ),
			'sessions'       => __( 'Sessions Report', 'gamplify-gld' ),
		);
	}
}
