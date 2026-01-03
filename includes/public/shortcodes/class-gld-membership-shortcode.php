<?php
/**
 * Membership Shortcode
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
 * GLD_Membership_Shortcode Class
 */
class GLD_Membership_Shortcode {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_shortcode( 'gld_membership', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Render the shortcode
	 *
	 * Usage: [gld_membership id="1"]
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
			'gld_membership'
		);
		
		if ( empty( $atts['id'] ) ) {
			return '';
		}
		
		global $wpdb;
		$table = GLD_MEMBER_KPI_TABLE;
		$kpi = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $atts['id'] ) );
		
		if ( ! $kpi ) {
			return '';
		}
		
		// Calculate count
		$count = gld_get_active_member_count( $kpi->metric_type );
		
		// CSS classes
		$card_class = 'gld-kpi-card';
		
		ob_start();
		?>
		<div class="<?php echo esc_attr( $card_class ); ?>">
			<div class="gld-kpi-header">
				<span class="gld-kpi-title"><?php echo esc_html( strtoupper( $kpi->title ) ); ?></span>
				<div class="gld-kpi-icon">
					<span class="dashicons dashicons-admin-users"></span>
				</div>
			</div>
			
			<div class="gld-kpi-value">
				<?php echo esc_html( gld_format_number( $count ) ); ?>
			</div>
			
			<?php if ( isset( $kpi->include_chart_version ) && in_array( $kpi->include_chart_version, array( 'yes', 'on', '1' ) ) ) : ?>
			<div class="gld-kpi-trend positive">
				<span class="dashicons dashicons-arrow-up-alt2"></span>
				<span class="gld-trend-value">5.2%</span>
				<span class="gld-trend-label"><?php esc_html_e( 'vs last month', 'gamplify-gld' ); ?></span>
			</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}
