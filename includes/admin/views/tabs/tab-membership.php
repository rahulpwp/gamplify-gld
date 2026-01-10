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
					<label for="kpi-metric-type"><?php esc_html_e( 'Metric Type', 'gamplify-gld' ); ?> *</label>
					<select id="kpi-metric-type" class="gld-select">
						<option value=""><?php esc_html_e( 'Select metric type', 'gamplify-gld' ); ?></option>						
						<?php
						if ( class_exists( 'WooCommerce' ) ) {
							$args = array(
								'type'   => array( 'subscription', 'variable-subscription' ),
								'limit'  => -1,
								'status' => 'publish',
							);
							
							// Check if wc_get_products exists, otherwise use WP_Query
							if ( function_exists( 'wc_get_products' ) ) {
								$products = wc_get_products( $args );
								if ( ! empty( $products ) ) {
									// echo '<optgroup label="' . esc_attr__( 'Subscription Products', 'gamplify-gld' ) . '">';
									foreach ( $products as $product ) {
										echo '<option value="' . esc_attr( $product->get_id() ) . '">' . esc_html( $product->get_name() ) . '</option>';
									}
									// echo '</optgroup>';
								}
							}
						}
						?>
					</select>
				</div>

				<div class="gld-form-group">
					<label for="kpi-filter-course"><?php esc_html_e( 'Filter by Course', 'gamplify-gld' ); ?></label>
					<select id="kpi-filter-course" class="gld-select select-2" multiple>
						<option value=""><?php esc_html_e( 'Select filter by course', 'gamplify-gld' ); ?></option>
						<option value=""><?php esc_html_e( 'No Courses', 'gamplify-gld' ); ?></option>
						<option value="all"><?php esc_html_e( 'All Courses', 'gamplify-gld' ); ?></option>
						<?php
						// Try to detect common LMS course post types
						$lms_post_types = array( 'sfwd-courses' );
						$found_post_type = false;
						
						foreach ( $lms_post_types as $pt ) {
							if ( post_type_exists( $pt ) ) {
								$found_post_type = $pt;
								break;
							}
						}
						
						if ( $found_post_type ) {
							$courses = get_posts( array(
								'post_type'      => $found_post_type,
								'posts_per_page' => -1,
								'post_status'    => 'publish',
								'orderby'        => 'title',
								'order'          => 'ASC',
							) );
							
							if ( ! empty( $courses ) ) {
								foreach ( $courses as $course ) {
									echo '<option value="' . esc_attr( $course->ID ) . '">' . esc_html( $course->post_title ) . '</option>';
								}
							}
						}
						?>
					</select>
				</div>

				<div class="gld-form-group">
					<label for="kpi-chart-version"><?php esc_html_e( 'Include Chart Version', 'gamplify-gld' ); ?></label>
					<select id="kpi-chart-version" class="gld-select">
						<option value=""><?php esc_html_e( 'Select include chart version', 'gamplify-gld' ); ?></option>
						<option value="no"><?php esc_html_e( 'No', 'gamplify-gld' ); ?></option>
						<option value="yes"><?php esc_html_e( 'Yes', 'gamplify-gld' ); ?></option>
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
			<h3 class="gld-section-title"><?php esc_html_e( 'Generated Metric Shortcodes', 'gamplify-gld' ); ?></h3>
			
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
			<div id="gld-kpi-pagination" class="gld-pagination" style="margin-top: 15px; text-align: right;"></div>
		</div>
	</div>

	<!-- Charts & Visuals Section -->
	<div class="gld-sub-content" id="subtab-charts" style="display: none;">
		<div class="gld-section">
			<h3 class="gld-section-title"><?php esc_html_e( 'Membership Charts & Visualizations', 'gamplify-gld' ); ?></h3>
			<p class="gld-section-description"><?php esc_html_e( 'Create visual representations of membership data', 'gamplify-gld' ); ?></p>

			<div class="gld-form-grid">
				<div class="gld-form-group">
					<label for="chart-config-type"><?php esc_html_e( 'Chart Type', 'gamplify-gld' ); ?> *</label>
					<select id="chart-config-type" class="gld-select">
						<option value=""><?php esc_html_e( 'Select chart type', 'gamplify-gld' ); ?></option>
						<option value="subscription_distribution_pie_chart"><?php esc_html_e( 'Subscription Distribution (Pie Chart)', 'gamplify-gld' ); ?></option>
						<option value="retention_analysis_pie_chart"><?php esc_html_e( 'Retention Analysis (Pie Chart)', 'gamplify-gld' ); ?></option>
						<option value="user_growth_over_time_line_chart"><?php esc_html_e( 'User Growth Over Time (Line Chart)', 'gamplify-gld' ); ?></option>
						<option value="churn_rate_over_time_line_chart"><?php esc_html_e( 'Churn Rate Over Time (Line Chart)', 'gamplify-gld' ); ?></option>
						<option value="dau_wau_mau_trends_area_chart"><?php esc_html_e( 'DAU/WAU/MAU Trends (Area Chart)', 'gamplify-gld' ); ?></option>
					</select>
				</div>

				<div class="gld-form-group">
					<label for="chart-config-product"><?php esc_html_e( 'Filter by Product', 'gamplify-gld' ); ?></label>
					<select id="chart-config-product" class="gld-select" multiple>
						<option value=""><?php esc_html_e( 'Select filter by product', 'gamplify-gld' ); ?></option>
						<option value="all"><?php esc_html_e( 'All Products', 'gamplify-gld' ); ?></option>
						<?php
						if ( class_exists( 'WooCommerce' ) ) {
							$args = array(
								'type'   => array( 'subscription', 'variable-subscription' ),
								'limit'  => -1,
								'status' => 'publish',
							);
							
							if ( function_exists( 'wc_get_products' ) ) {
								$products = wc_get_products( $args );
								if ( ! empty( $products ) ) {
									foreach ( $products as $product ) {
										echo '<option value="' . esc_attr( $product->get_id() ) . '">' . esc_html( $product->get_name() ) . '</option>';
									}
								}
							}
						}
						?>
					</select>
				</div>

				<div class="gld-form-group">
					<label for="chart-config-height"><?php esc_html_e( 'Chart Height (px)', 'gamplify-gld' ); ?></label>
					<input type="number" id="chart-config-height" class="chart-height gld-input" min="1" max="500" value="300">
				</div>

			</div>

			<div class="gld-action-row">
				<button class="button button-primary button-large gld-generate-btn" id="generate-chart-shortcode">
					<?php esc_html_e( 'Generate Chart Shortcode', 'gamplify-gld' ); ?>
				</button>
			</div>
		</div>

		<!-- Generated Chart Shortcodes Table -->
		<div class="gld-section">
			<h3 class="gld-section-title"><?php esc_html_e( 'Generated Chart Shortcodes', 'gamplify-gld' ); ?></h3>
			
			<table class="wp-list-table widefat fixed striped gld-shortcodes-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Type', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Title', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Product', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Chart Height', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Shortcode', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Created', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'gamplify-gld' ); ?></th>
					</tr>
				</thead>
				<tbody id="chart-shortcodes-list">
					<tr class="no-items">
						<td colspan="7" class="gld-no-items">
							<?php esc_html_e( 'No chart shortcodes generated yet.', 'gamplify-gld' ); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<div id="gld-chart-pagination" class="gld-pagination" style="margin-top: 15px; text-align: right;"></div>
		</div>
	</div>
</div>
