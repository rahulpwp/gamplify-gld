<?php
/**
 * Helper Functions
 *
 * @package    Gamplify_GLD
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Log error messages
 *
 * @param string $message Error message to log
 * @return void
 */
function gld_log_error( $message ) {
	if ( GLD_DEBUG ) {
		error_log( '[GLD] ' . $message );
		
		if ( defined( 'GLD_LOG_FILE' ) ) {
			$log_message = '[' . date( 'Y-m-d H:i:s' ) . '] ' . $message . PHP_EOL;
			file_put_contents( GLD_LOG_FILE, $log_message, FILE_APPEND );
		}
	}
}

/**
 * Get plugin settings
 *
 * @param string $key     Setting key
 * @param mixed  $default Default value
 * @return mixed
 */
function gld_get_setting( $key = '', $default = false ) {
	$settings = get_option( 'gld_settings', array() );
	
	if ( empty( $key ) ) {
		return $settings;
	}
	
	return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
}

/**
 * Update plugin setting
 *
 * @param string $key   Setting key
 * @param mixed  $value Setting value
 * @return bool
 */
function gld_update_setting( $key, $value ) {
	$settings = get_option( 'gld_settings', array() );
	$settings[ $key ] = $value;
	
	return update_option( 'gld_settings', $settings );
}

/**
 * Format number for display
 *
 * @param int|float $number Number to format
 * @return string
 */
function gld_format_number( $number ) {
	return number_format( $number );
}

/**
 * Format percentage for display
 *
 * @param float $number    Number to format
 * @param int   $decimals  Number of decimal places
 * @return string
 */
function gld_format_percentage( $number, $decimals = 2 ) {
	return number_format( $number, $decimals ) . '%';
}

/**
 * Get date range presets
 *
 * @return array
 */
function gld_get_date_range_presets() {
	return array(
		'today'        => __( 'Today', 'gamplify-gld' ),
		'yesterday'    => __( 'Yesterday', 'gamplify-gld' ),
		'last_7_days'  => __( 'Last 7 Days', 'gamplify-gld' ),
		'last_30_days' => __( 'Last 30 Days', 'gamplify-gld' ),
		'this_month'   => __( 'This Month', 'gamplify-gld' ),
		'last_month'   => __( 'Last Month', 'gamplify-gld' ),
		'this_year'    => __( 'This Year', 'gamplify-gld' ),
		'custom'       => __( 'Custom Range', 'gamplify-gld' ),
	);
}

/**
 * Get date range from preset
 *
 * @param string $preset Preset key
 * @return array Array with 'start' and 'end' dates
 */
function gld_get_date_range( $preset = 'last_7_days' ) {
	$end   = current_time( 'Y-m-d 23:59:59' );
	$start = current_time( 'Y-m-d 00:00:00' );
	
	switch ( $preset ) {
		case 'today':
			// Already set
			break;
			
		case 'yesterday':
			$start = date( 'Y-m-d 00:00:00', strtotime( '-1 day' ) );
			$end   = date( 'Y-m-d 23:59:59', strtotime( '-1 day' ) );
			break;
			
		case 'last_7_days':
			$start = date( 'Y-m-d 00:00:00', strtotime( '-7 days' ) );
			break;
			
		case 'last_30_days':
			$start = date( 'Y-m-d 00:00:00', strtotime( '-30 days' ) );
			break;
			
		case 'this_month':
			$start = date( 'Y-m-01 00:00:00' );
			break;
			
		case 'last_month':
			$start = date( 'Y-m-01 00:00:00', strtotime( 'first day of last month' ) );
			$end   = date( 'Y-m-t 23:59:59', strtotime( 'last day of last month' ) );
			break;
			
		case 'this_year':
			$start = date( 'Y-01-01 00:00:00' );
			break;
	}
	
	return array(
		'start' => $start,
		'end'   => $end,
	);
}

/**
 * Sanitize event data
 *
 * @param array $data Event data
 * @return array
 */
function gld_sanitize_event_data( $data ) {
	$sanitized = array();
	
	foreach ( $data as $key => $value ) {
		if ( is_array( $value ) ) {
			$sanitized[ $key ] = gld_sanitize_event_data( $value );
		} else {
			$sanitized[ $key ] = sanitize_text_field( $value );
		}
	}
	
	return $sanitized;
}

/**
 * Check if user has analytics access
 *
 * @param int $user_id User ID (default current user)
 * @return bool
 */
function gld_user_can_view_analytics( $user_id = 0 ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}
	
	$user = get_userdata( $user_id );
	
	if ( ! $user ) {
		return false;
	}
	
	return user_can( $user, 'manage_options' ) || user_can( $user, 'gld_view_analytics' );
}

/**
 * Anonymize IP address
 *
 * @param string $ip IP address
 * @return string
 */
function gld_anonymize_ip( $ip ) {
	// IPv4
	if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
		return preg_replace( '/\.\d+$/', '.0', $ip );
	}
	
	// IPv6
	if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
		return preg_replace( '/:[^:]+$/', ':0', $ip );
	}
	
	return '';
}

/**
 * Get user IP address
 *
 * @return string
 */
function gld_get_user_ip() {
	$ip = '';
	
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	return gld_anonymize_ip( $ip );
}

/**
 * Get device type from user agent
 *
 * @param string $user_agent User agent string
 * @return string
 */
function gld_get_device_type( $user_agent = '' ) {
	if ( empty( $user_agent ) ) {
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
	}
	
	if ( preg_match( '/mobile|android|iphone|ipad|phone/i', $user_agent ) ) {
		return 'mobile';
	}
	
	if ( preg_match( '/tablet|ipad/i', $user_agent ) ) {
		return 'tablet';
	}
	
	return 'desktop';
}

/**
 * Get active member count for specific products
 *
 * @param string|array $product_ids Product IDs (comma-separated or array)
 * @return int Active count
 */
function gld_get_active_member_count( $product_ids ) {
	if ( ! class_exists( 'WC_Subscriptions' ) ) {
		return 0;
	}

	if ( is_string( $product_ids ) ) {
		$product_ids = explode( ',', $product_ids );
	}
	
	$product_ids = array_map( 'absint', (array) $product_ids );
	$product_ids = array_filter( $product_ids ); // Remove empty
	
	if ( empty( $product_ids ) ) {
		return 0;
	}

	// Query active subscriptions
	// Note: wcs_get_subscriptions args are slightly different than get_posts
	$args = array(
		'subscription_status' => 'active',
		'subscriptions_per_page' => -1,
		'product_id' => $product_ids,
		'return' => 'ids', // Performance: just get IDs
	);
	
	$subscriptions = wcs_get_subscriptions( $args );
	
	return count( $subscriptions );
}

/**
 * Get churned member count for specific products
 *
 * @param string|array $product_ids Product IDs (comma-separated or array)
 * @return int Churned count
 */
function gld_get_churned_member_count( $product_ids ) {
	if ( ! class_exists( 'WC_Subscriptions' ) ) {
		return 0;
	}

	if ( is_string( $product_ids ) ) {
		$product_ids = explode( ',', $product_ids );
	}
	
	$product_ids = array_map( 'absint', (array) $product_ids );
	$product_ids = array_filter( $product_ids );
	
	if ( empty( $product_ids ) ) {
		return 0;
	}

	$args = array(
		'subscription_status' => array( 'cancelled', 'expired', 'trash' ),
		'subscriptions_per_page' => -1,
		'product_id' => $product_ids,
		'return' => 'ids',
	);
	
	$subscriptions = wcs_get_subscriptions( $args );
	
	return count( $subscriptions );
}

/**
 * Get learning metric count
 *
 * @param string $metric_type Metric type
 * @param string|array $course_ids Course IDs
 * @return int Count
 */
function gld_get_learning_metric_count( $metric_type, $course_ids ) {
	if ( ! class_exists( 'SFWD_LMS' ) ) {
		return 0;
	}

	global $wpdb;
	$count = 0;

	// Normalize course_ids
	if ( is_string( $course_ids ) ) {
		if ( $course_ids === 'all' || $course_ids === '0' || empty( $course_ids ) ) {
			$course_ids = 'all';
		} else {
			$course_ids = explode( ',', $course_ids );
		}
	}
	
	if ( is_array( $course_ids ) ) {
		$course_ids = array_map( 'absint', $course_ids );
		$course_ids = array_filter( $course_ids );
	}

	switch ( $metric_type ) {
		case 'course_completed':
			if ( $course_ids === 'all' ) {
				$count = $wpdb->get_var( "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->usermeta} WHERE meta_key LIKE 'course_completed_%' AND meta_value != ''" );
			} else {
				$keys = array_map( function( $id ) {
					return 'course_completed_' . $id;
				}, $course_ids );
				$placeholders = implode( ',', array_fill( 0, count( $keys ), '%s' ) );
				$query        = $wpdb->prepare( "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->usermeta} WHERE meta_key IN ($placeholders) AND meta_value != ''", $keys );
				$count        = $wpdb->get_var( $query );
			}
			break;

		case 'course_in_progress':
			$activity_table = $wpdb->prefix . 'learndash_user_activity';
			// Check if table exists
			$table_exists = $wpdb->get_var( "SHOW TABLES LIKE '$activity_table'" );
			
			if ( $table_exists ) {
				if ( $course_ids === 'all' ) {
					$count = $wpdb->get_var( "SELECT COUNT(DISTINCT user_id) FROM $activity_table WHERE activity_type = 'course' AND activity_status = 0" );
				} else {
					$placeholders = implode( ',', array_fill( 0, count( $course_ids ), '%d' ) );
					$query = $wpdb->prepare( "SELECT COUNT(DISTINCT user_id) FROM $activity_table WHERE activity_type = 'course' AND activity_status = 0 AND post_id IN ($placeholders)", $course_ids );
					$count = $wpdb->get_var( $query );
				}
			} else {
				// Fallback if no activity table (older LD)
				$count = 0; 
			}
			break;

		case 'quiz_completed':
			$activity_table = $wpdb->prefix . 'learndash_user_activity';
			$table_exists = $wpdb->get_var( "SHOW TABLES LIKE '$activity_table'" );
			
			if ( $table_exists ) {
				if ( $course_ids === 'all' ) {
					$count = $wpdb->get_var( "SELECT COUNT(*) FROM $activity_table WHERE activity_type = 'quiz' AND activity_status = 1" );
				} else {
					$placeholders = implode( ',', array_fill( 0, count( $course_ids ), '%d' ) );
					$query = $wpdb->prepare( "SELECT COUNT(*) FROM $activity_table WHERE activity_type = 'quiz' AND activity_status = 1 AND course_id IN ($placeholders)", $course_ids );
					$count = $wpdb->get_var( $query );
				}
			} else {
				$count = 0;
			}
			break;
	}

	return (int) $count;
}

/**
 * Get learning table data for Course Completion Summary
 *
 * @param array|string $course_ids Array of course IDs or 'all'
 * @param int $per_page
 * @param int $page
 * @param string $sort_by
 * @return array
 */
function gld_get_learning_table_data( $course_ids = 'all', $per_page = 10, $page = 1, $sort_by = 'course_name' ) {
	if ( ! class_exists( 'SFWD_LMS' ) ) {
		return array( 'items' => array(), 'total' => 0, 'pages' => 0 );
	}

	global $wpdb;

	// Normalize course_ids
	if ( is_string( $course_ids ) ) {
		if ( $course_ids === 'all' || $course_ids === '0' || empty( $course_ids ) ) {
			$course_ids = 'all';
		} else {
			$course_ids = explode( ',', $course_ids );
		}
	}

	$args = array(
		'post_type'      => 'sfwd-courses',
		'post_status'    => 'publish',
		'posts_per_page' => $per_page,
		'paged'          => $page,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	if ( is_array( $course_ids ) ) {
		$args['post__in'] = array_map( 'absint', $course_ids );
	}

	$query = new WP_Query( $args );
	$items = array();

	if ( $query->have_posts() ) {
		foreach ( $query->posts as $course ) {
			$course_id = (int) $course->ID;
			
			// Get Students (Enrolled) - Check both meta and activity table for robustness
			$student_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->usermeta} WHERE meta_key = %s", 'course_' . $course_id . '_access_from' ) );
			
			// Get Completions
			$completions = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value != ''", 'course_completed_' . $course_id ) );
			
			// Calculate Avg Score as completion percentage (Completions / Students)
			// as requested: "Avg Score : Students / Completions to get the percent"
			$avg_score = 0;
			if ( $student_count > 0 ) {
				$avg_score = ( $completions / $student_count ) * 100;
			}

			$items[] = array(
				'course_id'   => $course_id,
				'course_name' => $course->post_title,
				'students'    => (int) $student_count,
				'completions' => (int) $completions,
				'avg_score'   => round( $avg_score, 1 ),
			);
		}
	}

	return array(
		'items' => $items,
		'total' => (int) $query->found_posts,
		'pages' => (int) $query->max_num_pages,
	);
}

/**
 * Get drilldown data for a specific course (Lessons & Quizzes)
 *
 * @param int $course_id
 * @return array
 */
function gld_get_course_drilldown_data( $course_id ) {
	if ( ! class_exists( 'SFWD_LMS' ) ) {
		return array( 'lessons' => array(), 'quizzes' => array() );
	}

	global $wpdb;
	$course_id = absint( $course_id );
	
	// 1. Get total students in course for average calculation
	$total_students = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->usermeta} WHERE meta_key = %s", 'course_' . $course_id . '_access_from' ) );
	$total_students = max( 1, (int) $total_students );

	// 2. Get Lessons (Progress)
	$lessons_list = learndash_get_course_lessons_list( $course_id );
	$lessons_data = array();
	
	if ( ! empty( $lessons_list ) ) {
		foreach ( $lessons_list as $lesson ) {
			$lesson_id = $lesson['post']->ID;
			
			// Count how many users completed this lesson
			// LearnDash stores lesson completion in user meta: _sfwd-lessons-completed
			// But for aggregation, it's easier to check activity table if available
			$activity_table = $wpdb->prefix . 'learndash_user_activity';
			$completed_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT user_id) FROM $activity_table WHERE post_id = %d AND activity_type = 'lesson' AND activity_status = 1", $lesson_id ) );
			
			$lessons_data[] = array(
				'title'    => $lesson['post']->post_title,
				'progress' => round( ( (int) $completed_count / $total_students ) * 100 ),
			);
		}
	}

	// 3. Get Quizzes (Assessments) - Comprehensive search
	$quizzes_data = array();
	$quiz_ids     = array();

	// Method 1: Get quizzes from course steps (most comprehensive)
	if ( function_exists( 'learndash_get_course_steps' ) ) {
		$steps = learndash_get_course_steps( $course_id, array( 'sfwd-quiz' ) );
		if ( ! empty( $steps ) ) {
			$quiz_ids = array_merge( $quiz_ids, $steps );
		}
	}

	// Method 2: Get quizzes directly attached to course (fallback/standard)
	$quizzes_list = learndash_get_course_quiz_list( $course_id );
	if ( ! empty( $quizzes_list ) ) {
		foreach ( $quizzes_list as $quiz ) {
			$quiz_ids[] = $quiz['post']->ID;
		}
	}

	// Method 3: Check activity table for any quiz touched in this course context
	$activity_table = $wpdb->prefix . 'learndash_user_activity';
	$touched_quizzes = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT post_id FROM $activity_table WHERE course_id = %d AND activity_type = 'quiz'", $course_id ) );
	if ( ! empty( $touched_quizzes ) ) {
		$quiz_ids = array_merge( $quiz_ids, array_map( 'absint', $touched_quizzes ) );
	}

	// Unique quiz IDs
	$quiz_ids = array_unique( $quiz_ids );

	if ( ! empty( $quiz_ids ) ) {
		$wpdb->hide_errors();
		
		foreach ( $quiz_ids as $quiz_id ) {
			$quiz_post = get_post( $quiz_id );
			if ( ! $quiz_post || $quiz_post->post_type !== 'sfwd-quiz' ) {
				continue;
			}

			// Count unique students who attended/took the quiz in this course context
			$attended_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT user_id) FROM $activity_table WHERE post_id = %d AND activity_type = 'quiz' AND course_id = %d", $quiz_id, $course_id ) );
			
			$attendance_percent = round( ( (int) $attended_count / $total_students ) * 100 );

			$quizzes_data[] = array(
				'title'    => $quiz_post->post_title,
				'progress' => $attendance_percent, // Use same key as lessons for consistency
			);
		}
		$wpdb->show_errors();
	}

	return array(
		'lessons' => $lessons_data,
		'quizzes' => $quizzes_data,
	);
}
