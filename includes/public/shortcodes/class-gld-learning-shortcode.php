<?php
/**
 * Learning Shortcodes
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
 * GLD_Learning_Shortcode Class
 */
class GLD_Learning_Shortcode {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_shortcode( 'gld_learning_kpi', array( $this, 'render_kpi' ) );
		add_shortcode( 'gld_learning_table', array( $this, 'render_table' ) );
	}

	/**
	 * Render the KPI shortcode
	 *
	 * [gld_learning_kpi id="1"]
	 */
	public function render_kpi( $atts ) {
		$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'gld_learning_kpi' );
		if ( empty( $atts['id'] ) ) return '';

		global $wpdb;
		$kpi = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GLD_LEARNING_KPI_TABLE . " WHERE id = %d", $atts['id'] ) );
		if ( ! $kpi ) return '';

		$count = gld_get_learning_metric_count( $kpi->metric_type, $kpi->filter_by_course );

		// Determine dynamic attributes based on metric type
		$icon = 'dashicons-welcome-learn-more';
		$label = __( 'Total completions', 'gamplify-gld' );
		$type_class = 'type-default';

		switch ( $kpi->metric_type ) {
			case 'course_completed':
				$icon = 'dashicons-book-alt';
				$label = __( 'Total completions', 'gamplify-gld' );
				$type_class = 'type-completed';
				break;
			case 'course_in_progress':
				$icon = 'dashicons-text-page';
				$label = __( 'Exercises finished', 'gamplify-gld' );
				$type_class = 'type-progress';
				break;
			case 'quiz_completed':
				$icon = 'dashicons-welcome-learn-more';
				$label = __( 'Assessments completed', 'gamplify-gld' );
				$type_class = 'type-quiz';
				break;
		}

		ob_start();
		?>
		<div class="gld-kpi-card learning-kpi <?php echo esc_attr( $type_class ); ?>">
			<div class="gld-kpi-header">
				<span class="gld-kpi-title"><?php echo esc_html( strtoupper( $kpi->title ) ); ?></span>
				<div class="gld-kpi-icon">
					<span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
				</div>
			</div>
			<div class="gld-kpi-value"><?php echo esc_html( gld_format_number( $count ) ); ?></div>
			<div class="gld-kpi-footer">
				<span class="gld-kpi-label"><?php echo esc_html( $label ); ?></span>
			</div>
			<?php if ( $kpi->include_chart_version === 'yes' ) : ?>
				<div class="gld-kpi-chart-wrapper" style="height: 40px; margin-top: 15px;">
                    <!-- Placeholder for sparking chart -->
					<div class="gld-micro-chart"></div>
				</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render the Table shortcode
	 *
	 * [gld_learning_table id="1"]
	 */
	public function render_table( $atts ) {
		$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'gld_learning_table' );
		if ( empty( $atts['id'] ) ) return '';

		global $wpdb;
		$config = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GLD_LEARNING_DATA_TABLE . " WHERE id = %d", $atts['id'] ) );
		if ( ! $config ) return '';

		$per_page = !empty( $config->rows_to_display ) ? intval( $config->rows_to_display ) : 10; // Default
		$page = 1;

		$data = gld_get_learning_table_data( $config->filter_by_course, $per_page, $page );

		ob_start();
		?>
		<div class="gld-table-container summary-table-layout" 
			 data-id="<?php echo esc_attr( $config->id ); ?>" 
			 data-filter="<?php echo esc_attr( $config->filter_by_course ); ?>"
			 data-nonce="<?php echo esc_attr( wp_create_nonce( 'gld_public_nonce' ) ); ?>">
			
			<div class="gld-table-header">
				<h3 class="gld-table-title"><?php echo esc_html( $config->title ); ?></h3>
			</div>

			<div class="gld-table-responsive">
				<table class="gld-summary-table">
					<thead>
						<tr>
							<th class="col-course-name"><?php esc_html_e( 'Course Name', 'gamplify-gld' ); ?></th>
							<th class="col-students"><?php esc_html_e( 'Students', 'gamplify-gld' ); ?></th>
							<th class="col-completions"><?php esc_html_e( 'Completions', 'gamplify-gld' ); ?></th>
							<th class="col-avg-score"><?php esc_html_e( 'Avg Score', 'gamplify-gld' ); ?></th>
							<th class="col-actions"><?php esc_html_e( 'Actions', 'gamplify-gld' ); ?></th>
						</tr>
					</thead>
					<tbody class="gld-table-body">
						<?php if ( ! empty( $data['items'] ) ) : ?>
							<?php foreach ( $data['items'] as $item ) : ?>
								<tr>
									<td class="col-course-name"><strong><?php echo esc_html( $item['course_name'] ); ?></strong></td>
									<td class="col-students"><?php echo esc_html( gld_format_number( $item['students'] ) ); ?></td>
									<td class="col-completions">
										<span class="gld-pill pill-pink"><?php echo esc_html( gld_format_number( $item['completions'] ) ); ?></span>
									</td>
									<td class="col-avg-score">
										<span class="gld-score-text"><?php echo esc_html( $item['avg_score'] ); ?>%</span>
									</td>
									<td class="col-actions">
										<button class="gld-view-users-btn" data-course-id="<?php echo esc_attr( $item['course_id'] ); ?>">
											<span class="dashicons dashicons-visibility"></span>
											<?php esc_html_e( 'View Users', 'gamplify-gld' ); ?>
											<span class="dashicons dashicons-arrow-right-alt2"></span>
										</button>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="5" class="gld-no-data"><?php esc_html_e( 'No courses found.', 'gamplify-gld' ); ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>

			<?php if ( $data['pages'] > 1 ) : ?>
				<div class="gld-table-pagination">
					<?php for ( $i = 1; $i <= $data['pages']; $i++ ) : ?>
						<button class="gld-page-link <?php echo $i === 1 ? 'active' : ''; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></button>
					<?php endfor; ?>
				</div>
			<?php endif; ?>

			<!-- Modal Structure -->
			<div class="gld-modal-overlay gld-hidden" id="gld-user-modal">
				<div class="gld-modal-content">
					<div class="gld-modal-header">
						<h4 class="gld-modal-title"><?php esc_html_e( 'Course Users', 'gamplify-gld' ); ?></h4>
						<button class="gld-modal-close">&times;</button>
					</div>
					<div class="gld-modal-body">
						<div id="gld-modal-user-list-content">
							<div class="gld-modal-placeholder">
								<span class="dashicons dashicons-update spin gld-hidden"></span>
								<p class="gld-modal-msg"></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
