<?php
/**
 * Public AJAX Handlers
 *
 * @package    Gamplify_GLD
 * @subpackage Gamplify_GLD/public
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class GLD_Public_Ajax {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_ajax_gld_get_public_learning_table', array( $this, 'get_learning_table' ) );
		add_action( 'wp_ajax_nopriv_gld_get_public_learning_table', array( $this, 'get_learning_table' ) );

		add_action( 'wp_ajax_gld_get_course_drilldown', array( $this, 'get_course_drilldown' ) );
		add_action( 'wp_ajax_nopriv_gld_get_course_drilldown', array( $this, 'get_course_drilldown' ) );
	}

	/**
	 * Get learning table data via AJAX
	 */
	public function get_learning_table() {
		check_ajax_referer( 'gld_public_nonce', 'nonce' );

		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		$page = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;

		global $wpdb;
		$config = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GLD_LEARNING_DATA_TABLE . " WHERE id = %d", $id ) );
		
		if ( ! $config ) {
			wp_send_json_error( array( 'message' => 'Config not found' ) );
		}

		$per_page = !empty( $config->rows_to_display ) ? intval( $config->rows_to_display ) : 10; // Default
		$data = gld_get_learning_table_data( $config->filter_by_course, $per_page, $page );

		ob_start();
		if ( ! empty( $data['items'] ) ) {
			foreach ( $data['items'] as $item ) {
				?>
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
				<?php
			}
		} else {
			?>
			<tr>
				<td colspan="5" class="gld-no-data"><?php esc_html_e( 'No courses found.', 'gamplify-gld' ); ?></td>
			</tr>
			<?php
		}
		$html = ob_get_clean();

		wp_send_json_success( array(
			'html'  => $html,
			'pages' => $data['pages'],
			'current_page' => $page
		) );
	}

	/**
	 * Get course drilldown data (Progress & Assessments) for the modal
	 */
	public function get_course_drilldown() {
		check_ajax_referer( 'gld_public_nonce', 'nonce' );

		$course_id = isset( $_POST['course_id'] ) ? absint( $_POST['course_id'] ) : 0;
		if ( ! $course_id ) {
			wp_send_json_error( array( 'message' => 'Invalid Course ID' ) );
		}

		$course = get_post( $course_id );
		$data = gld_get_course_drilldown_data( $course_id );

		ob_start();
		?>
		<div class="gld-modal-drilldown" data-course-id="<?php echo esc_attr( $course_id ); ?>">
			<div class="gld-modal-tabs">
				<button class="gld-tab-btn active" data-tab="progress"><?php esc_html_e( 'Progress', 'gamplify-gld' ); ?></button>
				<button class="gld-tab-btn" data-tab="assessments"><?php esc_html_e( 'Assessments', 'gamplify-gld' ); ?></button>
			</div>

			<div class="gld-tab-content active" id="gld-tab-progress">
				<div class="gld-progress-list">
					<?php if ( ! empty( $data['lessons'] ) ) : ?>
						<?php foreach ( $data['lessons'] as $lesson ) : ?>
							<div class="gld-progress-item">
								<div class="gld-progress-info">
									<span class="gld-progress-title"><?php echo esc_html( $lesson['title'] ); ?></span>
									<span class="gld-progress-percent"><?php echo esc_html( $lesson['progress'] ); ?>%</span>
								</div>
								<div class="gld-progress-bar-wrapper">
									<div class="gld-progress-bar-fill" style="width: <?php echo esc_attr( $lesson['progress'] ); ?>%;"></div>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<p class="gld-no-data"><?php esc_html_e( 'No lessons found.', 'gamplify-gld' ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<div class="gld-tab-content" id="gld-tab-assessments">
				<div class="gld-assessment-list">
					<?php if ( ! empty( $data['quizzes'] ) ) : ?>
						<?php foreach ( $data['quizzes'] as $quiz ) : ?>
							<div class="gld-assessment-card">
								<span class="gld-assessment-title"><?php echo esc_html( $quiz['title'] ); ?></span>
								<div class="gld-assessment-meta">
									<span class="gld-assessment-score"><?php echo esc_html( $quiz['progress'] ); ?>%</span>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<p class="gld-no-data"><?php esc_html_e( 'No quizzes found.', 'gamplify-gld' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
		$html = ob_get_clean();

		wp_send_json_success( array( 
			'html' => $html,
			'title' => $course->post_title
		) );
	}
}
