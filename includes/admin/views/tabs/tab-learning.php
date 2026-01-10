<?php
/**
 * Learning Tab Content
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
		<span class="dashicons dashicons-welcome-learn-more gld-panel-icon"></span>
		<div class="gld-panel-title-wrapper">
			<h2 class="gld-panel-title"><?php esc_html_e( 'Learning KPI Metrics', 'gamplify-gld' ); ?></h2>
			<p class="gld-panel-description"><?php esc_html_e( 'Generate shortcodes for course progress, assessments, and student performance', 'gamplify-gld' ); ?></p>
		</div>
	</div>

	<!-- Sub-tabs -->
	<div class="gld-sub-tabs">
		<button class="gld-sub-tab active" data-subtab="learning-kpis">
			<span class="dashicons dashicons-book-alt"></span>
			<?php esc_html_e( 'Learning KPIs', 'gamplify-gld' ); ?>
		</button>
		<button class="gld-sub-tab" data-subtab="learning-data-tables">
			<span class="dashicons dashicons-editor-table"></span>
			<?php esc_html_e( 'Data Tables', 'gamplify-gld' ); ?>
		</button>
	</div>

	<!-- Learning KPIs Section -->
	<div class="gld-sub-content" id="subtab-learning-kpis">
		<div class="gld-section">
			<h3 class="gld-section-title"><?php esc_html_e( 'Learning KPI Metrics', 'gamplify-gld' ); ?></h3>
			<p class="gld-section-description"><?php esc_html_e( 'Generate shortcodes for displaying course completion, progress, and quiz metrics', 'gamplify-gld' ); ?></p>

			<div class="gld-form-grid">
				<input type="hidden" id="learning-kpi-id" value="">
				<div class="gld-form-group">
					<label for="learning-kpi-metric-type"><?php esc_html_e( 'Metric Type', 'gamplify-gld' ); ?> *</label>
					<select id="learning-kpi-metric-type" class="gld-select">
						<option value=""><?php esc_html_e( 'Select metric type', 'gamplify-gld' ); ?></option>
                        <option value="course_completed"><?php esc_html_e( 'Course Completed', 'gamplify-gld' ); ?></option>
                        <option value="course_in_progress"><?php esc_html_e( 'Course In Progress', 'gamplify-gld' ); ?></option>
                        <option value="quiz_completed"><?php esc_html_e( 'Quiz Completed', 'gamplify-gld' ); ?></option>
					</select>
				</div>

				<div class="gld-form-group">
					<label for="learning-kpi-filter-course"><?php esc_html_e( 'Filter by Course', 'gamplify-gld' ); ?></label>
					<select id="learning-kpi-filter-course" class="gld-select select-2" multiple>
						<option value="all"><?php esc_html_e( 'All Courses', 'gamplify-gld' ); ?></option>
						<?php
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
					<label for="learning-kpi-include-chart"><?php esc_html_e( 'Include Chart Version', 'gamplify-gld' ); ?></label>
					<select id="learning-kpi-include-chart" class="gld-select">
						<option value="no"><?php esc_html_e( 'No', 'gamplify-gld' ); ?></option>
						<option value="yes"><?php esc_html_e( 'Yes', 'gamplify-gld' ); ?></option>
					</select>
				</div>
			</div>

			<div class="gld-action-row">
				<button class="button button-primary button-large gld-generate-btn" id="generate-learning-kpi-shortcode">
					<?php esc_html_e( 'Generate Shortcode', 'gamplify-gld' ); ?>
				</button>
			</div>
		</div>

		<!-- Generated Learning KPIs Table -->
		<div class="gld-section">
			<h3 class="gld-section-title"><?php esc_html_e( 'Generated Learning KPI Shortcodes', 'gamplify-gld' ); ?></h3>
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
				<tbody id="learning-kpi-shortcodes-list">
					<tr class="no-items">
						<td colspan="6" class="gld-no-items">
							<?php esc_html_e( 'No shortcodes generated yet.', 'gamplify-gld' ); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<div id="learning-kpi-pagination" class="gld-pagination"></div>
		</div>
	</div>

	<!-- Learning Data Tables Section -->
	<div class="gld-sub-content" id="subtab-learning-data-tables" style="display: none;">
		<div class="gld-section">
			<h3 class="gld-section-title"><?php esc_html_e( 'Learning Data Tables', 'gamplify-gld' ); ?></h3>
			<p class="gld-section-description"><?php esc_html_e( 'Generate shortcodes for detailed learning data tables', 'gamplify-gld' ); ?></p>

			<div class="gld-form-grid">
				<input type="hidden" id="learning-table-id" value="">
				<div class="gld-form-group">
					<label for="learning-table-type"><?php esc_html_e( 'Table Type', 'gamplify-gld' ); ?> *</label>
					<select id="learning-table-type" class="gld-select">
						<option value=""><?php esc_html_e( 'Select table type', 'gamplify-gld' ); ?></option>
						<option value="course_completion_summary"><?php esc_html_e( 'Course Completion Summary', 'gamplify-gld' ); ?></option>
					</select>
				</div>

				<div class="gld-form-group">
					<label for="learning-table-filter-course"><?php esc_html_e( 'Filter by Course', 'gamplify-gld' ); ?></label>
					<select id="learning-table-filter-course" class="gld-select select-2" multiple>
						<option value="all"><?php esc_html_e( 'All Courses', 'gamplify-gld' ); ?></option>
						<?php
						if ( $found_post_type ) {
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
					<label for="learning-table-rows"><?php esc_html_e( 'Rows to Display', 'gamplify-gld' ); ?></label>
					<input type="number" id="learning-table-rows" class="gld-input" min="1" max="100" value="10">
				</div>

				<div class="gld-form-group">
					<label for="learning-table-sort"><?php esc_html_e( 'Sort By', 'gamplify-gld' ); ?></label>
					<select id="learning-table-sort" class="gld-select">
						<option value="date"><?php esc_html_e( 'Date', 'gamplify-gld' ); ?></option>
						<option value="course_name"><?php esc_html_e( 'Course Name', 'gamplify-gld' ); ?></option>
						<option value="status"><?php esc_html_e( 'Status', 'gamplify-gld' ); ?></option>
					</select>
				</div>
			</div>

			<div class="gld-action-row">
				<button class="button button-primary button-large gld-generate-btn" id="generate-learning-table-shortcode">
					<?php esc_html_e( 'Generate Shortcode', 'gamplify-gld' ); ?>
				</button>
			</div>
		</div>

		<!-- Generated Learning Data Tables -->
		<div class="gld-section">
			<h3 class="gld-section-title"><?php esc_html_e( 'Generated Learning Data Table Shortcodes', 'gamplify-gld' ); ?></h3>
			<table class="wp-list-table widefat fixed striped gld-shortcodes-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Type', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Title', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Course', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Rows', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Shortcode', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Created', 'gamplify-gld' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'gamplify-gld' ); ?></th>
					</tr>
				</thead>
				<tbody id="learning-table-shortcodes-list">
					<tr class="no-items">
						<td colspan="7" class="gld-no-items">
							<?php esc_html_e( 'No shortcodes generated yet.', 'gamplify-gld' ); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<div id="learning-table-pagination" class="gld-pagination"></div>
		</div>
	</div>
</div>
