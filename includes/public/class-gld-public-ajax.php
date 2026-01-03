<?php
/**
 * Public AJAX Handlers
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
 * GLD_Public_Ajax Class
 */
class GLD_Public_Ajax {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init_hooks();
	}
	
	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'wp_ajax_gld_track_event', array( $this, 'track_event' ) );
		add_action( 'wp_ajax_nopriv_gld_track_event', array( $this, 'track_event' ) );
		add_action( 'wp_ajax_gld_get_chart_data', array( $this, 'get_chart_data' ) );
		add_action( 'wp_ajax_nopriv_gld_get_chart_data', array( $this, 'get_chart_data' ) );
	}
	
	/**
	 * Track event via AJAX
	 *
	 * @return void
	 */
	public function track_event() {
		check_ajax_referer( 'gld_public_nonce', 'nonce' );
		
		$event_type = isset( $_POST['event_type'] ) ? sanitize_text_field( $_POST['event_type'] ) : '';
		$event_name = isset( $_POST['event_name'] ) ? sanitize_text_field( $_POST['event_name'] ) : '';
		$event_data = isset( $_POST['event_data'] ) ? gld_sanitize_event_data( $_POST['event_data'] ) : array();
		
		if ( empty( $event_type ) || empty( $event_name ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid event data', 'gamplify-gld' ) ) );
		}
		
		$result = gld_track_event( $event_type, $event_name, $event_data );
		
		if ( $result ) {
			wp_send_json_success( array( 'event_id' => $result ) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Failed to track event', 'gamplify-gld' ) ) );
		}
	}
	
	/**
	 * Get chart data for frontend
	 *
	 * @return void
	 */
	public function get_chart_data() {
		check_ajax_referer( 'gld_public_nonce', 'nonce' );
		
		$event_type = isset( $_POST['event_type'] ) ? sanitize_text_field( $_POST['event_type'] ) : 'page_view';
		$period = isset( $_POST['period'] ) ? sanitize_text_field( $_POST['period'] ) : 'last_7_days';
		
		$date_range = gld_get_date_range( $period );
		
		global $wpdb;
		
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DATE(created_at) as date, COUNT(*) as count 
				FROM " . GLD_EVENTS_TABLE . " 
				WHERE event_type = %s 
				AND created_at >= %s 
				AND created_at <= %s 
				GROUP BY DATE(created_at) 
				ORDER BY date ASC",
				$event_type,
				$date_range['start'],
				$date_range['end']
			)
		);
		
		$labels = array();
		$values = array();
		
		foreach ( $results as $result ) {
			$labels[] = date( 'M j', strtotime( $result->date ) );
			$values[] = (int) $result->count;
		}
		
		wp_send_json_success( array(
			'labels' => $labels,
			'values' => $values,
		) );
	}
}

// Initialize
new GLD_Public_Ajax();
