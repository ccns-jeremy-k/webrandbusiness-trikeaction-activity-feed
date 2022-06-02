<?php

/**
 * Adaptive Streaming.
 *
 * @link       https://plugins360.com
 * @since      1.5.7
 *
 * @package    All_In_One_Video_Gallery
 * @subpackage All_In_One_Video_Gallery/premium
 */
 
// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AIOVG_Premium_Admin_Adaptive_Streaming class.
 *
 * @since 1.5.7
 */
class AIOVG_Premium_Admin_Adaptive_Streaming {	

	/**
	 * Register "Adaptive / Live Streaming" fields.
	 *
	 * @since  1.6.5
	 * @param  array $fields Core fields array.
	 * @return array $fields Updated fields array.
	 */
	public function register_shortcode_fields( $fields ) {
		$new_fields = array();

		foreach ( $fields['video']['sections']['general']['fields'] as $field ) {
			$new_fields[] = $field;
		
			if ( 'mp4' == $field['name'] ) {
				$new_fields[] = array(
					'name'        => 'hls',
					'label'       => __( 'HLS', 'all-in-one-video-gallery' ),
					'description' => sprintf( '%s: https://www.mysite.com/stream.m3u8', __( 'Example', 'all-in-one-video-gallery' ) ),
					'type'        => 'text',
					'value'       => ''
				);

				$new_fields[] = array(
					'name'        => 'dash',
					'label'       => __( 'M(PEG)-DASH', 'all-in-one-video-gallery' ),
					'description' => sprintf( '%s: https://www.mysite.com/stream.mpd', __( 'Example', 'all-in-one-video-gallery' ) ),
					'type'        => 'text',
					'value'       => ''
				);
			}
		}

		$fields['video']['sections']['general']['fields'] = $new_fields;
		
		return $fields;		
	}
	
	/**
	 * Add adaptive streaming form fields to the video form.
	 *
	 * @since 1.5.7
	 * @param int   $post_id Post ID.
	 */
	public function add_video_source_fields( $post_id ) {		
		$hls  = get_post_meta( $post_id, 'hls', true );
		$dash = get_post_meta( $post_id, 'dash', true );
		
		require_once AIOVG_PLUGIN_DIR . 'premium/admin/partials/adaptive-streaming.php';		
	}

	/**
	 * Save meta data.
	 *
	 * @since  1.5.7
	 * @param  int     $post_id Post ID.
	 * @param  WP_Post $post    The post object.
	 * @return int     $post_id If the save was successful or not.
	 */
	public function save_meta_data( $post_id, $post ) {	
		if ( ! isset( $_POST['post_type'] ) ) {
        	return $post_id;
    	}
	
		// Check this is the "aiovg_videos" custom post type
    	if ( 'aiovg_videos' != $post->post_type ) {
        	return $post_id;
    	}
		
		// If this is an autosave, our form has not been submitted, so we don't want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        	return $post_id;
		}
		
		// Check the logged in user has permission to edit this post
    	if ( ! aiovg_current_user_can( 'edit_aiovg_video', $post_id ) ) {
        	return $post_id;
    	}
		
		// Check if "aiovg_video_sources_nonce" nonce is set
    	if ( isset( $_POST['aiovg_video_sources_nonce'] ) ) {		
			// Verify that the nonce is valid
    		if ( wp_verify_nonce( $_POST['aiovg_video_sources_nonce'], 'aiovg_save_video_sources' ) ) {			
				// OK to save meta data
				$hls = isset( $_POST['hls'] ) ? esc_url_raw( $_POST['hls'] ) : '';
				update_post_meta( $post_id, 'hls', $hls );
				
				$dash = isset( $_POST['dash'] ) ? esc_url_raw( $_POST['dash'] ) : '';
				update_post_meta( $post_id, 'dash', $dash );								
			}			
		}
		
		return $post_id;	
	}

}
