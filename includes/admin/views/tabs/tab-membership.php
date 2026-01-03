<?php
/**
 * Membership Tab Content
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
		<span class="dashicons dashicons-groups gld-panel-icon"></span>
		<div class="gld-panel-title-wrapper">
			<h2 class="gld-panel-title"><?php esc_html_e( 'Membership Metrics', 'gamplify-gld' ); ?></h2>
			<p class="gld-panel-description"><?php esc_html_e( 'Generate shortcodes for membership statistics and health indicators', 'gamplify-gld' ); ?></p>
		</div>
	</div>

	<!-- Sub-tabs -->
	<div class="gld-sub-tabs">
		<button class="gld-sub-tab active" data-subtab="kpis">
			<span class="dashicons dashicons-chart-line"></span>
			<?php esc_html_e( 'Member KPIs', 'gamplify-gld' ); ?>
		</button>
		<button class="gld-sub-tab" data-subtab="charts">
			<span class="dashicons dashicons-chart-area"></span>
			<?php esc_html_e( 'Charts & Visuals', 'gamplify-gld' ); ?>
		</button>
	</div>

	<!-- Member KPIs Section -->
	<div class="gld-sub-content" id="subtab-kpis">
		<div class="gld-section">
			<h3 class="gld-section-title"><?php esc_html_e( 'Member Count & KPI Metrics', 'gamplify-gld' ); ?></h3>
			<p class="gld-section-description"><?php esc_html_e( 'Generate shortcodes for displaying member counts, health scores, and key metrics', 'gamplify-gld' ); ?></p>

			<div class="gld-form-grid">
				<div class="gld-form-group">
					<label for="metric-type"><?php esc_html_e( 'Metric Type', 'gamplify-gld' ); ?> *</label>
					<select id="metric-type" class="gld-select">
						<option value=""><?php esc_html_e( 'Select metric type', 'gamplify-gld' ); ?></option>
						<option value="total_members"><?php esc_html_e( 'Total Members', 'gamplify-gld' ); ?></option>
						<option value="active_members"><?php esc_html_e( 'Active Members', 'gamplify-gld' ); ?></option>
						<option value="new_members"><?php esc_html_e( 'New Members (This Month)', 'gamplify-gld' ); ?></option>
						<option value="member_growth"><?php esc_html_e( 'Member Growth Rate', 'gamplify-gld' ); ?></option>
						<option value="engagement_score"><?php esc_html_e( 'Engagement Score', 'gamplify-gld' ); ?></option>
					</select>
				</div>

				<div class="gld-form-group">
					<label for="filter-course"><?php esc_html_e( 'Filter by Course', 'gamplify-gld' ); ?></label>
					<select id="filter-course" class="gld-select">
						<option value=""><?php esc_html_e( 'Select filter by course', 'gamplify-gld' ); ?></option>
						<option value="all"><?php esc_html_e( 'All Courses', 'gamplify-gld' ); ?></option>
						<!-- Dynamic courses will be loaded here -->
					</select>
				</div>

				<div class="gld-form-group">
					<label for="chart-version"><?php esc_html_e( 'Include Chart Version', 'gamplify-gld' ); ?></label>
					<select id="chart-version" class="gld-select">
						<option value=""><?php esc_html_e( 'Select include chart version', 'gamplify-gld' ); ?></option>
						<option value="no_chart"><?php esc_html_e( 'No Chart', 'gamplify-gld' ); ?></option>
						<option value="line_chart"><?php esc_html_e( 'Line Chart', 'gamplify-gld' ); ?></option>
						<option value="bar_chart"><?php esc_html_e( 'Bar Chart', 'gamplify-gld' ); ?></option>
						<option value="pie_chart"><?php esc_html_e( 'Pie Chart', 'gamplify-gld' ); ?></option>
					</select>
				</div>
			</div>

			<div class="gld-action-row">
				<button class="button button-primary button-large gld-generate-btn" id="generate-membership-shortcode">
					<?php esc_html_e( 'Generate Shortcode', 'gamplify-gld' ); ?>
				</button>
			</div>
		</div>

		<!-- Generated Shortcodes Table -->
		<div class="gld-section">
			<h3 class="gld-section-title"><?php esc_html_e( 'Generated Shortcodes', 'gamplify-gld' ); ?></h3>
			
			<table class="wp-list-table widefat fixed striped gld-shortcodes-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Type', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Title', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Course', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Shortcode', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Created', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'gamplify-gld' ); ?></th>
					</tr>
				</thead>
				<tbody id="membership-shortcodes-list">
					<tr class="no-items">
						<td colspan="6" class="gld-no-items">
							<?php esc_html_e( 'No shortcodes generated yet. Create your first shortcode above!', 'gamplify-gld' ); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<!-- Charts & Visuals Section -->
	<div class="gld-sub-content" id="subtab-charts" style="display: none;">
		<div class="gld-section">
			<h3 class="gld-section-title"><?php esc_html_e( 'Membership Charts & Visualizations', 'gamplify-gld' ); ?></h3>
			<p class="gld-section-description"><?php esc_html_e( 'Create visual representations of membership data', 'gamplify-gld' ); ?></p>

			<div class="gld-form-grid">
				<div class="gld-form-group">
					<label for="chart-type"><?php esc_html_e( 'Chart Type', 'gamplify-gld' ); ?> *</label>
					<select id="chart-type" class="gld-select">
						<option value=""><?php esc_html_e( 'Select chart type', 'gamplify-gld' ); ?></option>
						<option value="growth_trend"><?php esc_html_e( 'Growth Trend', 'gamplify-gld' ); ?></option>
						<option value="activity_heatmap"><?php esc_html_e( 'Activity Heatmap', 'gamplify-gld' ); ?></option>
						<option value="demographics"><?php esc_html_e( 'Demographics Breakdown', 'gamplify-gld' ); ?></option>
					</select>
				</div>

				<div class="gld-form-group">
					<label for="time-period"><?php esc_html_e( 'Time Period', 'gamplify-gld' ); ?></label>
					<select id="time-period" class="gld-select">
						<option value="7days"><?php esc_html_e( 'Last 7 Days', 'gamplify-gld' ); ?></option>
						<option value="30days"><?php esc_html_e( 'Last 30 Days', 'gamplify-gld' ); ?></option>
						<option value="90days"><?php esc_html_e( 'Last 90 Days', 'gamplify-gld' ); ?></option>
						<option value="1year"><?php esc_html_e( 'Last Year', 'gamplify-gld' ); ?></option>
					</select>
				</div>
			</div>

			<div class="gld-action-row">
				<button class="button button-primary button-large gld-generate-btn">
					<?php esc_html_e( 'Generate Chart Shortcode', 'gamplify-gld' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>
