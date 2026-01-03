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
 * Display chart shortcode
 *
 * Usage: [gld_chart type="bar" event_type="page_view" period="last_7_days"]
 *
 * @param array $atts Shortcode attributes
 * @return string
 */
function gld_chart_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'type'       => 'bar',
			'event_type' => 'page_view',
			'period'     => 'last_7_days',
			'height'     => '300',
		),
		$atts,
		'gld_chart'
	);
	
	$chart_id = 'gld-chart-' . wp_generate_password( 8, false );
	
	wp_enqueue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', array(), '4.4.0', true );
	
	ob_start();
	?>
	<div class="gld-chart-container">
		<canvas id="<?php echo esc_attr( $chart_id ); ?>" height="<?php echo esc_attr( $atts['height'] ); ?>"></canvas>
	</div>
	<script>
	jQuery(document).ready(function($) {
		var ctx = document.getElementById('<?php echo esc_js( $chart_id ); ?>').getContext('2d');
		// Chart data would be loaded via AJAX
		var chart = new Chart(ctx, {
			type: '<?php echo esc_js( $atts['type'] ); ?>',
			data: {
				labels: [],
				datasets: [{
					label: '<?php echo esc_js( $atts['event_type'] ); ?>',
					data: [],
					backgroundColor: 'rgba(54, 162, 235, 0.2)',
					borderColor: 'rgba(54, 162, 235, 1)',
					borderWidth: 1
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false
			}
		});
		
		// Load chart data via AJAX
		$.ajax({
			url: gld_public.ajax_url,
			type: 'POST',
			data: {
				action: 'gld_get_chart_data',
				nonce: gld_public.nonce,
				event_type: '<?php echo esc_js( $atts['event_type'] ); ?>',
				period: '<?php echo esc_js( $atts['period'] ); ?>'
			},
			success: function(response) {
				if (response.success) {
					chart.data.labels = response.data.labels;
					chart.data.datasets[0].data = response.data.values;
					chart.update();
				}
			}
		});
	});
	</script>
	<?php
	return ob_get_clean();
}
add_shortcode( 'gld_chart', 'gld_chart_shortcode' );

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
