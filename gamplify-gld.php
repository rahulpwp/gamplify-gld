<?php
/**
 * Gamplify GLD - WordPress Plugin
 *
 * @package           Gamplify_GLD
 * @author            Tribepub Team
 * @copyright         2026 Tribepub
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Gamplify GLD
 * Plugin URI:        https://tribepub.com
 * Description:       Gamplify Logs & Data - Advanced analytics and reporting system for WordPress
 * Version:           1.0.0
 * Author:            Tribepub Team
 * Author URI:        https://tribepub.com/
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gamplify-gld
 * Domain Path:       /languages
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define plugin constants
define( 'GLD_VERSION', '1.0.0' );
define( 'GLD_DEBUG', false );

define( 'GLD_FILE_NAME_PREFIX', 'gld' );
define( 'GLD_DIR', __DIR__ );
define( 'GLD_URL', plugin_dir_url( __FILE__ ) );
define( 'GLD_LOG_FILE', GLD_DIR . '/error_log.txt' );
define( 'GLD_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( 'GLD_PREFIX', 'gld_' );

// Database table names
global $wpdb;
define( 'GLD_EVENTS_TABLE', $wpdb->prefix . 'gld_events' );
define( 'GLD_REPORTS_TABLE', $wpdb->prefix . 'gld_reports' );
define( 'GLD_SESSIONS_TABLE', $wpdb->prefix . 'gld_sessions' );
define( 'GLD_MEMBER_KPI_TABLE', $wpdb->prefix . 'gld_member_kpi' );

/**
 * Plugin activation handler
 *
 * @since 1.0.0
 * @return void
 */
function activate_gld() {
	try {
		require_once GLD_DIR . '/includes/class-gld-activator.php';
		GLD_Activator::activate();
	} catch ( Exception $e ) {
		gld_log_error( 'Activation failed: ' . $e->getMessage() );
	}
}

/**
 * Plugin deactivation handler
 *
 * @since 1.0.0
 * @return void
 */
function deactivate_gld() {
	try {
		require_once GLD_DIR . '/includes/class-gld-deactivator.php';
		GLD_Deactivator::deactivate();
	} catch ( Exception $e ) {
		gld_log_error( 'Deactivation failed: ' . $e->getMessage() );
	}
}

register_activation_hook( __FILE__, 'activate_gld' );
register_deactivation_hook( __FILE__, 'deactivate_gld' );

// Load core files
$core_files = array(
	'includes/gld-functions.php',
	'includes/gld-database.php',
	'includes/class-gld.php',
);

foreach ( $core_files as $file ) {
	$file_path = GLD_DIR . '/' . $file;
	if ( file_exists( $file_path ) ) {
		require_once $file_path;
	} else {
		gld_log_error( 'Core file missing: ' . $file );
	}
}

// Load admin files
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	$admin_files = array(
		'includes/admin/class-gld-admin.php',
		'includes/admin/class-gld-admin-ajax.php',
		'includes/admin/class-gld-settings.php',
		'includes/admin/class-gld-dashboard.php',
		'includes/admin/class-gld-reports.php',
		'includes/admin/class-gld-analytics-engine.php',
		'includes/admin/class-gld-data-export.php',
	);

	foreach ( $admin_files as $file ) {
		$file_path = GLD_DIR . '/' . $file;
		if ( file_exists( $file_path ) ) {
			require_once $file_path;
		} else {
			gld_log_error( 'Admin file missing: ' . $file );
		}
	}
}

// Load public files
$public_files = array(
	'includes/public/class-gld-public.php',
	'includes/public/class-gld-public-ajax.php',
	'includes/public/class-gld-tracker.php',
	'includes/public/class-gld-frontend-reports.php',
);

foreach ( $public_files as $file ) {
	$file_path = GLD_DIR . '/' . $file;
	if ( file_exists( $file_path ) ) {
		require_once $file_path;
	} else {
		gld_log_error( 'Public file missing: ' . $file );
	}
}

// Load shortcodes
require_once GLD_DIR . '/includes/gld-shortcodes.php';

/**
 * Initialize plugin
 *
 * @since 1.0.0
 * @return void
 */
function run_gld() {
	try {
		$plugin = new GLD();
		$plugin->run();
	} catch ( Exception $e ) {
		gld_log_error( 'Plugin initialization failed: ' . $e->getMessage() );
	}
}

run_gld();
