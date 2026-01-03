<?php
/**
 * Chart Shortcode
 *
 * @package    Gamplify_GLD
 * @subpackage Gamplify_GLD/public/shortcodes
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * GLD_Chart_Shortcode Class
 */
class GLD_Chart_Shortcode {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_shortcode( 'gld_chart', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Render the shortcode
	 *
	 * Usage: [gld_chart id="1"]
	 *
	 * @param array $atts Shortcode attributes
	 * @return string
	 */
	public function render_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'gld_chart'
		);
		
		if ( empty( $atts['id'] ) ) {
			return '';
		}
		
		global $wpdb;
		$table = GLD_MEMBER_CHARTS_TABLE;
		$chart = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $atts['id'] ) );
		
		if ( ! $chart ) {
			return '';
		}

		// Prepare Data
		$labels = array();
		$data_values = array();
		$bg_colors = array();
		
		$product_ids = array();
		if ( ! empty( $chart->filter_by_course ) ) { 
			$product_ids = explode( ',', $chart->filter_by_course );
		}
		
		// Logic branching based on chart type
		$stats_footer = '';
		$cutout = '0%'; // Default solid pie
		
		if ( $chart->metric_type === 'subscription_distribution_pie_chart' ) {
			// --- Subscription Distribution ---
			$cutout = '60%'; // Doughnut
			$bg_colors = array( '#3b82f6', '#10b981', '#f59e0b', '#6366f1', '#ec4899' ); // Blue, Green, Orange...
			
			foreach ( $product_ids as $p_id ) {
				$p_id = absint( $p_id );
				if ( $p_id ) {
					$product = wc_get_product( $p_id );
					if ( $product ) {
						$labels[] = $product->get_name();
						$data_values[] = gld_get_active_member_count( $p_id );
					}
				}
			}
			
			if ( empty( $labels ) ) {
				$labels = array( 'Annual', 'Monthly', 'Trial' );
				$data_values = array( 120, 240, 50 );
			}
			
		} elseif ( $chart->metric_type === 'retention_analysis_pie_chart' ) {
			// --- Retention Analysis ---
			$cutout = '60%'; // Doughnut
			$bg_colors = array( '#10b981', '#ef4444' ); // Green (Renewed), Red (Churned)
			$labels = array( 'Renewed', 'Churned' );
			
			// Calculate counts
			// "Renewed" roughly equals Active for this context, "Churned" is Cancelled/Expired
			$renewed_count = 0;
			$churned_count = 0;
			
			if ( ! empty( $product_ids ) ) {
				$renewed_count = gld_get_active_member_count( $product_ids );
				$churned_count = gld_get_churned_member_count( $product_ids );
			} else {
				// Dummy data
				$renewed_count = 924;
				$churned_count = 76;
			}
			
			$data_values = array( $renewed_count, $churned_count );
			
			// Calculate Rates
			$total = $renewed_count + $churned_count;
			$renewal_rate = $total > 0 ? round( ( $renewed_count / $total ) * 100, 1 ) : 0;
			$churn_rate = $total > 0 ? round( ( $churned_count / $total ) * 100, 1 ) : 0;
			
			// Generate Stats Footer HTML
			ob_start();
			?>
			<div class="gld-chart-stats">
				<div class="gld-stat-item">
					<div class="gld-stat-number" style="color: #10b981;"><?php echo esc_html( $renewal_rate ); ?>%</div>
					<div class="gld-stat-label"><?php esc_html_e( 'Renewal Rate', 'gamplify-gld' ); ?></div>
				</div>
				<div class="gld-stat-item">
					<div class="gld-stat-number" style="color: #ef4444;"><?php echo esc_html( $churn_rate ); ?>%</div>
					<div class="gld-stat-label"><?php esc_html_e( 'Churn Rate', 'gamplify-gld' ); ?></div>
				</div>
			</div>
			<?php
			$stats_footer = ob_get_clean();
		} else {
			// --- Default/Other Charts (Line/Bar) ---
			// Placeholder for other types logic if needed
			$labels = array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun' );
			$data_values = array( 12, 19, 3, 5, 2, 3 );
			$bg_colors = array( '#3b82f6' );
		}
		
		// Determine JS Type (override manually if specific needs)
		$js_type = ( strpos( $chart->metric_type, 'pie' ) !== false ) ? 'doughnut' : 'line';
		if ( strpos( $chart->metric_type, 'area' ) !== false ) { $js_type = 'line'; }

		$height = ! empty( $chart->ichart_height ) ? $chart->ichart_height : 300;
		$unique_id = 'gld-chart-' . $chart->id . '-' . wp_rand( 1000, 9999 );
		
		wp_enqueue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', array(), '4.4.0', true );
		
		ob_start();
		?>
		<div class="gld-chart-card">
			<div class="gld-chart-title"><?php echo esc_html( $chart->title ); ?></div>
			<div class="gld-chart-wrapper" style="height: <?php echo esc_attr( $height ); ?>px;">
				<canvas id="<?php echo esc_attr( $unique_id ); ?>"></canvas>
			</div>
			<?php echo $stats_footer; // Output footer if exists ?>
		</div>
		<script>
		document.addEventListener('DOMContentLoaded', function() {
			var ctx = document.getElementById('<?php echo esc_js( $unique_id ); ?>').getContext('2d');
			
			var data = {
				labels: <?php echo json_encode( $labels ); ?>,
				datasets: [{
					data: <?php echo json_encode( $data_values ); ?>,
					backgroundColor: <?php echo json_encode( $bg_colors ); ?>,
					borderWidth: 0,
					hoverOffset: 4
				}]
			};
			
			var config = {
				type: '<?php echo esc_js( $js_type ); ?>',
				data: data,
				options: {
					responsive: true,
					maintainAspectRatio: false,
					cutout: '<?php echo esc_js( $cutout ); ?>',
					plugins: {
						legend: {
							position: 'bottom',
							labels: {
								usePointStyle: true,
								pointStyle: 'rectRounded',
								padding: 20,
								font: {
									size: 12
								}
							}
						},
						tooltip: {
							callbacks: {
								label: function(context) {
									var label = context.label || '';
									var value = context.raw || 0;
									var total = context.chart._metasets[context.datasetIndex].total;
									var percentage = Math.round((value / total) * 100) + '%';
									return label + ': ' + value + ' (' + percentage + ')';
								}
							}
						}
					}
				}
			};
			
			<?php if ( $chart->metric_type === 'dau_wau_mau_trends_area_chart' ) : ?>
			config.data.datasets[0].fill = true;
			<?php endif; ?>
			
			new Chart(ctx, config);
		});
		</script>
		<?php
		return ob_get_clean();
	}
}
