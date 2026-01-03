<?php
/**
 * Admin Settings View
 *
 * @package    Gamplify_GLD
 * @subpackage Gamplify_GLD/admin/views
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

$settings = gld_get_setting();
?>

<div class="wrap gld-settings">
	<h1><?php esc_html_e( 'Gamplify GLD Settings', 'gamplify-gld' ); ?></h1>
	
	<form method="post" action="options.php">
		<?php settings_fields( 'gld_settings_group' ); ?>
		
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Enable Tracking', 'gamplify-gld' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="gld_settings[enable_tracking]" value="1" <?php checked( gld_get_setting( 'enable_tracking', true ), true ); ?> />
						<?php esc_html_e( 'Enable analytics tracking', 'gamplify-gld' ); ?>
					</label>
				</td>
			</tr>
			
			<tr>
				<th scope="row"><?php esc_html_e( 'Track Logged-in Users', 'gamplify-gld' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="gld_settings[track_logged_in_users]" value="1" <?php checked( gld_get_setting( 'track_logged_in_users', true ), true ); ?> />
						<?php esc_html_e( 'Track logged-in users', 'gamplify-gld' ); ?>
					</label>
				</td>
			</tr>
			
			<tr>
				<th scope="row"><?php esc_html_e( 'Track Administrators', 'gamplify-gld' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="gld_settings[track_admins]" value="1" <?php checked( gld_get_setting( 'track_admins', false ), true ); ?> />
						<?php esc_html_e( 'Track administrator activity', 'gamplify-gld' ); ?>
					</label>
				</td>
			</tr>
			
			<tr>
				<th scope="row"><?php esc_html_e( 'Anonymize IP Addresses', 'gamplify-gld' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="gld_settings[anonymize_ip]" value="1" <?php checked( gld_get_setting( 'anonymize_ip', true ), true ); ?> />
						<?php esc_html_e( 'Anonymize IP addresses for privacy', 'gamplify-gld' ); ?>
					</label>
				</td>
			</tr>
			
			<tr>
				<th scope="row"><?php esc_html_e( 'Data Retention', 'gamplify-gld' ); ?></th>
				<td>
					<input type="number" name="gld_settings[data_retention_days]" value="<?php echo esc_attr( gld_get_setting( 'data_retention_days', 90 ) ); ?>" min="1" max="365" />
					<?php esc_html_e( 'days', 'gamplify-gld' ); ?>
					<p class="description"><?php esc_html_e( 'Number of days to keep analytics data', 'gamplify-gld' ); ?></p>
				</td>
			</tr>
		</table>
		
		<?php submit_button(); ?>
	</form>
</div>
