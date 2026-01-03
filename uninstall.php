<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package    Gamplify_GLD
 * @since      1.0.0
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Define table names
$events_table   = $wpdb->prefix . 'gld_events';
$reports_table  = $wpdb->prefix . 'gld_reports';
$sessions_table = $wpdb->prefix . 'gld_sessions';

// Delete options
delete_option( 'gld_version' );
delete_option( 'gld_settings' );
delete_option( 'gld_db_version' );

// Drop tables (optional - uncomment if you want to remove data on uninstall)
// $wpdb->query( "DROP TABLE IF EXISTS {$events_table}" );
// $wpdb->query( "DROP TABLE IF EXISTS {$reports_table}" );
// $wpdb->query( "DROP TABLE IF EXISTS {$sessions_table}" );

// Clear any cached data
wp_cache_flush();
