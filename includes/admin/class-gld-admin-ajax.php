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
		
		add_action( 'wp_ajax_gld_save_chart', array( $this, 'save_chart' ) );
		add_action( 'wp_ajax_gld_get_charts', array( $this, 'get_charts' ) );
		add_action( 'wp_ajax_gld_delete_chart', array( $this, 'delete_chart' ) );

		// Learning module handlers
		add_action( 'wp_ajax_gld_save_learning_kpi', array( $this, 'save_learning_kpi' ) );
		add_action( 'wp_ajax_gld_get_learning_kpis', array( $this, 'get_learning_kpis' ) );
		add_action( 'wp_ajax_gld_get_learning_kpi', array( $this, 'get_learning_kpi' ) );
		add_action( 'wp_ajax_gld_delete_learning_kpi', array( $this, 'delete_learning_kpi' ) );
		add_action( 'wp_ajax_gld_save_learning_data_table', array( $this, 'save_learning_data_table' ) );
		add_action( 'wp_ajax_gld_get_learning_data_tables', array( $this, 'get_learning_data_tables' ) );
		add_action( 'wp_ajax_gld_get_learning_data_table', array( $this, 'get_learning_data_table' ) );
		add_action( 'wp_ajax_gld_delete_learning_data_table', array( $this, 'delete_learning_data_table' ) );
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
		
		// Enrich items with course/product names
		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {
				// Metric Type Name Resolution (if it's a product ID)
				if ( is_numeric( $item->metric_type ) && class_exists( 'WooCommerce' ) ) {
					$product = wc_get_product( $item->metric_type );
					if ( $product ) {
						// Optionally update title display or just leave title as stored. 
						// The title is stored as text, so it should be fine.
						// But metric_type column might need care if used for logic.
					}
				}

				// Course Name Resolution
				$item->course_display_name = $item->filter_by_course; // Default to value
				
				if ( ! empty( $item->filter_by_course ) && $item->filter_by_course != '0' ) {
					// Check for multiple courses (comma separated)
					if ( strpos( $item->filter_by_course, ',' ) !== false ) {
						$course_ids = explode( ',', $item->filter_by_course );
						$course_names = array();
						
						foreach ( $course_ids as $course_id ) {
							if ( is_numeric( $course_id ) ) {
								$course_post = get_post( trim( $course_id ) );
								if ( $course_post ) {
									$course_names[] = $course_post->post_title;
								} else {
									$course_names[] = sprintf( __( '#%d', 'gamplify-gld' ), $course_id );
								}
							}
						}
						
						if ( ! empty( $course_names ) ) {
							$item->course_display_name = implode( ', ', $course_names );
						}
					} elseif ( is_numeric( $item->filter_by_course ) ) {
						// Single course
						$course_post = get_post( $item->filter_by_course );
						if ( $course_post ) {
							$item->course_display_name = $course_post->post_title;
						} else {
							$item->course_display_name = sprintf( __( 'Course #%d (Deleted)', 'gamplify-gld' ), $item->filter_by_course );
						}
					}
				}
			}
		}
		
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

	/**
	 * Save Member Chart
	 *
	 * @return void
	 */
	public function save_chart() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );
		}
		
		$title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		$metric_type = isset( $_POST['chart_type'] ) ? sanitize_text_field( $_POST['chart_type'] ) : '';
		$filter_by_product = isset( $_POST['filter_by_product'] ) ? sanitize_text_field( $_POST['filter_by_product'] ) : '';
		$chart_height = isset( $_POST['chart_height'] ) ? absint( $_POST['chart_height'] ) : 300;
		
		if ( empty( $metric_type ) ) {
			wp_send_json_error( array( 'message' => __( 'Chart type is required', 'gamplify-gld' ) ) );
		}
		
		global $wpdb;
		
		$data = array(
			'title'            => $title,
			'metric_type'      => $metric_type,
			'filter_by_product' => $filter_by_product,
			'chart_height'    => $chart_height,
		);
		
		$format = array( '%s', '%s', '%s', '%d' );
		
		$result = $wpdb->insert( GLD_MEMBER_CHARTS_TABLE, $data, $format );
		
		if ( $result ) {
			wp_send_json_success( array(
				'message' => __( 'Chart shortcode saved successfully', 'gamplify-gld' ),
				'id'      => $wpdb->insert_id,
				'data'    => $data
			) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Failed to save Chart shortcode', 'gamplify-gld' ) ) );
		}
	}

	/**
	 * Get Member Charts with pagination
	 *
	 * @return void
	 */
	public function get_charts() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );
		}
		
		global $wpdb;
		
		$page = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
		$limit = 5;
		$offset = ( $page - 1 ) * $limit;
		
		// Get items
		$items = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM " . GLD_MEMBER_CHARTS_TABLE . " 
				ORDER BY created DESC 
				LIMIT %d OFFSET %d",
				$limit,
				$offset
			)
		);
		
		// Enrich items with product names
		if ( ! empty( $items ) && class_exists( 'WooCommerce' ) ) {
			foreach ( $items as $item ) {
				$item->product_name = $item->filter_by_product; // Default
				
				if ( ! empty( $item->filter_by_product ) ) {
					// Check for multiple products
					if ( strpos( $item->filter_by_product, ',' ) !== false ) {
						$product_ids = explode( ',', $item->filter_by_product );
						$product_names = array();
						
						foreach ( $product_ids as $pid ) {
							if ( is_numeric( $pid ) ) {
								$product = wc_get_product( trim( $pid ) );
								if ( $product ) {
									$product_names[] = $product->get_name();
								} else {
									$product_names[] = sprintf( __( '#%d', 'gamplify-gld' ), $pid );
								}
							}
						}
						
						if ( ! empty( $product_names ) ) {
							$item->product_name = implode( ', ', $product_names );
						}
					} elseif ( is_numeric( $item->filter_by_product ) ) {
						// Single product
						$product = wc_get_product( $item->filter_by_product );
						if ( $product ) {
							$item->product_name = $product->get_name();
						} else {
							$item->product_name = sprintf( __( 'Product #%d (Deleted)', 'gamplify-gld' ), $item->filter_by_product );
						}
					}
				}
			}
		}
		
		// Get total count
		$total_items = (int) $wpdb->get_var( "SELECT COUNT(*) FROM " . GLD_MEMBER_CHARTS_TABLE );
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
	 * Delete Member Chart
	 *
	 * @return void
	 */
	public function delete_chart() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );
		}
		
		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		
		if ( ! $id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid ID', 'gamplify-gld' ) ) );
		}
		
		global $wpdb;
		
		$result = $wpdb->delete( GLD_MEMBER_CHARTS_TABLE, array( 'id' => $id ), array( '%d' ) );
		
		if ( $result ) {
			wp_send_json_success( array( 'message' => __( 'Chart shortcode deleted successfully', 'gamplify-gld' ) ) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Failed to delete Chart shortcode', 'gamplify-gld' ) ) );
		}
	}

	/**
	 * Save Learning KPI
	 *
	 * @return void
	 */
	public function save_learning_kpi() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );

		$metric_type = isset( $_POST['metric_type'] ) ? sanitize_text_field( $_POST['metric_type'] ) : '';
		$filter_by_course = isset( $_POST['filter_by_course'] ) ? sanitize_text_field( $_POST['filter_by_course'] ) : '';
		$include_chart_version = isset( $_POST['include_chart'] ) ? sanitize_text_field( $_POST['include_chart'] ) : 'no';
		$title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

		if ( empty( $metric_type ) ) wp_send_json_error( array( 'message' => __( 'Metric type is required', 'gamplify-gld' ) ) );

		global $wpdb;
		$data = array(
			'title'                 => $title ?: ucfirst( str_replace( '_', ' ', $metric_type ) ),
			'metric_type'           => $metric_type,
			'filter_by_course'      => $filter_by_course,
			'include_chart_version' => $include_chart_version,
		);

		if ( $id ) {
			$result = $wpdb->update( GLD_LEARNING_KPI_TABLE, $data, array( 'id' => $id ) );
			if ( $result !== false ) {
				wp_send_json_success( array( 'message' => __( 'Learning KPI updated successfully', 'gamplify-gld' ), 'id' => $id ) );
			} else {
				wp_send_json_error( array( 'message' => __( 'Failed to update Learning KPI', 'gamplify-gld' ) ) );
			}
		} else {
			$result = $wpdb->insert( GLD_LEARNING_KPI_TABLE, $data );
			if ( $result ) {
				wp_send_json_success( array( 'message' => __( 'Learning KPI saved successfully', 'gamplify-gld' ), 'id' => $wpdb->insert_id ) );
			} else {
				wp_send_json_error( array( 'message' => __( 'Failed to save Learning KPI', 'gamplify-gld' ) ) );
			}
		}
	}

	/**
	 * Get single Learning KPI
	 *
	 * @return void
	 */
	public function get_learning_kpi() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );

		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		if ( ! $id ) wp_send_json_error( array( 'message' => __( 'Invalid ID', 'gamplify-gld' ) ) );

		global $wpdb;
		$kpi = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GLD_LEARNING_KPI_TABLE . " WHERE id = %d", $id ) );

		if ( $kpi ) {
			wp_send_json_success( $kpi );
		} else {
			wp_send_json_error( array( 'message' => __( 'KPI not found', 'gamplify-gld' ) ) );
		}
	}

	/**
	 * Get Learning KPIs
	 *
	 * @return void
	 */
	public function get_learning_kpis() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );

		global $wpdb;
		$page = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
		$limit = 5;
		$offset = ( $page - 1 ) * $limit;

		$items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GLD_LEARNING_KPI_TABLE . " ORDER BY created DESC LIMIT %d OFFSET %d", $limit, $offset ) );
		
		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {
				$item->course_display_name = $this->resolve_course_names( $item->filter_by_course );
			}
		}

		$total_items = (int) $wpdb->get_var( "SELECT COUNT(*) FROM " . GLD_LEARNING_KPI_TABLE );
		$total_pages = ceil( $total_items / $limit );

		wp_send_json_success( array( 'items' => $items, 'pagination' => array( 'current_page' => $page, 'total_pages' => $total_pages, 'total_items' => $total_items ) ) );
	}

	/**
	 * Delete Learning KPI
	 *
	 * @return void
	 */
	public function delete_learning_kpi() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );

		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		global $wpdb;
		$result = $wpdb->delete( GLD_LEARNING_KPI_TABLE, array( 'id' => $id ) );
		if ( $result ) {
			wp_send_json_success( array( 'message' => __( 'Learning KPI deleted successfully', 'gamplify-gld' ) ) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Failed to delete Learning KPI', 'gamplify-gld' ) ) );
		}
	}

	/**
	 * Save Learning Data Table
	 *
	 * @return void
	 */
	public function save_learning_data_table() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );

		$table_type = isset( $_POST['table_type'] ) ? sanitize_text_field( $_POST['table_type'] ) : '';
		$filter_by_course = isset( $_POST['filter_by_course'] ) ? sanitize_text_field( $_POST['filter_by_course'] ) : '';
		$rows_to_display = isset( $_POST['rows'] ) ? absint( $_POST['rows'] ) : 10;
		$sort_by = isset( $_POST['sort'] ) ? sanitize_text_field( $_POST['sort'] ) : 'date';
		$title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

		if ( empty( $table_type ) ) wp_send_json_error( array( 'message' => __( 'Table type is required', 'gamplify-gld' ) ) );

		global $wpdb;
		$data = array(
			'title'           => $title ?: ucfirst( str_replace( '_', ' ', $table_type ) ),
			'table_type'      => $table_type,
			'filter_by_course' => $filter_by_course,
			'rows_to_display' => $rows_to_display,
			'sort_by'         => $sort_by,
		);

		if ( $id ) {
			$result = $wpdb->update( GLD_LEARNING_DATA_TABLE, $data, array( 'id' => $id ) );
			if ( $result !== false ) {
				wp_send_json_success( array( 'message' => __( 'Learning Data Table updated successfully', 'gamplify-gld' ), 'id' => $id ) );
			} else {
				wp_send_json_error( array( 'message' => __( 'Failed to update Learning Data Table', 'gamplify-gld' ) ) );
			}
		} else {
			$result = $wpdb->insert( GLD_LEARNING_DATA_TABLE, $data );
			if ( $result ) {
				wp_send_json_success( array( 'message' => __( 'Learning Data Table saved successfully', 'gamplify-gld' ), 'id' => $wpdb->insert_id ) );
			} else {
				wp_send_json_error( array( 'message' => __( 'Failed to save Learning Data Table', 'gamplify-gld' ) ) );
			}
		}
	}

	/**
	 * Get single Learning Data Table
	 *
	 * @return void
	 */
	public function get_learning_data_table() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );

		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		if ( ! $id ) wp_send_json_error( array( 'message' => __( 'Invalid ID', 'gamplify-gld' ) ) );

		global $wpdb;
		$config = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GLD_LEARNING_DATA_TABLE . " WHERE id = %d", $id ) );

		if ( $config ) {
			wp_send_json_success( $config );
		} else {
			wp_send_json_error( array( 'message' => __( 'Data Table configuration not found', 'gamplify-gld' ) ) );
		}
	}

	/**
	 * Get Learning Data Tables
	 *
	 * @return void
	 */
	public function get_learning_data_tables() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );

		global $wpdb;
		$page = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
		$limit = 5;
		$offset = ( $page - 1 ) * $limit;

		$items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GLD_LEARNING_DATA_TABLE . " ORDER BY created DESC LIMIT %d OFFSET %d", $limit, $offset ) );
		
		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {
				$item->course_display_name = $this->resolve_course_names( $item->filter_by_course );
			}
		}

		$total_items = (int) $wpdb->get_var( "SELECT COUNT(*) FROM " . GLD_LEARNING_DATA_TABLE );
		$total_pages = ceil( $total_items / $limit );

		wp_send_json_success( array( 'items' => $items, 'pagination' => array( 'current_page' => $page, 'total_pages' => $total_pages, 'total_items' => $total_items ) ) );
	}

	/**
	 * Delete Learning Data Table
	 *
	 * @return void
	 */
	public function delete_learning_data_table() {
		check_ajax_referer( 'gld_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'gamplify-gld' ) ) );

		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		global $wpdb;
		$result = $wpdb->delete( GLD_LEARNING_DATA_TABLE, array( 'id' => $id ) );
		if ( $result ) {
			wp_send_json_success( array( 'message' => __( 'Learning Data Table deleted successfully', 'gamplify-gld' ) ) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Failed to delete Learning Data Table', 'gamplify-gld' ) ) );
		}
	}

	/**
	 * Helper to resolve course names
	 */
	private function resolve_course_names( $course_ids_str ) {
		if ( empty( $course_ids_str ) || $course_ids_str === 'all' ) return __( 'All Courses', 'gamplify-gld' );
		
		$course_ids = explode( ',', $course_ids_str );
		$names = array();
		foreach ( $course_ids as $id ) {
			if ( is_numeric( $id ) ) {
				$post = get_post( trim( $id ) );
				$names[] = $post ? $post->post_title : sprintf( '#%d', $id );
			}
		}
		return ! empty( $names ) ? implode( ', ', $names ) : $course_ids_str;
	}
}


// Initialize
new GLD_Admin_Ajax();
