<?php
/**
 * Frontend Reports Class
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
 * GLD_Frontend_Reports Class
 */
class GLD_Frontend_Reports {

	/**
	 * Get user statistics
	 *
	 * @param int $user_id User ID
	 * @return array
	 */
	public static function get_user_stats( $user_id ) {
		if ( ! $user_id ) {
			return array();
		}
		
		$date_range = gld_get_date_range( 'last_30_days' );
		
		return array(
			'total_events'  => gld_get_event_count( array(
				'user_id'    => $user_id,
				'start_date' => $date_range['start'],
				'end_date'   => $date_range['end'],
			) ),
			'page_views'    => gld_get_event_count( array(
				'user_id'    => $user_id,
				'event_type' => 'page_view',
				'start_date' => $date_range['start'],
				'end_date'   => $date_range['end'],
			) ),
		);
	}
}
