<?php
/**
 * Corporate Tab Content
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
		<span class="dashicons dashicons-building gld-panel-icon"></span>
		<div class="gld-panel-title-wrapper">
			<h2 class="gld-panel-title"><?php esc_html_e( 'Corporate Analytics', 'gamplify-gld' ); ?></h2>
			<p class="gld-panel-description"><?php esc_html_e( 'Enterprise-level reporting and team performance metrics', 'gamplify-gld' ); ?></p>
		</div>
	</div>

	<div class="gld-section">
		<h3 class="gld-section-title"><?php esc_html_e( 'Corporate Metrics', 'gamplify-gld' ); ?></h3>
		<p class="gld-section-description"><?php esc_html_e( 'Generate shortcodes for corporate and team analytics', 'gamplify-gld' ); ?></p>

		<div class="gld-form-grid">
			<div class="gld-form-group">
				<label for="corporate-metric"><?php esc_html_e( 'Metric Type', 'gamplify-gld' ); ?> *</label>
				<select id="corporate-metric" class="gld-select">
					<option value=""><?php esc_html_e( 'Select metric', 'gamplify-gld' ); ?></option>
					<option value="team_performance"><?php esc_html_e( 'Team Performance', 'gamplify-gld' ); ?></option>
					<option value="department_stats"><?php esc_html_e( 'Department Statistics', 'gamplify-gld' ); ?></option>
					<option value="roi_metrics"><?php esc_html_e( 'ROI Metrics', 'gamplify-gld' ); ?></option>
					<option value="compliance"><?php esc_html_e( 'Compliance Tracking', 'gamplify-gld' ); ?></option>
				</select>
			</div>
		</div>

		<div class="gld-action-row">
			<button class="button button-primary button-large gld-generate-btn">
				<?php esc_html_e( 'Generate Corporate Shortcode', 'gamplify-gld' ); ?>
			</button>
		</div>
	</div>
</div>
