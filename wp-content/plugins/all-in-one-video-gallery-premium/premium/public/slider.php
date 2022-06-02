<?php

/**
 * Slider.
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
 * AIOVG_Premium_Public_Slider class.
 *
 * @since 1.5.7
 */
class AIOVG_Premium_Public_Slider {

	/**
	 * Register "Slider" template.
	 *
	 * @since  1.5.7
	 * @param  array $templates Core templates array.
	 * @return array $templates Updated templates array.
	 */
	public function add_slider_template( $templates ) {	
		$templates['slider'] = __( 'Slider', 'all-in-one-video-gallery' );
		return $templates;		
	}
	
	/**
	 * Register "Slider" block fields.
	 *
	 * @since  1.5.7
	 * @param  array $fields Core fields array.
	 * @return array $fields Updated fields array.
	 */
	public function register_shortcode_fields( $fields ) {
		$videos_settings = get_option( 'aiovg_videos_settings' );

		if ( ! isset( $videos_settings['slider_layout'] ) ) {
			return $fields;
		}

		$videos = array();

		foreach ( $fields['videos']['sections']['gallery']['fields'] as $key => $field ) {
			$videos[] = $field;

			if ( 'ratio' == $field['name'] ) {
				$videos[] = array(
					'name'        => 'slider_layout',
					'label'       => __( 'Slider Layout', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'select',
					'options'     => array(
						'player'     => __( 'Videos Banner', 'all-in-one-video-gallery' ),
						'thumbnails' => __( 'Thumbnails Slider (Links to the single video page)', 'all-in-one-video-gallery' ),
						'popup'      => __( 'Thumbnails Slider (Opens videos in a popup)', 'all-in-one-video-gallery' ),
						'both'       => __( 'Player + Thumbnails Slider', 'all-in-one-video-gallery' )
					),
					'value'       => $videos_settings['slider_layout']
				);
			}

			if ( 'thumbnail_style' == $field['name'] ) {
				$videos[] = array(
					'name'        => 'link_title',
					'label'       => __( 'Link Video Titles to the Single Video Page?', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'checkbox',
					'value'       => $videos_settings['link_title']
				);

				$videos[] = array(
					'name'        => 'show_player_title',
					'label'       => __( 'Show / Hide (Player)', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'header',
					'value'       => 0
				);

				$videos[] = array(
					'name'        => 'show_player_title',
					'label'       => __( 'Video Title', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'checkbox',
					'value'       => isset( $videos_settings['display_player']['title'] )
				);

				$videos[] = array(
					'name'        => 'show_player_description',
					'label'       => __( 'Video Description', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'checkbox',
					'value'       => isset( $videos_settings['display_player']['description'] )
				);
			}
		}

		$fields['videos']['sections']['gallery']['fields'] = array_merge(
			$videos,
			array(				
				array(
					'name'        => 'arrows',
					'label'       => __( 'Arrows', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'checkbox',
					'value'       => $videos_settings['arrows']
				),
				array(
					'name'        => 'arrow_size',
					'label'       => __( 'Arrow Size (in pixels)', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'number',
					'min'         => 0,
					'max'         => 250,
					'step'        => 1,
					'value'       => $videos_settings['arrow_size']
				),
				array(
					'name'        => 'arrow_bg_color',
					'label'       => __( 'Arrow BG Color', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'color',
					'value'       => $videos_settings['arrow_bg_color']
				),
				array(
					'name'        => 'arrow_icon_color',
					'label'       => __( 'Arrow Icon Color', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'color',
					'value'       => $videos_settings['arrow_icon_color']
				),
				array(
					'name'        => 'arrow_radius',
					'label'       => __( 'Arrow Radius (in pixels)', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'number',
					'min'         => 0,
					'max'         => 250,
					'step'        => 1,
					'value'       => $videos_settings['arrow_radius']
				),
				array(
					'name'        => 'arrow_top_offset',
					'label'       => __( 'Arrow Top Offset (in percentage)', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'number',
					'min'         => 0,
					'max'         => 100,
					'step'        => 1,
					'value'       => $videos_settings['arrow_top_offset']
				),
				array(
					'name'        => 'arrow_left_offset',
					'label'       => __( 'Arrow Left Offset (in pixels)', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'number',
					'min'         => -500,
					'max'         => 500,
					'step'        => 1,
					'value'       => $videos_settings['arrow_left_offset']
				),
				array(
					'name'        => 'arrow_right_offset',
					'label'       => __( 'Arrow Right Offset (in pixels)', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'number',
					'min'         => -500,
					'max'         => 500,
					'step'        => 1,
					'value'       => $videos_settings['arrow_right_offset']
				),
				array(
					'name'        => 'dots',
					'label'       => __( 'Dots', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'checkbox',
					'value'       => $videos_settings['dots']
				),
				array(
					'name'        => 'dot_size',
					'label'       => __( 'Dot Size', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'number',
					'min'         => 0,
					'max'         => 250,
					'step'        => 1,
					'value'       => $videos_settings['dot_size']
				),
				array(
					'name'        => 'dot_color',
					'label'       => __( 'Dot Color', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'color',
					'value'       => $videos_settings['dot_color']
				),
				array(
					'name'        => 'slider_autoplay',
					'label'       => __( 'Slider Autoplay', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'checkbox',
					'value'       => $videos_settings['slider_autoplay']
				),
				array(
					'name'        => 'autoplay_speed',
					'label'       => __( 'Autoplay Speed (in milliseconds)', 'all-in-one-video-gallery' ),
					'description' => '',
					'type'        => 'number',
					'min'         => 100,
					'max'         => 300000,
					'step'        => 100,
					'value'       => $videos_settings['autoplay_speed']
				)
			)
		);
		
		return $fields;		
	}
	
	/**
	 * Get filtered php template file path.
	 *
	 * @since  1.5.7
	 * @param  array  $template   PHP file path.
	 * @param  array  $attributes An associative array of attributes.
	 * @return string             Filtered file path.
	 */
	public function load_template( $template, $attributes = array() ) {
		if ( 'videos-template-classic.php' == basename( $template ) ) {
			if ( 'slider' == $attributes['template'] ) {
				// Enqueue script / style dependencies
				wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-slick' );
				if ( 'popup' == $attributes['slider_layout'] ) {	
					wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-magnific-popup' );
				}
				wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-premium-public' );
		
				wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-slick' );
				if ( 'popup' == $attributes['slider_layout'] ) {	
					wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-magnific-popup' );
				}
				wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-premium-public' );
			
				$template = AIOVG_PLUGIN_DIR . 'premium/public/templates/videos-template-slider.php';
			}
		}		

		return $template;
	}

}
