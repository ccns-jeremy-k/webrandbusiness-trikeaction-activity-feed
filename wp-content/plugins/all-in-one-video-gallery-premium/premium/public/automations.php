<?php

/**
 * Automations.
 *
 * @link       https://plugins360.com
 * @since      1.6.2
 *
 * @package    All_In_One_Video_Gallery
 * @subpackage All_In_One_Video_Gallery/premium
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AIOVG_Premium_Public_Automations class.
 *
 * @since 1.6.2
 */
class AIOVG_Premium_Public_Automations {

	/**
	 * The class instance.
	 *
	 * @since  1.6.2
	 * @access private
	 * @var    object|AIOVG_Premium_Public_Automations
	 */
	private static $instance;

	/**
	 * Get an instance of this class.
	 *
	 * @since  1.6.2
	 * @return object|AIOVG_Premium_Public_Automations
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new AIOVG_Premium_Public_Automations();
		}

		return self::$instance;
	}

	/**
	 * Add a custom cron schedule for every 5 minutes.
	 *
	 * @since  2.4.1
	 * @param  array $schedules An array of non-default cron schedules.
	 * @return array $schedules Filtered array of non-default cron schedules.
	 */
	public function cron_schedules( $schedules ) {
		$schedules[ 'every-5-minutes' ] = array( 'interval' => 5 * MINUTE_IN_SECONDS, 'display' => __( 'Every 5 minutes', 'all-in-one-video-gallery' ) );
		return $schedules;
	}

	/**
	 * Schedule an action if it's not already scheduled.
	 *
	 * @since 1.6.2
	 */
	public function schedule_events() {
		if ( wp_next_scheduled( 'aiovg_hourly_scheduled_events' ) ) {
			wp_clear_scheduled_hook( 'aiovg_hourly_scheduled_events' );
		}

		if ( ! wp_next_scheduled( 'aiovg_schedule_every_five_minutes' ) ) {
			wp_schedule_event( time(), 'every-5-minutes', 'aiovg_schedule_every_five_minutes' );
		}
	}

	/**
	 * Called every 5 minutes. Run the auto import script.
	 *
	 * @since 2.4.1
	 */
	public function cron_event() {
		// Define the query
		$args = array(				
			'post_type' => 'aiovg_automations',			
			'post_status' => 'publish',
			'posts_per_page' => 1,			
			'meta_query' => array(
				array(
					'key' => 'import_next_schedule',
					'value'	=> date( 'Y-m-d H:i:s' ),
					'compare' => '<',
					'type' => 'DATETIME'
				)
			),
			'meta_key' => 'import_next_schedule',			
			'meta_type' => 'DATETIME',
			'orderby' => 'meta_value_datetime',
			'order' => 'ASC',
			'fields' => 'ids',
			'no_found_rows' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		);

		$aiovg_query = new WP_Query( $args );

		if ( $aiovg_query->have_posts() ) {
			$posts = $aiovg_query->posts;

			foreach ( $posts as $post_id ) {
				delete_post_meta( $post_id, 'import_next_schedule' );	
				$this->import( $post_id );
			}
		}
	}

	/**
	 * Test import videos.
	 * 
	 * @since  1.6.2
   	 * @param  array $params Array of query params.
     * @return mixed
     */
	public function test( $params ) {	
		$params = $this->get_params( $params );
		$params['filter_duplicates'] = 0;
		
		$response = $this->youtube( $params );				
		return $response;
	}

	/**
	 * Import videos.
	 * 
	 * @since  1.6.2
   	 * @param  int   $post_id Automations Post ID.
     */
	public function import( $post_id ) {
		set_time_limit( 1200 );

		$params = $this->get_params( $post_id );
		$response = $this->youtube( $params );

		if ( $response->success ) {
			// Pre import
			wp_defer_term_counting( true );
			wp_defer_comment_counting( true );

			if ( ! empty( $params['is_fast_mode'] ) ) {
				$actions = array( 'transition_post_status', 'save_post', 'pre_post_update', 'add_attachment', 'edit_attachment', 'edit_post', 'post_updated', 'wp_insert_post', 'save_post_aiovg_videos' );
				
				foreach ( $actions as $action ) {
					remove_all_actions( $action );
				}
			}

			// Insert video posts
			if ( $response->statistics['imported'] > 0 ) {
				$videos_inserted = 0;
				
				foreach ( $response->videos as $video ) {
					$args = array(
						'post_type'      => 'aiovg_videos',
						'post_title'     => wp_strip_all_tags( $video->title ),
						'post_author'    => $params['video_author'],
						'post_status'    => $params['video_status'],
						'ping_status'    => 'closed',
						'comment_status' => $params['comment_status']						
					);

					if ( ! empty( $params['video_description'] ) ) {
						$args['post_content'] = $video->description;	
					}

					if ( 'original' == $params['video_date'] ) {
						$args['post_date'] = $video->date;	
					}

					$video_id = wp_insert_post( $args );
	
					// Insert post meta
					if ( ! is_wp_error( $video_id ) ) {
						if ( ! empty( $params['video_categories'] ) ) {
							wp_set_object_terms( $video_id, array_map( 'intval', $params['video_categories'] ), 'aiovg_categories' );
						}	

						if ( ! empty( $params['video_tags'] ) ) {
							wp_set_object_terms( $video_id, array_map( 'intval', $params['video_tags'] ), 'aiovg_tags' );
						}
	
						$meta = array(
							'type'       => sanitize_text_field( $params['service'] ),
							'image'      => esc_url_raw( $video->image ),
							'image_id'   => 0,
							'duration'   => sanitize_text_field( $video->duration ),
							'featured'   => 0,
							'views'      => 0,
							'import_id'  => (int) $post_id,
							'import_key' => sanitize_text_field( $response->statistics['key'] )
						);

						if ( 'youtube' == $params['service'] ) {
							$meta['youtube'] = esc_url_raw( $video->url );
						}

						$this->add_post_meta_bulk( $video_id, $meta );

						++$videos_inserted;
					}
				}
				
				// If not all videos are inserted
				if ( $videos_inserted != $response->statistics['imported'] ) {
					$response->statistics['imported'] = $videos_inserted;
				}
			}

			// Import status
			update_post_meta( $post_id, 'import_status', sanitize_text_field( $response->status ) );

			// API Params
			update_post_meta( $post_id, 'import_api_params', array_map( 'sanitize_text_field', wp_unslash( $response->params ) ) );

			// Staistics
			$params['import_statistics']['last_error'] = $response->last_error;

			if ( 'scheduled' == $response->status ) {
				$params['import_statistics']['data'][] = $response->statistics;				
			} else {
				if ( $response->statistics['imported'] > 0 ) {
					$params['import_statistics']['data'][] = $response->statistics;
				}
			}	

			update_post_meta( $post_id, 'import_statistics', $this->sanitize_statistics( $params['import_statistics'] ) );

			// Schedule
			if ( ! empty( $params['schedule'] ) ) {
				if ( 'completed' == $response->status ) {
					delete_post_meta( $post_id, 'import_next_schedule' );
				} else {
					if ( 'rescheduled' == $response->status && ! empty( $response->params['pageToken'] ) ) {
						$params['schedule'] = 5 * 60;
					}

					$next_schedule = date( 'Y-m-d H:i:s', strtotime( '+' . (int) $params['schedule'] . ' seconds' ) );
					update_post_meta( $post_id, 'import_next_schedule', $next_schedule );
				}
			}			
			
			// Notify Admin
			if ( $response->statistics['imported'] > 0 ) {
				if ( 'draft' == $params['video_status'] || 'pending' == $params['video_status'] ) {
					aiovg_premium_notify_admin_videos_imported( $post_id, $response->statistics );
				}
			}

			// Post Import
			wp_defer_term_counting( false );
			wp_defer_comment_counting( false );
		} else {
			// Staistics
			$params['import_statistics']['last_error'] = $response->last_error;
			update_post_meta( $post_id, 'import_statistics', $this->sanitize_statistics( $params['import_statistics'] ) );
		}
	}	

	/**
	 * Import YouTube videos.
	 * 
	 * @since  1.6.2
	 * @access private
   	 * @param  array   $params Array of query params.
     * @return mixed
     */
	private function youtube( $params ) {
		require_once AIOVG_PLUGIN_DIR . 'premium/includes/youtube.php';

		// Vars
		$insert_keys = array();
		foreach ( $params['import_statistics']['data'] as $data ) {
			$insert_keys[] = $data['key'];
		}

		$exclude = array();
		foreach ( $params['exclude'] as $url ) {
			$exclude[] = aiovg_get_youtube_id_from_url( $url );
		}

		$limit = (int) $params['limit'];
		$per_request = 50;
		$iterations = ceil( $limit / $per_request );		

		$args = array(
			'api_key'        => sanitize_text_field( $params['api_key'] ),
			'type'           => sanitize_text_field( $params['type'] ),
			'playlistId'     => isset( $params['import_api_params']['playlistId'] ) ? sanitize_text_field( $params['import_api_params']['playlistId'] ) : '',
			'pageToken'      => isset( $params['import_api_params']['pageToken'] ) ? sanitize_text_field( $params['import_api_params']['pageToken'] ) : '',
			'publishedAfter' => isset( $params['import_api_params']['publishedAfter'] ) ? sanitize_text_field( $params['import_api_params']['publishedAfter'] ) : ''
		);

		switch ( $args['type'] ) {
			case 'search':
			case 'username':
			case 'videos':
				$args['src'] = sanitize_text_field( $params['src'] );

				if ( 'search' == $args['type'] ) {
					$args['order'] = sanitize_text_field( $params['order'] );
				}
				break;
			case 'playlist':
			case 'channel':
				$args['src'] = esc_url_raw( $params['src'] );
				break;
		}		

		// Request API
		$response = new stdClass();
		$response->success = 0;
		$response->videos = array();
		$response->status = $params['import_status'];
		$response->params = $params['import_api_params'];
		$response->statistics = array(
			'key'        => '',
			'date'       => date( 'Y-m-d H:i:s' ),
			'imported'   => 0,
			'excluded'   => 0,
			'duplicates' => 0
		);	
		$response->last_error = '';

		for ( $i = 0; $i < $iterations; $i++ ) {
			if ( $i == $iterations - 1 ) { // Last iteration
				$args['maxResults'] = $limit - ( $i * $per_request );
			} else {
				$args['maxResults'] = $per_request;
			}

			$youtube = new AIOVG_Premium_YouTube();		
			$data = $youtube->query( $args );

			if ( ! isset( $data->error ) ) {
				$response->success = 1;

				$bypass = false;

				$update_statistics = 1;
				if ( 'completed' == $params['import_status'] || 'rescheduled' == $params['import_status'] ) {
					$update_statistics = 0;
				}

				// Set Params				
				$args = array_merge( $args, array_map( 'sanitize_text_field', wp_unslash( $data->params ) ) );
				$response->params = $data->params;

				// Set Videos
				$videos = array();

				foreach ( $data->videos as $video ) {
					if ( 'playlist' !== $params['type'] ) {
						if ( 'completed' == $params['import_status'] || 'rescheduled' == $params['import_status'] ) {
							if ( in_array( $video->id, $insert_keys ) ) {
								$bypass = true;
								break;
							}
						}
					}

					// Set the first video id as key
					if ( '' == $response->statistics['key'] ) {
						$response->statistics['key'] = $video->id;
					}

					// Check in the excluded list
					if ( in_array( $video->id, $exclude ) ) {
						if ( $update_statistics ) {
							++$response->statistics['excluded'];
						}

						continue;
					}

					// Check if the video post already exists
					if ( $this->is_video_exists( $video->id, $params ) ) {
						if ( $update_statistics ) {
							++$response->statistics['duplicates'];
						}
						
						continue;
					}

					// OK to import
					$datetime = new DateTime( $video->date );
			 		$video->date = date_format( $datetime, 'Y-m-d H:i:s' );
			 
					$videos[] = $video;

					++$response->statistics['imported'];
				}				

				if ( ! empty( $videos ) ) {
					$response->videos = array_merge( $response->videos, $videos );					
				} elseif ( 'playlist' !== $params['type'] ) {
					if ( 'completed' == $params['import_status'] || 'rescheduled' == $params['import_status'] ) {
						$bypass = true;				
					}
				}			

				// Bypass the loop
				if ( empty( $args['pageToken'] ) ) {
					$bypass = true;
				}

				if ( $bypass ) {
					$response->params['pageToken'] = '';
					break;
				}
			} else {
				$response->last_error = $data->error_message;
				break;
			}
		}

		// Import status
		if ( $response->success ) {
			if ( empty( $response->status ) ) {
				$response->status = 'scheduled';
			}

			if ( empty( $response->params['pageToken'] ) ) {
				$response->status = 'completed';
			}
			
			if ( 'completed' == $response->status || 'rescheduled' == $response->status ) {
				$response->status = ! empty( $params['reschedule'] ) ? 'rescheduled' : 'completed';
			}
		}

		return $response;
	}

	/**
	 * Get query params.
	 * 
	 * @since  1.6.2
	 * @access private
	 * @param  int|array $input Automations Post ID or query params.
     * @return array            Array of query params.
     */
	private function get_params( $input ) {
		$video_settings       = get_option( 'aiovg_video_settings' );
		$automations_settings = get_option( 'aiovg_automations_settings' );

		// Params
		$params = array(
			'api_key'           => '',
			'is_fast_mode'      => $automations_settings['is_fast_mode'],
			'service'           => 'youtube',
			'type'              => 'playlist',			
			'src'               => '',
			'exclude'           => array(),
			'filter_duplicates' => 1,
			'order'             => 'relevance',
			'limit'             => 50,
			'schedule'          => 0,
			'reschedule'        => 0,
			'video_categories'  => array(),
			'video_tags'        => array(),
			'video_description' => 0,
			'video_date'        => 'original',
			'video_author'      => get_current_user_id(),
			'video_status'      => 'publish',
			'comment_status'    => ( (int) $video_settings['has_comments'] > 0 ) ? 'open' : 'closed',
			'import_status'     => '',
			'import_api_params' => array(),			
			'import_statistics' => array( 
				'last_error' => '',
				'data'       => array()
			)
		);
		
		if ( is_array( $input ) ) {
			foreach ( $params as $key => $value ) {
				if ( isset( $input[ $key ] ) && ! empty( $input[ $key ] ) ) {
					$params[ $key ] = $input[ $key ];
				}
			}
		} else {
			$post_meta = get_post_meta( $input );

			foreach ( $params as $key => $value ) {
				if ( isset( $post_meta[ $key ] ) ) {
					if ( ! empty( $post_meta[ $key ][0] ) ) {
						$params[ $key ] = maybe_unserialize( $post_meta[ $key ][0] );
					}
				} else {
					if ( 'video_description' == $key ) {
						$params['video_description'] = 1;
					}
				}
			}

			$type = $params['type'];
			$params['src'] = isset( $post_meta[ $type ] ) ? $post_meta[ $type ][0] : '';
		}

		// API Key
		if ( empty( $params['api_key'] ) ) {
			$service = $params['service'];
			$params['api_key'] = ! empty( $automations_settings[ $service . '_api_key' ] ) ? $automations_settings[ $service . '_api_key' ] : '';
		}		

		// Exclude
		if ( ! empty( $params['exclude'] ) ) {
			$exclude = str_replace( array( "\n", "\n\r", ' ' ), ',', $params['exclude'] );
			$exclude = explode( ',', $exclude );
			$params['exclude'] = array_filter( $exclude );
		}

		// Reschedule
		if ( empty( $params['schedule'] ) ) {
			$params['reschedule'] = 0;
		}

		if ( 'videos' == $params['type'] ) {
			$params['reschedule'] = 0;
		}

		if ( 'youtube' == $service ) {
			if ( 'search' == $params['type'] && 'date' != $params['order'] ) {
				$params['reschedule'] = 0;
			}

			if ( 'playlist' == $params['type'] && 'rescheduled' == $params['import_status'] ) {
				$params['limit'] = max( 250, (int) $params['limit'] );
			}
		}

		return $params;
	}

	/**
	 * Check if the video already exists.
	 * 
	 * @since  1.6.2
	 * @access private
	 * @param  string  $video_id Video ID.
	 * @param  array   $params   Array of query params.
     * @return bool              True if exists, false if not.
     */
	private function is_video_exists( $video_id, $params ) {
		if ( 0 == $params['filter_duplicates'] ) {
			return false;
		}

		$service = sanitize_text_field( $params['service'] );

		$args = array(				
			'post_type' => 'aiovg_videos',			
			'post_status' => 'any',
			'posts_per_page' => 1,
			'fields' => 'ids',
			'no_found_rows' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'type',
					'value'	=> $service,
					'compare' => '='
				),
				array(
					'key' => $service,
					'value'	=> sanitize_text_field( $video_id ),
					'compare' => 'LIKE'
				)
			)
		);

		$aiovg_query = new WP_Query( $args );

		if ( $aiovg_query->have_posts() ) {
			return true;
		}

		return false;
	}	

	/**
	 * Insert multiple post meta at once.
	 * 
	 * @since  1.6.2
	 * @access private
	 * @param  int     $post_id Post ID.
	 * @param  array   $data    Post meta keys and values.
     */
	private function add_post_meta_bulk( $post_id, $data ) {
		global $wpdb;

		$meta_table = _get_meta_table( 'post' );
		$values = array();
		
		foreach ( $data as $key => $value ) {					
			$values[] = '(' . $post_id . ',"' . $key . '",\'' . maybe_serialize( $value ) . '\')';						
		}
		
		$wpdb->query( "INSERT INTO $meta_table (`post_id`, `meta_key`, `meta_value`) VALUES " . implode( ',', $values ) );
	}

	/**
	 * Sanitize statistics array.
	 * 
	 * @since  1.6.2
	 * @access private
	 * @param  array   $statistics Raw statistics data.
     * @return array   $statistics Cleaned statistics data.
     */
	private function sanitize_statistics( $statistics ) {
		if ( ! empty( $statistics ) ) {
			$statistics['last_error'] = wp_kses_post( $statistics['last_error'] );

			if ( ! empty( $statistics['data'] ) ) {
				$arr = array();

				foreach ( $statistics['data'] as $data ) {
					$arr[] = array(
						'key'        => sanitize_text_field( $data['key'] ),
						'date'       => sanitize_text_field( $data['date'] ),
						'imported'   => (int) $data['imported'],
						'excluded'   => (int) $data['excluded'],
						'duplicates' => (int) $data['duplicates']
					); 
				}

				$statistics['data'] = $arr;
			}
		}
		
		return $statistics;
	}

	/**
	 * Update the "Exclude URLs" list.
	 *
	 * @since 2.4.1
	 * @param int   $post_id Post ID.
	 */
	public function before_delete_post( $post_id ) {	
		if ( defined( 'AIOVG_UNINSTALL_PLUGIN' ) ) {
			return;	
		}

		if ( 'aiovg_videos' != get_post_type( $post_id ) ) {
			return;
		}

		$type = get_post_meta( $post_id, 'type', true );

		if ( 'youtube' == $type ) {
			$import_id = (int) get_post_meta( $post_id, 'import_id', true );

			if ( ! empty( $import_id ) ) {
				$excluded_video = get_post_meta( $post_id, 'youtube', true );
				$excluded_urls  = get_post_meta( $import_id, 'exclude', true );				

				if ( ! empty( $excluded_urls ) ) {
					$excluded_urls = str_replace( array( "\n", "\n\r", ' ' ), ',', $excluded_urls );
					$excluded_urls = explode( ',', $excluded_urls );

					$excluded_urls[] = $excluded_video;

					$excluded_urls = array_filter( $excluded_urls );
					$excluded_urls = array_unique( $excluded_urls );
					$excluded_urls = array_map( 'esc_url_raw', $excluded_urls );

					$excluded_urls = implode( "\n", $excluded_urls );
				} else {
					$excluded_urls = esc_url_raw( $excluded_video );
				}

				update_post_meta( $import_id, 'exclude', $excluded_urls );
			}
		}
	}

}
