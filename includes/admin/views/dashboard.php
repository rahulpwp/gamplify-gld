<?php
/**
 * Admin Dashboard View
 *
 * @package    Gamplify_GLD
 * @subpackage Gamplify_GLD/admin/views
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<div class="wrap gld-dashboard">
	<h1><?php esc_html_e( 'Analytics Dashboard', 'gamplify-gld' ); ?></h1>
	
	<div class="gld-date-filter">
		<select id="gld-period-select">
			<option value="today"><?php esc_html_e( 'Today', 'gamplify-gld' ); ?></option>
			<option value="yesterday"><?php esc_html_e( 'Yesterday', 'gamplify-gld' ); ?></option>
			<option value="last_7_days" selected><?php esc_html_e( 'Last 7 Days', 'gamplify-gld' ); ?></option>
			<option value="last_30_days"><?php esc_html_e( 'Last 30 Days', 'gamplify-gld' ); ?></option>
			<option value="this_month"><?php esc_html_e( 'This Month', 'gamplify-gld' ); ?></option>
		</select>
	</div>
	
	<div class="gld-stats-grid">
		<div class="gld-stat-card">
			<h3><?php esc_html_e( 'Total Events', 'gamplify-gld' ); ?></h3>
			<div class="gld-stat-value" id="total-events">-</div>
		</div>
		
		<div class="gld-stat-card">
			<h3><?php esc_html_e( 'Page Views', 'gamplify-gld' ); ?></h3>
			<div class="gld-stat-value" id="page-views">-</div>
		</div>
		
		<div class="gld-stat-card">
			<h3><?php esc_html_e( 'Unique Users', 'gamplify-gld' ); ?></h3>
			<div class="gld-stat-value" id="unique-users">-</div>
		</div>
		
		<div class="gld-stat-card">
			<h3><?php esc_html_e( 'Avg. Session Time', 'gamplify-gld' ); ?></h3>
			<div class="gld-stat-value" id="avg-session-time">-</div>
		</div>
	</div>
	
	<div class="gld-charts-section">
		<div class="gld-chart-container">
			<h2><?php esc_html_e( 'Page Views Over Time', 'gamplify-gld' ); ?></h2>
			<canvas id="page-views-chart"></canvas>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	function loadDashboardStats(period) {
		$.ajax({
			url: gld_admin.ajax_url,
			type: 'POST',
			data: {
				action: 'gld_get_dashboard_stats',
				nonce: gld_admin.nonce,
				period: period
			},
			success: function(response) {
				if (response.success) {
					$('#total-events').text(response.data.total_events.toLocaleString());
					$('#page-views').text(response.data.page_views.toLocaleString());
					$('#unique-users').text(response.data.unique_users.toLocaleString());
					$('#avg-session-time').text(response.data.avg_session_time + ' min');
				}
			}
		});
	}
	
	$('#gld-period-select').on('change', function() {
		loadDashboardStats($(this).val());
	});
	
	loadDashboardStats('last_7_days');
});
</script>
