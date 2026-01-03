<?php
/**
 * Reports Tab Content
 *
 * @package    Gamplify_GLD
 * @subpackage Gamplify_GLD/admin/views/tabs
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<div class="gld-tab-panel">
	<div class="gld-panel-header">
		<span class="dashicons dashicons-chart-bar gld-panel-icon"></span>
		<div class="gld-panel-title-wrapper">
			<h2 class="gld-panel-title"><?php esc_html_e( 'Reports & Analytics', 'gamplify-gld' ); ?></h2>
			<p class="gld-panel-description"><?php esc_html_e( 'Generate comprehensive reports and analytics shortcodes', 'gamplify-gld' ); ?></p>
		</div>
	</div>

	<div class="gld-section">
		<h3 class="gld-section-title"><?php esc_html_e( 'Report Generator', 'gamplify-gld' ); ?></h3>
		<p class="gld-section-description"><?php esc_html_e( 'Create custom reports with various data visualizations', 'gamplify-gld' ); ?></p>

		<div class="gld-form-grid">
			<div class="gld-form-group">
				<label for="report-type"><?php esc_html_e( 'Report Type', 'gamplify-gld' ); ?> *</label>
				<select id="report-type" class="gld-select">
					<option value=""><?php esc_html_e( 'Select report type', 'gamplify-gld' ); ?></option>
					<option value="user_activity"><?php esc_html_e( 'User Activity Report', 'gamplify-gld' ); ?></option>
					<option value="engagement"><?php esc_html_e( 'Engagement Report', 'gamplify-gld' ); ?></option>
					<option value="performance"><?php esc_html_e( 'Performance Metrics', 'gamplify-gld' ); ?></option>
					<option value="custom"><?php esc_html_e( 'Custom Report', 'gamplify-gld' ); ?></option>
				</select>
			</div>

			<div class="gld-form-group">
				<label for="date-range"><?php esc_html_e( 'Date Range', 'gamplify-gld' ); ?></label>
				<select id="date-range" class="gld-select">
					<option value="7days"><?php esc_html_e( 'Last 7 Days', 'gamplify-gld' ); ?></option>
					<option value="30days"><?php esc_html_e( 'Last 30 Days', 'gamplify-gld' ); ?></option>
					<option value="90days"><?php esc_html_e( 'Last 90 Days', 'gamplify-gld' ); ?></option>
					<option value="custom"><?php esc_html_e( 'Custom Range', 'gamplify-gld' ); ?></option>
				</select>
			</div>
		</div>

		<div class="gld-action-row">
			<button class="button button-primary button-large gld-generate-btn">
				<?php esc_html_e( 'Generate Report Shortcode', 'gamplify-gld' ); ?>
			</button>
		</div>
	</div>
</div>
