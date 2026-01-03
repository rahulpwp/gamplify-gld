<?php
/**
 * Gamification Tab Content
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
		<span class="dashicons dashicons-awards gld-panel-icon"></span>
		<div class="gld-panel-title-wrapper">
			<h2 class="gld-panel-title"><?php esc_html_e( 'Gamification Metrics', 'gamplify-gld' ); ?></h2>
			<p class="gld-panel-description"><?php esc_html_e( 'Points, badges, leaderboards, and achievement tracking', 'gamplify-gld' ); ?></p>
		</div>
	</div>

	<div class="gld-section">
		<h3 class="gld-section-title"><?php esc_html_e( 'Gamification Analytics', 'gamplify-gld' ); ?></h3>
		<p class="gld-section-description"><?php esc_html_e( 'Generate shortcodes for gamification elements', 'gamplify-gld' ); ?></p>

		<div class="gld-form-grid">
			<div class="gld-form-group">
				<label for="gamification-type"><?php esc_html_e( 'Element Type', 'gamplify-gld' ); ?> *</label>
				<select id="gamification-type" class="gld-select">
					<option value=""><?php esc_html_e( 'Select type', 'gamplify-gld' ); ?></option>
					<option value="points"><?php esc_html_e( 'Points System', 'gamplify-gld' ); ?></option>
					<option value="badges"><?php esc_html_e( 'Badges Earned', 'gamplify-gld' ); ?></option>
					<option value="leaderboard"><?php esc_html_e( 'Leaderboard', 'gamplify-gld' ); ?></option>
					<option value="achievements"><?php esc_html_e( 'Achievements', 'gamplify-gld' ); ?></option>
					<option value="levels"><?php esc_html_e( 'User Levels', 'gamplify-gld' ); ?></option>
				</select>
			</div>
		</div>

		<div class="gld-action-row">
			<button class="button button-primary button-large gld-generate-btn">
				<?php esc_html_e( 'Generate Gamification Shortcode', 'gamplify-gld' ); ?>
			</button>
		</div>
	</div>
</div>
