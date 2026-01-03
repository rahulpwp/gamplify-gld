<?php
/**
 * WooCommerce Tab Content
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
	<!-- Green Header with Icon -->
	<div class="gld-panel-header">
		<!-- Icon wrapper with light green background -->
		<div class="gld-panel-icon-wrapper">
			<span class="dashicons dashicons-cart gld-panel-icon"></span>
		</div>
		<div class="gld-panel-title-wrapper">
			<h2 class="gld-panel-title"><?php esc_html_e( 'WooCommerce Analytics', 'gamplify-gld' ); ?></h2>
			<p class="gld-panel-description"><?php esc_html_e( 'Advanced WooCommerce reporting with orders, subscriptions, and customer analytics', 'gamplify-gld' ); ?></p>
		</div>
	</div>

	<!-- Sub Tabs Pills -->
	<div class="gld-sub-tabs">
		<button class="gld-sub-tab active" data-subtab="analytics">
			<span class="dashicons dashicons-chart-line"></span>
			<?php esc_html_e( 'Analytics', 'gamplify-gld' ); ?>
		</button>
		<button class="gld-sub-tab" data-subtab="products">
			<span class="dashicons dashicons-products"></span>
			<?php esc_html_e( 'Products', 'gamplify-gld' ); ?>
		</button>
	</div>

	<!-- Analytics Sub-tab Content -->
	<div id="subtab-analytics" class="gld-sub-content">
		
		<!-- Main Card -->
		<div class="gld-card">
			<div class="gld-card-header">
				<h3 class="gld-card-title"><?php esc_html_e( 'WooCommerce Dashboards', 'gamplify-gld' ); ?></h3>
				<p class="gld-card-description"><?php esc_html_e( 'Comprehensive analytics for orders, revenue, and subscriptions', 'gamplify-gld' ); ?></p>
			</div>
			
			<div class="gld-card-body">
				<div class="gld-form-group full-width">
					<label for="woo-metric-type"><?php esc_html_e( 'Metric Type', 'gamplify-gld' ); ?> <span class="required">*</span></label>
					<select id="woo-metric-type" class="gld-select">
						<option value=""><?php esc_html_e( 'Select metric type', 'gamplify-gld' ); ?></option>
						<option value="orders"><?php esc_html_e( 'Orders Overview', 'gamplify-gld' ); ?></option>
						<option value="revenue"><?php esc_html_e( 'Revenue Summary', 'gamplify-gld' ); ?></option>
						<option value="subscriptions"><?php esc_html_e( 'Subscriptions Status', 'gamplify-gld' ); ?></option>
						<option value="customers"><?php esc_html_e( 'Customer Growth', 'gamplify-gld' ); ?></option>
					</select>
				</div>
				
				<button class="button button-primary gld-btn-full gld-generate-btn" id="generate-woo-shortcode">
					<?php esc_html_e( 'Generate Shortcode', 'gamplify-gld' ); ?>
				</button>
			</div>
		</div>

		<!-- Results Area -->
		<div class="gld-results-area">
			<div class="gld-empty-state">
				<p><?php esc_html_e( 'No shortcodes generated yet. Create your first shortcode above!', 'gamplify-gld' ); ?></p>
			</div>
			<div id="woo-shortcode-result" style="display:none;">
				<!-- Dynamic content -->
			</div>
		</div>
	</div>

	<!-- Products Sub-tab Content -->
	<div id="subtab-products" class="gld-sub-content" style="display:none;">
		
		<!-- Main Card -->
		<div class="gld-card">
			<div class="gld-card-header">
				<h3 class="gld-card-title"><?php esc_html_e( 'Product Analytics', 'gamplify-gld' ); ?></h3>
				<p class="gld-card-description"><?php esc_html_e( 'Track product performance and customer behavior', 'gamplify-gld' ); ?></p>
			</div>
			
			<div class="gld-card-body">
				<div class="gld-form-grid">
					<div class="gld-form-group">
						<label for="woo-product-id"><?php esc_html_e( 'Product ID', 'gamplify-gld' ); ?></label>
						<input type="text" id="woo-product-id" class="gld-select" placeholder="<?php esc_attr_e( 'Leave empty for all products', 'gamplify-gld' ); ?>">
					</div>

					<div class="gld-form-group">
						<label for="woo-product-metric"><?php esc_html_e( 'Metric', 'gamplify-gld' ); ?></label>
						<select id="woo-product-metric" class="gld-select">
							<option value=""><?php esc_html_e( 'Select metric', 'gamplify-gld' ); ?></option>
							<option value="views"><?php esc_html_e( 'Product Views', 'gamplify-gld' ); ?></option>
							<option value="purchases"><?php esc_html_e( 'Purchases', 'gamplify-gld' ); ?></option>
							<option value="add_to_cart"><?php esc_html_e( 'Add to Cart Rate', 'gamplify-gld' ); ?></option>
						</select>
					</div>
				</div>
				
				<button class="button button-primary gld-btn-full gld-generate-btn" id="generate-woo-product-shortcode">
					<?php esc_html_e( 'Generate Shortcode', 'gamplify-gld' ); ?>
				</button>
			</div>
		</div>

		<!-- Results Area -->
		<div class="gld-results-area">
			<div class="gld-empty-state">
				<p><?php esc_html_e( 'No shortcodes generated yet. Create your first shortcode above!', 'gamplify-gld' ); ?></p>
			</div>
			<div id="woo-product-shortcode-result" style="display:none;">
				<!-- Dynamic content -->
			</div>
		</div>
	</div>
</div>
