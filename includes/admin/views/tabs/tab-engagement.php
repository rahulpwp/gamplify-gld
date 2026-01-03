<?php
/**
 * Engagement Tab Content
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
		<span class="dashicons dashicons-star-filled gld-panel-icon"></span>
		<div class="gld-panel-title-wrapper">
			<h2 class="gld-panel-title"><?php esc_html_e( 'Engagement Metrics', 'gamplify-gld' ); ?></h2>
			<p class="gld-panel-description"><?php esc_html_e( 'Monitor user engagement, interactions, and community activity', 'gamplify-gld' ); ?></p>
		</div>
	</div>

	<div class="gld-section">
		<h3 class="gld-section-title"><?php esc_html_e( 'Engagement Tracking', 'gamplify-gld' ); ?></h3>
		<p class="gld-section-description"><?php esc_html_e( 'Generate shortcodes for engagement metrics', 'gamplify-gld' ); ?></p>

		<div class="gld-form-grid">
			<div class="gld-form-group">
				<label for="engagement-type"><?php esc_html_e( 'Engagement Type', 'gamplify-gld' ); ?> *</label>
				<select id="engagement-type" class="gld-select">
					<option value=""><?php esc_html_e( 'Select type', 'gamplify-gld' ); ?></option>
					<option value="active_users"><?php esc_html_e( 'Active Users', 'gamplify-gld' ); ?></option>
					<option value="comments"><?php esc_html_e( 'Comments & Discussions', 'gamplify-gld' ); ?></option>
					<option value="likes_shares"><?php esc_html_e( 'Likes & Shares', 'gamplify-gld' ); ?></option>
					<option value="session_duration"><?php esc_html_e( 'Session Duration', 'gamplify-gld' ); ?></option>
				</select>
			</div>
		</div>

		<div class="gld-action-row">
			<button class="button button-primary button-large gld-generate-btn">
				<?php esc_html_e( 'Generate Engagement Shortcode', 'gamplify-gld' ); ?>
			</button>
		</div>
	</div>
</div>
