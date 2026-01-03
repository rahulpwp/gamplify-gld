<?php
/**
 * User Dashboard View
 *
 * @package    Gamplify_GLD
 * @subpackage Gamplify_GLD/public/views
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

$user_id = get_current_user_id();
$stats = GLD_Frontend_Reports::get_user_stats( $user_id );
?>

<div class="gld-user-dashboard">
	<h2><?php esc_html_e( 'Your Analytics', 'gamplify-gld' ); ?></h2>
	
	<div class="gld-user-stats">
		<div class="gld-user-stat">
			<h3><?php esc_html_e( 'Total Activity', 'gamplify-gld' ); ?></h3>
			<div class="gld-stat-value"><?php echo esc_html( gld_format_number( $stats['total_events'] ) ); ?></div>
		</div>
		
		<div class="gld-user-stat">
			<h3><?php esc_html_e( 'Page Views', 'gamplify-gld' ); ?></h3>
			<div class="gld-stat-value"><?php echo esc_html( gld_format_number( $stats['page_views'] ) ); ?></div>
		</div>
	</div>
</div>
