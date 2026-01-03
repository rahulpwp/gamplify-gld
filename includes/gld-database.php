<?php
/**
 * Database Schema and Queries
 *
 * @package    Gamplify_GLD
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Create database tables
 *
 * @return void
 */
function gld_create_tables() {
	global $wpdb;
	
	$charset_collate = $wpdb->get_charset_collate();
	
	// Events table
	$events_table = GLD_EVENTS_TABLE;
	$events_sql   = "CREATE TABLE IF NOT EXISTS {$events_table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		event_type varchar(50) NOT NULL,
		event_name varchar(255) NOT NULL,
		user_id bigint(20) unsigned NOT NULL DEFAULT 0,
		session_id varchar(100) NOT NULL,
		event_data longtext,
		ip_address varchar(45),
		user_agent text,
		created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (id),
		KEY event_type (event_type),
		KEY user_id (user_id),
		KEY session_id (session_id),
		KEY created_at (created_at)
	) $charset_collate;";
	
	// Reports table
	$reports_table = GLD_REPORTS_TABLE;
	$reports_sql   = "CREATE TABLE IF NOT EXISTS {$reports_table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		report_name varchar(255) NOT NULL,
		report_type varchar(50) NOT NULL,
		report_config longtext,
		created_by bigint(20) unsigned NOT NULL,
		is_scheduled tinyint(1) NOT NULL DEFAULT 0,
		schedule_config longtext,
		created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (id),
		KEY report_type (report_type),
		KEY created_by (created_by),
		KEY is_scheduled (is_scheduled)
	) $charset_collate;";
	
	// Sessions table
	$sessions_table = GLD_SESSIONS_TABLE;
	$sessions_sql   = "CREATE TABLE IF NOT EXISTS {$sessions_table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		session_id varchar(100) NOT NULL,
		user_id bigint(20) unsigned NOT NULL DEFAULT 0,
		start_time datetime NOT NULL,
		end_time datetime,
		page_count int(11) NOT NULL DEFAULT 0,
		referrer text,
		device_type varchar(20),
		PRIMARY KEY  (id),
		UNIQUE KEY session_id (session_id),
		KEY user_id (user_id),
		KEY start_time (start_time)
	) $charset_collate;";
	
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $events_sql );
	dbDelta( $reports_sql );
	dbDelta( $sessions_sql );

	// Member KPI table
	$member_kpi_table = GLD_MEMBER_KPI_TABLE;
	$member_kpi_sql   = "CREATE TABLE IF NOT EXISTS {$member_kpi_table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		title varchar(255) NOT NULL,
		metric_type varchar(50) NOT NULL,
		filter_by_course varchar(50) NOT NULL DEFAULT 0,
		include_chart_version varchar(50) NOT NULL DEFAULT 0,
		created datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (id)
	) $charset_collate;";
	
	dbDelta( $member_kpi_sql );

	// Member Charts table
	$member_charts_table = GLD_MEMBER_CHARTS_TABLE;
	$member_charts_sql   = "CREATE TABLE IF NOT EXISTS {$member_charts_table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		title varchar(255) NOT NULL,
		metric_type varchar(50) NOT NULL,
		filter_by_product varchar(50) NOT NULL DEFAULT 0,
		chart_height int(11) NOT NULL DEFAULT 300,
		created datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (id)
	) $charset_collate;";
	
	dbDelta( $member_charts_sql );
	
	// Update database version
	update_option( 'gld_db_version', GLD_VERSION );
}

/**
 * Track event
 *
 * @param string $event_type Event type
 * @param string $event_name Event name
 * @param array  $event_data Event data
 * @param int    $user_id    User ID
 * @return int|false Event ID or false on failure
 */
function gld_track_event( $event_type, $event_name, $event_data = array(), $user_id = 0 ) {
	global $wpdb;
	
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}
	
	$session_id = gld_get_session_id();
	
	$data = array(
		'event_type' => sanitize_text_field( $event_type ),
		'event_name' => sanitize_text_field( $event_name ),
		'user_id'    => absint( $user_id ),
		'session_id' => $session_id,
		'event_data' => wp_json_encode( gld_sanitize_event_data( $event_data ) ),
		'ip_address' => gld_get_user_ip(),
		'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( $_SERVER['HTTP_USER_AGENT'], 0, 255 ) : '',
	);
	
	$result = $wpdb->insert( GLD_EVENTS_TABLE, $data );
	
	if ( $result ) {
		return $wpdb->insert_id;
	}
	
	return false;
}

/**
 * Get or create session ID
 *
 * @return string
 */
function gld_get_session_id() {
	if ( isset( $_COOKIE['gld_session_id'] ) ) {
		return sanitize_text_field( $_COOKIE['gld_session_id'] );
	}
	
	$session_id = wp_generate_password( 32, false );
	setcookie( 'gld_session_id', $session_id, time() + ( 30 * MINUTE_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN );
	
	return $session_id;
}

/**
 * Get events by criteria
 *
 * @param array $args Query arguments
 * @return array
 */
function gld_get_events( $args = array() ) {
	global $wpdb;
	
	$defaults = array(
		'event_type' => '',
		'user_id'    => 0,
		'start_date' => '',
		'end_date'   => '',
		'limit'      => 100,
		'offset'     => 0,
		'orderby'    => 'created_at',
		'order'      => 'DESC',
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	$where = array( '1=1' );
	
	if ( ! empty( $args['event_type'] ) ) {
		$where[] = $wpdb->prepare( 'event_type = %s', $args['event_type'] );
	}
	
	if ( ! empty( $args['user_id'] ) ) {
		$where[] = $wpdb->prepare( 'user_id = %d', $args['user_id'] );
	}
	
	if ( ! empty( $args['start_date'] ) ) {
		$where[] = $wpdb->prepare( 'created_at >= %s', $args['start_date'] );
	}
	
	if ( ! empty( $args['end_date'] ) ) {
		$where[] = $wpdb->prepare( 'created_at <= %s', $args['end_date'] );
	}
	
	$where_clause = implode( ' AND ', $where );
	
	$orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );
	
	$sql = "SELECT * FROM " . GLD_EVENTS_TABLE . " 
			WHERE {$where_clause} 
			ORDER BY {$orderby} 
			LIMIT %d OFFSET %d";
	
	return $wpdb->get_results(
		$wpdb->prepare( $sql, $args['limit'], $args['offset'] )
	);
}

/**
 * Get event count
 *
 * @param array $args Query arguments
 * @return int
 */
function gld_get_event_count( $args = array() ) {
	global $wpdb;
	
	$defaults = array(
		'event_type' => '',
		'user_id'    => 0,
		'start_date' => '',
		'end_date'   => '',
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	$where = array( '1=1' );
	
	if ( ! empty( $args['event_type'] ) ) {
		$where[] = $wpdb->prepare( 'event_type = %s', $args['event_type'] );
	}
	
	if ( ! empty( $args['user_id'] ) ) {
		$where[] = $wpdb->prepare( 'user_id = %d', $args['user_id'] );
	}
	
	if ( ! empty( $args['start_date'] ) ) {
		$where[] = $wpdb->prepare( 'created_at >= %s', $args['start_date'] );
	}
	
	if ( ! empty( $args['end_date'] ) ) {
		$where[] = $wpdb->prepare( 'created_at <= %s', $args['end_date'] );
	}
	
	$where_clause = implode( ' AND ', $where );
	
	$sql = "SELECT COUNT(*) FROM " . GLD_EVENTS_TABLE . " WHERE {$where_clause}";
	
	return (int) $wpdb->get_var( $sql );
}

/**
 * Delete old events
 *
 * @param int $days Number of days to keep
 * @return int|false Number of rows deleted or false on failure
 */
function gld_cleanup_old_events( $days = 90 ) {
	global $wpdb;
	
	$date = date( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );
	
	return $wpdb->query(
		$wpdb->prepare(
			"DELETE FROM " . GLD_EVENTS_TABLE . " WHERE created_at < %s",
			$date
		)
	);
}
