<?php
/**
 * Admin Reports View
 *
 * @package    Gamplify_GLD
 * @subpackage Gamplify_GLD/admin/views
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<div class="wrap gld-reports">
	<h1><?php esc_html_e( 'Reports', 'gamplify-gld' ); ?></h1>
	
	<div class="gld-reports-actions">
		<button class="button button-primary" id="create-report"><?php esc_html_e( 'Create New Report', 'gamplify-gld' ); ?></button>
	</div>
	
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Report Name', 'gamplify-gld' ); ?></th>
				<th><?php esc_html_e( 'Type', 'gamplify-gld' ); ?></th>
				<th><?php esc_html_e( 'Created', 'gamplify-gld' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'gamplify-gld' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="4"><?php esc_html_e( 'No reports found. Create your first report!', 'gamplify-gld' ); ?></td>
			</tr>
		</tbody>
	</table>
</div>
