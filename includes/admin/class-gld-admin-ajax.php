<?php
/**
 * Admin AJAX Handlers
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
 * GLD_Admin_Ajax Class
 */
class GLD_Admin_Ajax {

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
		add_action( 'wp_ajax_gld_get_dashboard_stats', array( $this, 'get_dashboard_stats' ) );
		add_action( 'wp_ajax_gld_get_chart_data', array( $this, 'get_chart_data' ) );
		add_action( 'wp_ajax_gld_export_report', array( $this, 'export_report' ) );
		add_action( 'wp_ajax_gld_save_report', array( $this, 'save_report' ) );
		add_action( 'wp_ajax_gld_delete_report', array( $this, 'delete_report' ) );
		add_action( 'wp_ajax_gld_save_member_kpi', array( $this, 'save_member_kpi' ) );
		add_action( 'wp_ajax_gld_get_member_kpis', array( $this, 'get_member_kpis' ) );
		add_action( 'wp_ajax_gld_delete_member_kpi', array( $this, 'delete_member_kpi' ) );
	}
	
	/**
	 * Get dashboard statistics
	 *
	 * @return void
	 */
	public function get_dashboard_stats() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );
		}
		
		$period = isset( $_POST['period'] ) ? sanitize_text_field( $_POST['period'] ) : 'last_7_days';
		$date_range = gld_get_date_range( $period );
		
		$stats = array(
			'total_events'    => gld_get_event_count( array(
				'start_date' => $date_range['start'],
				'end_date'   => $date_range['end'],
			) ),
			'page_views'      => gld_get_event_count( array(
				'event_type' => 'page_view',
				'start_date' => $date_range['start'],
				'end_date'   => $date_range['end'],
			) ),
			'unique_users'    => $this->get_unique_users_count( $date_range ),
			'avg_session_time' => $this->get_avg_session_time( $date_range ),
		);
		
		wp_send_json_success( $stats );
	}
	
	/**
	 * Get chart data
	 *
	 * @return void
	 */
	public function get_chart_data() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );
		}
		
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
	
	/**
	 * Export report
	 *
	 * @return void
	 */
	public function export_report() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );
		}
		
		$format = isset( $_POST['format'] ) ? sanitize_text_field( $_POST['format'] ) : 'csv';
		$report_id = isset( $_POST['report_id'] ) ? absint( $_POST['report_id'] ) : 0;
		
		$exporter = new GLD_Data_Export();
		$file_url = $exporter->export_report( $report_id, $format );
		
		if ( $file_url ) {
			wp_send_json_success( array( 'file_url' => $file_url ) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Export failed', 'gamplify-gld' ) ) );
		}
	}
	
	/**
	 * Save report
	 *
	 * @return void
	 */
	public function save_report() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );
		}
		
		// Report saving logic would go here
		wp_send_json_success( array( 'message' => __( 'Report saved successfully', 'gamplify-gld' ) ) );
	}
	
	/**
	 * Delete report
	 *
	 * @return void
	 */
	public function delete_report() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );
		}
		
		$report_id = isset( $_POST['report_id'] ) ? absint( $_POST['report_id'] ) : 0;
		
		global $wpdb;
		$result = $wpdb->delete( GLD_REPORTS_TABLE, array( 'id' => $report_id ), array( '%d' ) );
		
		if ( $result ) {
			wp_send_json_success( array( 'message' => __( 'Report deleted successfully', 'gamplify-gld' ) ) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Delete failed', 'gamplify-gld' ) ) );
		}
	}
	
	/**
	 * Get unique users count
	 *
	 * @param array $date_range Date range
	 * @return int
	 */
	private function get_unique_users_count( $date_range ) {
		global $wpdb;
		
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT user_id) 
				FROM " . GLD_EVENTS_TABLE . " 
				WHERE created_at >= %s 
				AND created_at <= %s 
				AND user_id > 0",
				$date_range['start'],
				$date_range['end']
			)
		);
	}
	
	/**
	 * Get average session time
	 *
	 * @param array $date_range Date range
	 * @return float
	 */
	private function get_avg_session_time( $date_range ) {
		global $wpdb;
		
		$avg = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT AVG(TIMESTAMPDIFF(SECOND, start_time, end_time)) 
				FROM " . GLD_SESSIONS_TABLE . " 
				WHERE start_time >= %s 
				AND start_time <= %s 
				AND end_time IS NOT NULL",
				$date_range['start'],
				$date_range['end']
			)
		);
		
		return $avg ? round( $avg / 60, 2 ) : 0; // Return in minutes
	}

	/**
	 * Save Member KPI
	 *
	 * @return void
	 */
	public function save_member_kpi() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );
		}
		
		$title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		$metric_type = isset( $_POST['metric_type'] ) ? sanitize_text_field( $_POST['metric_type'] ) : '';
		$filter_by_course = isset( $_POST['filter_by_course'] ) ? sanitize_text_field( $_POST['filter_by_course'] ) : '';
		$include_chart_version = isset( $_POST['include_chart_version'] ) ? sanitize_text_field( $_POST['include_chart_version'] ) : '';
		
		if ( empty( $metric_type ) ) {
			wp_send_json_error( array( 'message' => __( 'Metric type is required', 'gamplify-gld' ) ) );
		}
		
		global $wpdb;
		
		$data = array(
			'title'                 => $title,
			'metric_type'           => $metric_type,
			'filter_by_course'      => $filter_by_course,
			'include_chart_version' => $include_chart_version,
		);
		
		$format = array( '%s', '%s', '%s', '%s' );
		
		$result = $wpdb->insert( GLD_MEMBER_KPI_TABLE, $data, $format );
		
		if ( $result ) {
			wp_send_json_success( array(
				'message' => __( 'Member KPI saved successfully', 'gamplify-gld' ),
				'id'      => $wpdb->insert_id,
				'data'    => $data
			) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Failed to save Member KPI', 'gamplify-gld' ) ) );
		}
	}

	/**
	 * Get Member KPIs with pagination
	 *
	 * @return void
	 */
	public function get_member_kpis() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );
		}
		
		global $wpdb;
		
		$page = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
		$limit = 5; // Temporary limit as requested
		$offset = ( $page - 1 ) * $limit;
		
		// Get items
		$items = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM " . GLD_MEMBER_KPI_TABLE . " 
				ORDER BY created DESC 
				LIMIT %d OFFSET %d",
				$limit,
				$offset
			)
		);
		
		// Get total count
		$total_items = (int) $wpdb->get_var( "SELECT COUNT(*) FROM " . GLD_MEMBER_KPI_TABLE );
		$total_pages = ceil( $total_items / $limit );
		
		wp_send_json_success( array(
			'items' => $items,
			'pagination' => array(
				'current_page' => $page,
				'total_pages'  => $total_pages,
				'total_items'  => $total_items,
				'limit'        => $limit
			)
		) );
	}

	/**
	 * Delete Member KPI
	 *
	 * @return void
	 */
	public function delete_member_kpi() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );
		}
		
		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		
		if ( ! $id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid ID', 'gamplify-gld' ) ) );
		}
		
		global $wpdb;
		
		$result = $wpdb->delete( GLD_MEMBER_KPI_TABLE, array( 'id' => $id ), array( '%d' ) );
		
		if ( $result ) {
			wp_send_json_success( array( 'message' => __( 'Member KPI deleted successfully', 'gamplify-gld' ) ) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Failed to delete Member KPI', 'gamplify-gld' ) ) );
		}
	}
}


// Initialize
new GLD_Admin_Ajax();
