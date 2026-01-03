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
			<h2 class="gld-panel-title"><?php esc_html_e( 'Learning Analytics', 'gamplify-gld' ); ?></h2>
			<p class="gld-panel-description"><?php esc_html_e( 'Track course progress, completion rates, and learning outcomes', 'gamplify-gld' ); ?></p>
		</div>
	</div>

	<div class="gld-section">
		<h3 class="gld-section-title"><?php esc_html_e( 'Course Analytics', 'gamplify-gld' ); ?></h3>
		<p class="gld-section-description"><?php esc_html_e( 'Generate shortcodes for course-related metrics', 'gamplify-gld' ); ?></p>

		<div class="gld-form-grid">
			<div class="gld-form-group">
				<label for="learning-metric"><?php esc_html_e( 'Learning Metric', 'gamplify-gld' ); ?> *</label>
				<select id="learning-metric" class="gld-select">
					<option value=""><?php esc_html_e( 'Select metric', 'gamplify-gld' ); ?></option>
					<option value="completion_rate"><?php esc_html_e( 'Completion Rate', 'gamplify-gld' ); ?></option>
					<option value="progress_tracking"><?php esc_html_e( 'Progress Tracking', 'gamplify-gld' ); ?></option>
					<option value="quiz_scores"><?php esc_html_e( 'Quiz Scores', 'gamplify-gld' ); ?></option>
					<option value="time_spent"><?php esc_html_e( 'Time Spent Learning', 'gamplify-gld' ); ?></option>
				</select>
			</div>

			<div class="gld-form-group">
				<label for="course-filter"><?php esc_html_e( 'Course', 'gamplify-gld' ); ?></label>
				<select id="course-filter" class="gld-select">
					<option value="all"><?php esc_html_e( 'All Courses', 'gamplify-gld' ); ?></option>
					<!-- Dynamic courses -->
				</select>
			</div>
		</div>

		<div class="gld-action-row">
			<button class="button button-primary button-large gld-generate-btn">
				<?php esc_html_e( 'Generate Learning Shortcode', 'gamplify-gld' ); ?>
			</button>
		</div>
	</div>
</div>
