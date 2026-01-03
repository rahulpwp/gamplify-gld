<?php
/**
 * Content Tab Content
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
		<span class="dashicons dashicons-admin-page gld-panel-icon"></span>
		<div class="gld-panel-title-wrapper">
			<h2 class="gld-panel-title"><?php esc_html_e( 'Content Analytics', 'gamplify-gld' ); ?></h2>
			<p class="gld-panel-description"><?php esc_html_e( 'Content performance, views, and engagement metrics', 'gamplify-gld' ); ?></p>
		</div>
	</div>

	<div class="gld-section">
		<h3 class="gld-section-title"><?php esc_html_e( 'Content Metrics', 'gamplify-gld' ); ?></h3>
		<p class="gld-section-description"><?php esc_html_e( 'Generate shortcodes for content analytics', 'gamplify-gld' ); ?></p>

		<div class="gld-form-grid">
			<div class="gld-form-group">
				<label for="content-metric"><?php esc_html_e( 'Metric Type', 'gamplify-gld' ); ?> *</label>
				<select id="content-metric" class="gld-select">
					<option value=""><?php esc_html_e( 'Select metric', 'gamplify-gld' ); ?></option>
					<option value="page_views"><?php esc_html_e( 'Page Views', 'gamplify-gld' ); ?></option>
					<option value="popular_content"><?php esc_html_e( 'Popular Content', 'gamplify-gld' ); ?></option>
					<option value="time_on_page"><?php esc_html_e( 'Time on Page', 'gamplify-gld' ); ?></option>
					<option value="bounce_rate"><?php esc_html_e( 'Bounce Rate', 'gamplify-gld' ); ?></option>
				</select>
			</div>

			<div class="gld-form-group">
				<label for="content-type"><?php esc_html_e( 'Content Type', 'gamplify-gld' ); ?></label>
				<select id="content-type" class="gld-select">
					<option value="all"><?php esc_html_e( 'All Content', 'gamplify-gld' ); ?></option>
					<option value="posts"><?php esc_html_e( 'Posts', 'gamplify-gld' ); ?></option>
					<option value="pages"><?php esc_html_e( 'Pages', 'gamplify-gld' ); ?></option>
				</select>
			</div>
		</div>

		<div class="gld-action-row">
			<button class="button button-primary button-large gld-generate-btn">
				<?php esc_html_e( 'Generate Content Shortcode', 'gamplify-gld' ); ?>
			</button>
		</div>
	</div>
</div>
