<?php
/**
 * Analytics Engine
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
 * GLD_Analytics_Engine Class
 */
class GLD_Analytics_Engine {

	/**
	 * Calculate metrics
	 *
	 * @param array $args Arguments
	 * @return array
	 */
	public static function calculate_metrics( $args = array() ) {
		$defaults = array(
			'start_date' => date( 'Y-m-d 00:00:00', strtotime( '-7 days' ) ),
			'end_date'   => date( 'Y-m-d 23:59:59' ),
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		return array(
			'total_events'    => gld_get_event_count( $args ),
			'page_views'      => gld_get_event_count( array_merge( $args, array( 'event_type' => 'page_view' ) ) ),
			'unique_users'    => self::get_unique_users( $args ),
			'avg_session_time' => self::get_avg_session_time( $args ),
		);
	}
	
	/**
	 * Get unique users
	 *
	 * @param array $args Arguments
	 * @return int
	 */
	private static function get_unique_users( $args ) {
		global $wpdb;
		
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT user_id) FROM " . GLD_EVENTS_TABLE . " 
				WHERE created_at >= %s AND created_at <= %s AND user_id > 0",
				$args['start_date'],
				$args['end_date']
			)
		);
	}
	
	/**
	 * Get average session time
	 *
	 * @param array $args Arguments
	 * @return float
	 */
	private static function get_avg_session_time( $args ) {
		global $wpdb;
		
		$avg = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT AVG(TIMESTAMPDIFF(SECOND, start_time, end_time)) 
				FROM " . GLD_SESSIONS_TABLE . " 
				WHERE start_time >= %s AND start_time <= %s AND end_time IS NOT NULL",
				$args['start_date'],
				$args['end_date']
			)
		);
		
		return $avg ? round( $avg / 60, 2 ) : 0;
	}
}
