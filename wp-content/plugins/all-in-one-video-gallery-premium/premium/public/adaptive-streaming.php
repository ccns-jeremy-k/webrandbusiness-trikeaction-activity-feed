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
 * AIOVG_Premium_Public_Adaptive_Streaming class.
 *
 * @since 1.5.7
 */
class AIOVG_Premium_Public_Adaptive_Streaming {

	/**
	 * Add support for HLS and M(PEG)-DASH.
	 *
	 * @since  1.5.7
	 * @param  array $mimes Array of allowed mime types.
	 * @return array        Filtered mime types array.
	 */
	public function add_mime_types( $mimes ) {			
		$mimes['m3u8'] = 'application/x-mpegurl';
		$mimes['mpd']  = 'application/dash+xml';

		return $mimes;		
	}

	/**
	 * Add "Adaptive / Live Streaming" source type to the video form.
	 *
	 * @since  1.5.7
	 * @param  array $types Array of default source types.
	 * @return array        Filtered source types array.
	 */
	public function add_video_source_types( $types ) {		
		$types['adaptive'] = __( 'Adaptive / Live Streaming', 'all-in-one-video-gallery' );
		return $types;		
	}
	
	/**
	 * Filter video sources.
	 *
	 * @since  2.4.0
	 * @param  array $sources Video sources.
	 * @param  array $params  Player params.
	 * @return array $sources Filtered video sources.
	 */
	public function player_sources( $sources, $params ) {	
		$formats = array(			
			'hls'  => 'application/x-mpegurl',
			'dash' => 'application/dash+xml'
		);
		
		$post_id = (int) $params['post_id'];		
		$type    = '';

		if ( $post_id > 0 ) {
			$type = get_post_meta( $post_id, 'type', true );
		}
			
		foreach ( $formats as $format => $mime ) {
			$src = '';
			
			if ( isset( $params[ $format ] ) ) {
				$src = $params[ $format ];
			} elseif ( $post_id > 0 && 'adaptive' == $type ) {
				$src = get_post_meta( $post_id, $format, true );
			}
			
			if ( ! empty( $src ) ) {
				$sources[ $format ] = array(
					'type' => $mime,
					'src'  => $src
				);
			}
		}

		if ( isset( $sources['mp4'] ) && ! isset( $sources['hls'] ) ) {
			$mp4_src = $sources['mp4']['src'];

			if ( strpos( $mp4_src, 'videos.files.wordpress.com' ) !== false ) {
				$hls_src = str_replace( '.mp4', '.master.m3u8', $mp4_src );
				$has_hls = 0;

				$query = parse_url( $mp4_src, PHP_URL_QUERY );
				parse_str( $query, $parsed_url );

				if ( isset( $parsed_url['isnew'] ) ) {
					$has_hls = (int) $parsed_url['isnew'];
				} else {					
					$hls_response = wp_remote_get( $hls_src );

					if ( 200 == wp_remote_retrieve_response_code( $hls_response ) ) {
						$has_hls = 1;
					}

					if ( $post_id > 0 && 'default' == $type ) {
						update_post_meta( $post_id, 'mp4', aiovg_sanitize_url( add_query_arg( 'isnew', $has_hls, $mp4_src ) ) );
					}
				}

				if ( $has_hls ) {
					$hls_source = array(
						'hls' => array(
							'type' => 'application/x-mpegurl',
							'src'  => $hls_src
						)
					);

					$sources = array_merge( $hls_source, $sources );
				}
			}
		}
		
		return $sources;	
	}

	/**
	 * Enqueue necessary scripts.
	 *
	 * @since 2.4.0
	 * @param array $params Player params.
	 */
	public function player_scripts( $params ) {
		if ( isset( $params['sources']['hls'] ) || isset( $params['sources']['dash'] ) ) {
			wp_enqueue_script( 
				AIOVG_PLUGIN_SLUG . '-http-streaming', 
				AIOVG_PLUGIN_URL . 'premium/vendor/videojs-plugins/http-streaming/videojs-http-streaming.min.js', 
				array(), 
				'2.14.2', 
				false 
			);

			if ( in_array( 'qualitySelector', $params['settings']['player']['controlBar']['children'] ) ) {
				wp_enqueue_style( 
					AIOVG_PLUGIN_SLUG . '-videojs-quality-menu', 
					AIOVG_PLUGIN_URL . 'premium/vendor/videojs-plugins/videojs-quality-menu/videojs-quality-menu.css', 
					array(), 
					'1.4.0',
					'all' 
				);

				wp_enqueue_script( 
					AIOVG_PLUGIN_SLUG . '-videojs-quality-menu', 
					AIOVG_PLUGIN_URL . 'premium/vendor/videojs-plugins/videojs-quality-menu/videojs-quality-menu.min.js', 
					array(), 
					'1.4.0', 
					false 
				);

				wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-premium-player' );
			}
		}
	}

	/**
	 * Filter player page URL.
	 * 
	 * @since  1.5.7
	 * @param  string $url     Default player page URL.
	 * @param  int    $post_id Post ID.
	 * @param  array  $atts    Video/Player attributes.
	 * @return string $url     Filtered player page URL.
	 */
	public function player_page_url( $url, $post_id, $atts ) {		
		if ( ! empty( $atts['hls'] ) ) {
			$url = add_query_arg( 'hls', urlencode( $atts['hls'] ), $url );
		}

		if ( ! empty( $atts['dash'] ) ) {
			$url = add_query_arg( 'dash', urlencode( $atts['dash'] ), $url );
		}
		
		return $url;	
	}

	/**
	 * Filter video sources.
	 *
	 * @since  1.5.7
	 * @param  array $sources Video sources.
	 * @return array $sources Filtered video sources.
	 */
	public function iframe_player_sources( $sources ) {	
		$formats = array(			
			'hls'  => 'application/x-mpegurl',
			'dash' => 'application/dash+xml'
		);
		
		$post_id = (int) get_query_var( 'aiovg_video' );		
		$type    = '';
		
		if ( $post_id > 0 ) {
			$type = get_post_meta( $post_id, 'type', true );
		}
			
		foreach ( $formats as $format => $mime ) {
			$src = '';
			
			if ( isset( $_GET[ $format ] ) ) {
				$src = esc_url_raw( $_GET[ $format ] );
			} elseif ( $post_id > 0 && 'adaptive' == $type ) {
				$src = get_post_meta( $post_id, $format, true );
			}
			
			if ( ! empty( $src ) ) {
				$sources[ $format ] = array(
					'type' => $mime,
					'src'  => $src
				);
			}
		}

		if ( isset( $sources['mp4'] ) && ! isset( $sources['hls'] ) ) {
			$mp4_src = $sources['mp4']['src'];

			if ( strpos( $mp4_src, 'videos.files.wordpress.com' ) !== false ) {
				$hls_src = str_replace( '.mp4', '.master.m3u8', $mp4_src );
				$has_hls = 0;

				$query = parse_url( $mp4_src, PHP_URL_QUERY );
				parse_str( $query, $parsed_url );

				if ( isset( $parsed_url['isnew'] ) ) {
					$has_hls = (int) $parsed_url['isnew'];
				} else {					
					$hls_response = wp_remote_get( $hls_src );

					if ( 200 == wp_remote_retrieve_response_code( $hls_response ) ) {
						$has_hls = 1;
					}

					if ( $post_id > 0 && 'default' == $type ) {
						update_post_meta( $post_id, 'mp4', aiovg_sanitize_url( add_query_arg( 'isnew', $has_hls, $mp4_src ) ) );
					}
				}

				if ( $has_hls ) {
					$hls_source = array(
						'hls' => array(
							'type' => 'application/x-mpegurl',
							'src'  => $hls_src
						)
					);

					$sources = array_merge( $hls_source, $sources );
				}
			}
		}
		
		return $sources;	
	}

	/**
	 * Load the necessary styles in the player header.
	 *
	 * @since 2.4.5
	 * @param array $settings   Player settings.
	 * @param array $attributes Video attributes.
	 * @param array $sources    Video sources.
	 */
	public function iframe_player_styles( $settings, $attributes, $sources ) {
		if ( isset( $sources['hls'] ) || isset( $sources['dash'] ) ) {
			if ( in_array( 'qualitySelector', $settings['controlBar']['children'] ) ) {
				printf(
					'<link rel="stylesheet" href="%spremium/vendor/videojs-plugins/videojs-quality-menu/videojs-quality-menu.css?v=1.4.0" />',
					AIOVG_PLUGIN_URL
				);
			}
		}
	}

	/**
	 * Load the necessary scripts in the player footer.
	 *
	 * @since 2.0.0
	 * @param array $settings   Player settings.
	 * @param array $attributes Video attributes.
	 * @param array $sources    Video sources.
	 */
	public function iframe_player_scripts( $settings, $attributes, $sources ) {
		if ( isset( $sources['hls'] ) || isset( $sources['dash'] ) ) {
			printf(
				'<script src="%spremium/vendor/videojs-plugins/http-streaming/videojs-http-streaming.min.js?v=2.14.2" type="text/javascript"></script>',
				AIOVG_PLUGIN_URL
			);

			if ( in_array( 'qualitySelector', $settings['controlBar']['children'] ) ) {
				?>
				<script src="<?php echo AIOVG_PLUGIN_URL; ?>premium/vendor/videojs-plugins/videojs-quality-menu/videojs-quality-menu.min.js?v=1.4.0" type="text/javascript"></script>
				<script type="text/javascript">
					// Listen to the player initialized event
					window.addEventListener( 'player.init', function( evt ) {
						// Register the quality selection plugin
						evt.detail.player.qualityMenu();
					}, false );
				</script>
				<?php
			}
		}
	}

}
