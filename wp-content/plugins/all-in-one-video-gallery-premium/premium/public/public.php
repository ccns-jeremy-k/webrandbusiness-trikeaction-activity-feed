<?php

/**
 * The public-facing functionality of the plugin.
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
 * AIOVG_Premium_Public class.
 *
 * @since 1.5.7
 */
class AIOVG_Premium_Public {

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.5.7
	 */
	public function register_styles() {
		wp_register_style( 
			AIOVG_PLUGIN_SLUG . '-slick', 
			AIOVG_PLUGIN_URL . 'premium/vendor/slick/slick.css', 
			array(), 
			'1.8.1', 
			'all' 
		);

		wp_register_style( 
			AIOVG_PLUGIN_SLUG . '-premium-public', 
			AIOVG_PLUGIN_URL . 'premium/public/assets/css/public.css', 
			array(), 
			AIOVG_PLUGIN_VERSION, 
			'all' 
		);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.5.7
	 */
	public function register_scripts() {
		$thumbnail_generator_settings = get_option( 'aiovg_thumbnail_generator_settings' );	
		$antispam_settings = get_option( 'aiovg_antispam_settings' );	

		wp_register_script( 
			AIOVG_PLUGIN_SLUG . '-gpt-proxy', 
			'https://imasdk.googleapis.com/js/sdkloader/gpt_proxy.js', 
			array(), 
			AIOVG_PLUGIN_VERSION
		);

		wp_register_script( 
			AIOVG_PLUGIN_SLUG . '-premium-player', 
			AIOVG_PLUGIN_URL . 'premium/public/assets/js/player.js', 
			array( 'jquery' ), 
			AIOVG_PLUGIN_VERSION, 
			false 
		);

		wp_register_script( 
			AIOVG_PLUGIN_SLUG . '-slick', 
			AIOVG_PLUGIN_URL . 'premium/vendor/slick/slick.min.js', 
			array( 'jquery' ), 
			'1.8.1', 
			false 
		);

		wp_register_script( 
			AIOVG_PLUGIN_SLUG . '-premium-public', 
			AIOVG_PLUGIN_URL . 'premium/public/assets/js/public.js', 
			array( 'jquery' ), 
			AIOVG_PLUGIN_VERSION, 
			false 
		);

		wp_localize_script( 
			AIOVG_PLUGIN_SLUG . '-premium-public', 
			'aiovg_premium', 
			array(
				'ajax_url'                          => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'                        => wp_create_nonce( 'aiovg_ajax_nonce' ),
				'site_url'                          => esc_url_raw( get_site_url() ),
				'magic_field'                       => ( ! empty( $antispam_settings['honeypot'] ) || ! empty( $antispam_settings['timetrap'] ) ) ? true : false,
				'html5_thumbnail_generator_enabled' => empty( $thumbnail_generator_settings['enable_html5_thumbnail_generator'] ) ? false : true,
				'ffmpeg_enabled'                    => empty( $thumbnail_generator_settings['ffmpeg_path'] ) || empty( $thumbnail_generator_settings['ffmpeg_images_count'] ) ? false : true,
				'i18n'                              => array(
					'required'                               => __( 'This is a required field.', 'all-in-one-video-gallery' ),
					'invalid'                                => __( 'Invalid file format.', 'all-in-one-video-gallery' ),
					'loaded'                                 => __( 'Loaded', 'all-in-one-video-gallery' ),
					'processing'                             => __( 'processing...', 'all-in-one-video-gallery' ),
					'pending_upload'                         => __( 'Please wait until the upload is complete', 'all-in-one-video-gallery' ),
					'unknown_error'                          => __( 'Unknown error.', 'all-in-one-video-gallery' ),
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
	 * Enqueue Gutenberg block assets for backend editor.
	 *
	 * @since 1.6.1
	 */
	public function enqueue_block_editor_assets() {
		// Styles
		$this->register_styles();

		wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-slick' );
		wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-premium-public' );

		// Scripts
		$this->register_scripts();

		wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-slick' );
		wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-premium-public' );
	}

}
