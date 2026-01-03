<?php
/**
 * Assessments Tab Content
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
		<span class="dashicons dashicons-clipboard gld-panel-icon"></span>
		<div class="gld-panel-title-wrapper">
			<h2 class="gld-panel-title"><?php esc_html_e( 'Assessment Analytics', 'gamplify-gld' ); ?></h2>
			<p class="gld-panel-description"><?php esc_html_e( 'Quiz results, test scores, and assessment performance tracking', 'gamplify-gld' ); ?></p>
		</div>
	</div>

	<div class="gld-section">
		<h3 class="gld-section-title"><?php esc_html_e( 'Assessment Metrics', 'gamplify-gld' ); ?></h3>
		<p class="gld-section-description"><?php esc_html_e( 'Generate shortcodes for assessment and quiz analytics', 'gamplify-gld' ); ?></p>

		<div class="gld-form-grid">
			<div class="gld-form-group">
				<label for="assessment-metric"><?php esc_html_e( 'Metric Type', 'gamplify-gld' ); ?> *</label>
				<select id="assessment-metric" class="gld-select">
					<option value=""><?php esc_html_e( 'Select metric', 'gamplify-gld' ); ?></option>
					<option value="average_score"><?php esc_html_e( 'Average Score', 'gamplify-gld' ); ?></option>
					<option value="pass_rate"><?php esc_html_e( 'Pass Rate', 'gamplify-gld' ); ?></option>
					<option value="attempts"><?php esc_html_e( 'Attempts Count', 'gamplify-gld' ); ?></option>
					<option value="completion_time"><?php esc_html_e( 'Completion Time', 'gamplify-gld' ); ?></option>
				</select>
			</div>
		</div>

		<div class="gld-action-row">
			<button class="button button-primary button-large gld-generate-btn">
				<?php esc_html_e( 'Generate Assessment Shortcode', 'gamplify-gld' ); ?>
			</button>
		</div>
	</div>
</div>
