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
