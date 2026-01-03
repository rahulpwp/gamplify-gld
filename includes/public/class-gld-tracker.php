<?php
/**
 * Tracker Class
 *
 * @package    Gamplify_GLD
 * @subpackage Gamplify_GLD/public
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * GLD_Tracker Class
 */
class GLD_Tracker {

	/**
	 * Track page view
	 *
	 * @return void
	 */
	public function track_page_view() {
		if ( ! gld_get_setting( 'enable_page_tracking', true ) ) {
			return;
		}
		
		global $post;
		
		$event_data = array(
			'url'       => esc_url_raw( $_SERVER['REQUEST_URI'] ),
			'title'     => is_singular() && $post ? get_the_title( $post ) : wp_get_document_title(),
			'post_id'   => is_singular() && $post ? $post->ID : 0,
			'post_type' => is_singular() && $post ? $post->post_type : '',
			'referrer'  => isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( $_SERVER['HTTP_REFERER'] ) : '',
		);
		
		gld_track_event( 'page_view', 'Page View', $event_data );
	}
	
	/**
	 * Track custom event
	 *
	 * @param string $event_name Event name
	 * @param array  $event_data Event data
	 * @return int|false
	 */
	public function track_custom_event( $event_name, $event_data = array() ) {
		return gld_track_event( 'custom', $event_name, $event_data );
	}
}
