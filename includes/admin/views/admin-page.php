<?php
/**
 * Main Admin Page with Tabs
 *
 * @package    Gamplify_GLD
 * @subpackage Gamplify_GLD/admin/views
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Get current tab
$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'membership';
?>

<div class="wrap gld-admin-page">
	<!-- Header -->
	<div class="gld-header">
		<div class="gld-header-content">
			<div class="gld-back-link">
				<a href="<?php echo admin_url( 'admin.php?page=gld-analytics' ); ?>">
					← <?php esc_html_e( 'Back to Dashboard', 'gamplify-gld' ); ?>
				</a>
			</div>
			<h1 class="gld-page-title">
				<span class="gld-logo">GAMPLIFY</span>
				<?php esc_html_e( 'GLD Analytics - Admin Panel', 'gamplify-gld' ); ?>
			</h1>
			<p class="gld-subtitle"><?php esc_html_e( 'Configure dashboard sections and generate shortcodes', 'gamplify-gld' ); ?></p>
		</div>
	</div>

	<!-- Tab Navigation -->
	<div class="gld-tab-navigation">
		<div class="gld-tabs-row gld-tabs-row-1">
			<div class="gld-tab active" data-tab="membership">
				<span class="dashicons dashicons-heart"></span>
				<span class="gld-tab-label"><?php esc_html_e( 'Membership', 'gamplify-gld' ); ?></span>
			</div>
			
			<div class="gld-tab" data-tab="revenue">
				<span class="dashicons dashicons-money-alt"></span>
				<span class="gld-tab-label"><?php esc_html_e( 'Revenue', 'gamplify-gld' ); ?></span>
			</div>
			
			<div class="gld-tab" data-tab="learning">
				<span class="dashicons dashicons-welcome-learn-more"></span>
				<span class="gld-tab-label"><?php esc_html_e( 'Learning', 'gamplify-gld' ); ?></span>
			</div>
			
			<div class="gld-tab" data-tab="engagement">
				<span class="dashicons dashicons-star-empty"></span>
				<span class="gld-tab-label"><?php esc_html_e( 'Engagement', 'gamplify-gld' ); ?></span>
			</div>
			
			<div class="gld-tab" data-tab="reports">
				<span class="dashicons dashicons-media-text"></span>
				<span class="gld-tab-label"><?php esc_html_e( 'Reports', 'gamplify-gld' ); ?></span>
			</div>
		</div>
		
		<div class="gld-tabs-row gld-tabs-row-2">
			<div class="gld-tab" data-tab="corporate">
				<span class="dashicons dashicons-building"></span>
				<span class="gld-tab-label"><?php esc_html_e( 'Corporate', 'gamplify-gld' ); ?></span>
			</div>
			
			<div class="gld-tab" data-tab="woocommerce">
				<span class="dashicons dashicons-cart"></span>
				<span class="gld-tab-label"><?php esc_html_e( 'WooCommerce', 'gamplify-gld' ); ?></span>
			</div>
			
			<div class="gld-tab" data-tab="assessments">
				<span class="dashicons dashicons-clipboard"></span>
				<span class="gld-tab-label"><?php esc_html_e( 'Assessments', 'gamplify-gld' ); ?></span>
			</div>
			
			<div class="gld-tab" data-tab="gamification">
				<span class="dashicons dashicons-awards"></span>
				<span class="gld-tab-label"><?php esc_html_e( 'Gamification', 'gamplify-gld' ); ?></span>
			</div>
			
			<div class="gld-tab" data-tab="content">
				<span class="dashicons dashicons-analytics"></span>
				<span class="gld-tab-label"><?php esc_html_e( 'Content', 'gamplify-gld' ); ?></span>
			</div>
		</div>
	</div>

	<!-- Tab Content -->
	<div class="gld-content-wrapper">
		<div id="tab-membership" class="gld-main-tab-content active">
			<?php include GLD_DIR . '/includes/admin/views/tabs/tab-membership.php'; ?>
		</div>
		
		<div id="tab-revenue" class="gld-main-tab-content" style="display:none;">
			<?php 
			if ( file_exists( GLD_DIR . '/includes/admin/views/tabs/tab-revenue.php' ) ) {
				include GLD_DIR . '/includes/admin/views/tabs/tab-revenue.php';
			} else {
				echo '<p>' . esc_html__( 'Revenue content coming soon.', 'gamplify-gld' ) . '</p>';
			}
			?>
		</div>
		
		<div id="tab-learning" class="gld-main-tab-content" style="display:none;">
			<?php include GLD_DIR . '/includes/admin/views/tabs/tab-learning.php'; ?>
		</div>
		
		<div id="tab-engagement" class="gld-main-tab-content" style="display:none;">
			<?php include GLD_DIR . '/includes/admin/views/tabs/tab-engagement.php'; ?>
		</div>
		
		<div id="tab-reports" class="gld-main-tab-content" style="display:none;">
			<?php include GLD_DIR . '/includes/admin/views/tabs/tab-reports.php'; ?>
		</div>
		
		<div id="tab-corporate" class="gld-main-tab-content" style="display:none;">
			<?php include GLD_DIR . '/includes/admin/views/tabs/tab-corporate.php'; ?>
		</div>
		
		<div id="tab-woocommerce" class="gld-main-tab-content" style="display:none;">
			<?php include GLD_DIR . '/includes/admin/views/tabs/tab-woocommerce.php'; ?>
		</div>
		
		<div id="tab-assessments" class="gld-main-tab-content" style="display:none;">
			<?php include GLD_DIR . '/includes/admin/views/tabs/tab-assessments.php'; ?>
		</div>
		
		<div id="tab-gamification" class="gld-main-tab-content" style="display:none;">
			<?php include GLD_DIR . '/includes/admin/views/tabs/tab-gamification.php'; ?>
		</div>
		
		<div id="tab-content" class="gld-main-tab-content" style="display:none;">
			<?php include GLD_DIR . '/includes/admin/views/tabs/tab-content.php'; ?>
		</div>
	</div>
</div>
