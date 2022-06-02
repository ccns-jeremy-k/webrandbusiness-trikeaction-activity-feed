<?php

/**
 * The admin-specific functionality of the plugin.
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
 * AIOVG_Premium_Admin class.
 *
 * @since 1.5.7
 */
class AIOVG_Premium_Admin {

	/**
	 * Insert missing plugin options.
	 *
	 * @since 1.5.7
	 */
	public function insert_missing_options() {
		// Insert the brand settings
		if ( false == get_option( 'aiovg_brand_settings' ) ) {			
			$defaults = array(
				'show_logo'      => 1,
				'logo_image'     => '',
				'logo_link'      => esc_url( home_url() ),
				'logo_position'  => 'bottomleft',
				'logo_margin'    => 8,
				'copyright_text' => sprintf( __( 'Proudly by %s', 'all-in-one-video-gallery' ), get_option( 'blogname' ) )
			);
				
        	add_option( 'aiovg_brand_settings', $defaults );			
		}

		// Insert the thumbnail generator settings
		if ( false == get_option( 'aiovg_thumbnail_generator_settings' ) ) {			
			$defaults = array(
				'enable_html5_thumbnail_generator' => 1,
				'ffmpeg_path'                      => '',
				'ffmpeg_images_count'              => 10
			);
				
        	add_option( 'aiovg_thumbnail_generator_settings', $defaults );			
		}
		
		// Insert the automations settings
		if ( false == get_option( 'aiovg_automations_settings' ) ) {			
			$defaults = array(
				'youtube_api_key' => '',
				'is_fast_mode'    => 1
			);
				
        	add_option( 'aiovg_automations_settings', $defaults );			
		}		
		
		// Insert the slider & popup settings
		$videos_settings = get_option( 'aiovg_videos_settings' );

		$new_videos_settings = array();

		if ( ! array_key_exists( 'slider_layout', $videos_settings ) ) {
			$new_videos_settings = array(
				'slider_layout'      => 'thumbnails',								
				'arrows'             => 1,
				'arrow_size'         => 24,
				'arrow_bg_color'     => '#08c',
				'arrow_icon_color'   => '#fff',
				'arrow_radius'       => 12,
				'arrow_top_offset'   => 30,
				'arrow_left_offset'  => -15,
				'arrow_right_offset' => -15,
				'dots'               => 1,
				'dot_size'           => 24,
				'dot_color'          => '#08c'				
			);        			
		}
		
		if ( ! array_key_exists( 'link_title', $videos_settings ) ) {
			$new_videos_settings = array_merge( $new_videos_settings, array(
				'link_title'      => 0,
				'display_player'  => array(),
				'slider_autoplay' => 0,
				'autoplay_speed'  => 5000
			));
		}

		if ( count( $new_videos_settings ) ) {
			update_option( 'aiovg_videos_settings', array_merge( $videos_settings, $new_videos_settings ) );	
		}
		
		// Insert the user submission settings
		if ( false == get_option( 'aiovg_user_submission_settings' ) ) {			
			$defaults = array(
				'assign_categories'    => 1,
				'assign_tags'          => 0,
				'allowed_source_types' => array(
					'default'     => 'default',
					'youtube'     => 'youtube',
					'vimeo'       => 'vimeo',
					'dailymotion' => 'dailymotion',
					'facebook'    => 'facebook',
					'adaptive'    => 'adaptive'
				),
				'default_source_type'  => 'default',
				'allow_file_uploads'   => 1,
				'max_upload_size'      => '',
				'new_video_status'     => 'publish',
				'edit_video_status'    => 'publish',
				'terms_and_conditions' => ''
			);
				
			add_option( 'aiovg_user_submission_settings', $defaults );

			// Insert the missing pages
			$page_settings = get_option( 'aiovg_page_settings' );

			if ( ! array_key_exists( 'user_dashboard', $page_settings ) ) {
				aiovg_insert_missing_pages();			
			}
		}

		// Insert the antispam settings
		if ( false == get_option( 'aiovg_antispam_settings' ) ) {
			$permitted_chars = 'abcdefghijklmnopqrstuvwxyz';
			$honeypot_field_name = substr( str_shuffle( $permitted_chars ), 0, 6 ).rand( 1, 9999 );

			$defaults = array(
				'honeypot'              => 0,
				'honeypot_field_name'   => $honeypot_field_name,
				'timetrap'              => 0,
				'timetrap_minimum_time' => 3
			);

        	add_option( 'aiovg_antispam_settings', $defaults );			
    	}
		
		// Insert the user account settings
		if ( false == get_option( 'aiovg_user_account_settings' ) ) {			
			$defaults = array(
				'custom_login'           => '',
				'custom_register'        => '',
				'custom_forgot_password' => ''
			);
				
			add_option( 'aiovg_user_account_settings', $defaults );
		}
			
		// Insert the video pending review email settings
		if ( false == get_option( 'aiovg_email_video_pending_review_settings' ) ) {			
			$defaults = array(
				'subject' => __( '[{site_name}] Video "{video_title}" received', 'all-in-one-video-gallery' ),
				'body'    => __( "Dear {name},\n\nYour video \"{video_title}\" has been received and it's pending review. This review process could take up to 48 hours.\n\nThanks,\nThe Administrator of {site_name}", 'all-in-one-video-gallery' )
			);
				
			add_option( 'aiovg_email_video_pending_review_settings', $defaults );
		}	
			
		// Insert the video published email settings
		if ( false == get_option( 'aiovg_email_video_published_settings' ) ) {			
			$defaults = array(
				'subject' => __( '[{site_name}] Video "{video_title}" published', 'all-in-one-video-gallery' ),
				'body'    => __( "Dear {name},\n\nYour video \"{video_title}\" is now available at {video_url} and can be viewed by the public.\n\nThanks,\nThe Administrator of {site_name}", 'all-in-one-video-gallery' )
			);
				
        	add_option( 'aiovg_email_video_published_settings', $defaults );
		}		

		// Insert the seo settings
		if ( false == get_option( 'aiovg_seo_settings' ) ) {			
			$defaults = array(
				'schema_markup' => 1
			);
				
        	add_option( 'aiovg_seo_settings', $defaults );
		}

		// Insert the ads settings
		if ( aiovg_fs()->is_plan( 'business' ) ) {			
			if ( false == get_option( 'aiovg_ads_settings' ) ) {			
				$defaults = array(
					'enable_ads'             => 0,
					'vast_url'               => '',
					'vpaid_mode'             => 'insecure',
					'livestream_ad_interval' => 300,
					'use_gpt'                => 0
				);
					
				add_option( 'aiovg_ads_settings', $defaults );			
			}
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.5.7
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 
			AIOVG_PLUGIN_SLUG . '-premium-admin', 
			AIOVG_PLUGIN_URL . 'premium/admin/assets/css/admin.css', 
			array(), 
			AIOVG_PLUGIN_VERSION, 
			'all' 
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.5.7
	 */
	public function enqueue_scripts() {
		$thumbnail_generator_settings = get_option( 'aiovg_thumbnail_generator_settings' );

		wp_enqueue_script( 
			AIOVG_PLUGIN_SLUG . '-premium-admin', 
			AIOVG_PLUGIN_URL . 'premium/admin/assets/js/admin.js', 
			array( 'jquery' ), 
			AIOVG_PLUGIN_VERSION, 
			false 
		);

		wp_localize_script( 
			AIOVG_PLUGIN_SLUG . '-premium-admin', 
			'aiovg_premium_admin', 
			array(
				'ffmpeg_enabled' => empty( $thumbnail_generator_settings['ffmpeg_path'] ) || empty( $thumbnail_generator_settings['ffmpeg_images_count'] ) ? false : true,
				'i18n'           => array(
					'loading_api_data'                       => __( 'Please wait while we are loading data from the API server...', 'all-in-one-video-gallery' ),
					'ffmpeg_thumbnail_generation_failed'     => __( 'Sorry, the auto-thumbnail generation failed.', 'all-in-one-video-gallery' ),
					'thumbnail_generator_capture_image'      => __( 'Use the "Capture Image" button below to generate an image from your video.', 'all-in-one-video-gallery' ),
					'thumbnail_generator_select_image'       => __( 'Select an image from the options below.', 'all-in-one-video-gallery' ),
					'thumbnail_generator_processing'         => __( 'Generating images...', 'all-in-one-video-gallery' ),
					'thumbnail_generator_video_not_found'    => __( 'No video found. Add a video in the "MP4" video field to capture an image.', 'all-in-one-video-gallery' ),
					'thumbnail_generator_invalid_video_file' => __( 'Invalid video file.', 'all-in-one-video-gallery' ),
					'thumbnail_generator_cors_error'         => __( "Sorry, your video file server doesn't give us permission to generate an image from the video.", 'all-in-one-video-gallery' )
				)			
			)
		);
	}
	
	/**
	 * Get the list of custom plugin pages.
	 * 
	 * @since  1.6.5
	 * @param  array $pages Array of custom plugin pages.
	 * @return array $pages Updated array of custom plugin pages.
	 */
	function get_custom_pages_list( $pages ) {
		$pages['user_dashboard'] = array( 
			'title'   => __( 'User Dashboard', 'all-in-one-video-gallery' ), 
			'content' => '[aiovg_user_dashboard]' 
		);

		$pages['video_form'] = array( 
			'title'   => __( 'Video Form', 'all-in-one-video-gallery' ), 
			'content' => '[aiovg_video_form]' 
		);

		return $pages;
	}

}
