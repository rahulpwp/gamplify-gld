<?php
/**
 * Shortcodes
 *
 * @package    Gamplify_GLD
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Display statistics shortcode
 *
 * Usage: [gld_stats type="page_views" period="last_7_days"]
 *
 * @param array $atts Shortcode attributes
 * @return string
 */
function gld_stats_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'type'   => 'page_views',
			'period' => 'last_7_days',
			'user_id' => 0,
		),
		$atts,
		'gld_stats'
	);
	
	$date_range = gld_get_date_range( $atts['period'] );
	
	$args = array(
		'event_type' => $atts['type'],
		'start_date' => $date_range['start'],
		'end_date'   => $date_range['end'],
	);
	
	if ( $atts['user_id'] ) {
		$args['user_id'] = absint( $atts['user_id'] );
	}
	
	$count = gld_get_event_count( $args );
	
	ob_start();
	?>
	<div class="gld-stats-widget">
		<div class="gld-stat-value"><?php echo esc_html( gld_format_number( $count ) ); ?></div>
		<div class="gld-stat-label"><?php echo esc_html( ucwords( str_replace( '_', ' ', $atts['type'] ) ) ); ?></div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'gld_stats', 'gld_stats_shortcode' );


/**
 * Display user dashboard shortcode
 *
 * Usage: [gld_user_dashboard]
 *
 * @param array $atts Shortcode attributes
 * @return string
 */
function gld_user_dashboard_shortcode( $atts ) {
	if ( ! is_user_logged_in() ) {
		return '<p>' . __( 'Please log in to view your dashboard.', 'gamplify-gld' ) . '</p>';
	}
	
	$user_id = get_current_user_id();
	
	ob_start();
	include GLD_DIR . '/includes/public/views/user-dashboard.php';
	return ob_get_clean();
}
add_shortcode( 'gld_user_dashboard', 'gld_user_dashboard_shortcode' );

/**
 * Display leaderboard shortcode
 *
 * Usage: [gld_leaderboard type="page_views" limit="10"]
 *
 * @param array $atts Shortcode attributes
 * @return string
 */
function gld_leaderboard_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'type'   => 'page_views',
			'limit'  => 10,
			'period' => 'last_30_days',
		),
		$atts,
		'gld_leaderboard'
	);
	
	ob_start();
	?>
	<div class="gld-leaderboard">
		<h3><?php esc_html_e( 'Leaderboard', 'gamplify-gld' ); ?></h3>
		<div class="gld-leaderboard-list">
			<!-- Leaderboard data would be loaded here -->
			<p><?php esc_html_e( 'Loading leaderboard...', 'gamplify-gld' ); ?></p>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'gld_leaderboard', 'gld_leaderboard_shortcode' );

