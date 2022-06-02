<?php

/**
 * Ads.
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
 * AIOVG_Premium_Public_Ads class.
 *
 * @since 1.5.7
 */
class AIOVG_Premium_Public_Ads {

	/**
	 * Get things started.
	 *
	 * @since 1.5.7
	 */
	public function __construct() {		
		// Register shortcode(s)
		add_shortcode( "companion", array( $this, "display_companion_ads" ) );		
	}

	/**
	 * Filters the player settings.
	 * 
	 * @since  2.4.0
	 * @param  array $settings Player settings.
	 * @param  array $params   Player params.
	 * @return array $settings Filtered player settings.
	 */
	public function player_settings( $settings, $params ) {
		$ads_settings = get_option( 'aiovg_ads_settings' );

		// Check if the ads enabled
		$enabled = true;

		if ( isset( $params['ads'] ) ) {
			if ( 0 == (int) $params['ads'] ) {
				$enabled = false;
			}				
		} else {		
			if ( 'aiovg_videos' == $params['post_type'] ) {					
				$disable_ads = get_post_meta( $params['post_id'], 'disable_ads', true );
				if ( ! empty( $disable_ads ) ) {
					$enabled = false;
				}						
			}			
		}

		if ( ! $enabled ) {
			return $settings;
		}

		// Get VAST URL
		$vast_url = '';

		if ( isset( $params['vast_url'] ) ) {
			$vast_url = $params['vast_url'];				
		} else {		
			if ( 'aiovg_videos' == $params['post_type'] ) {	
				$override_vast_url = get_post_meta( $params['post_id'], 'override_vast_url', true );
				if ( $override_vast_url ) {
					$vast_url = get_post_meta( $params['post_id'], 'vast_url', true );
				}					
			}			
		}

		if ( empty( $vast_url ) ) {					
			$vast_url = $ads_settings['vast_url'];

			if ( empty( $vast_url ) ) {
				return $settings;
			}
		}		

		// ...
		$settings['site_url']     = esc_url( home_url() );
		$settings['post_title']   = $params['post_id'] > 0 ? sanitize_text_field( get_the_title( $params['post_id'] ) ) : '';
		$settings['post_excerpt'] = $params['post_id'] > 0 ? sanitize_text_field( aiovg_get_excerpt( $params['post_id'], 160, '' ) ) : '';
		$settings['ip_address']   = aiovg_get_ip_address();
		$settings['vast_url']     = esc_url_raw( $vast_url );
		$settings['vpaid_mode']   = sanitize_text_field( $ads_settings['vpaid_mode'] );
		$settings['companion']    = ( 0 == $ads_settings['use_gpt'] ? true : false );
		
		return $settings;		
	}

	/**
	 * Enqueue necessary scripts.
	 *
	 * @since 2.4.0
	 * @param array $params Player params.
	 */
	public function player_scripts( $params ) {
		if ( ! isset( $params['settings']['vast_url'] ) ) {
			return;
		}

		// Enqueue styles
		wp_enqueue_style( 
			AIOVG_PLUGIN_SLUG . '-contrib-ads', 
			AIOVG_PLUGIN_URL . 'premium/vendor/videojs-plugins/contrib-ads/videojs-contrib-ads.css', 
			array(), 
			'6.9.0',
			'all' 
		);

		wp_enqueue_style( 
			AIOVG_PLUGIN_SLUG . '-ima', 
			AIOVG_PLUGIN_URL . 'premium/vendor/videojs-plugins/ima/videojs.ima.css', 
			array(), 
			'2.0.0', 
			'all' 
		);

		// Enqueue scripts
		wp_enqueue_script( 
			AIOVG_PLUGIN_SLUG . '-ima-sdk', 
			'https://imasdk.googleapis.com/js/sdkloader/ima3.js', 
			array(), 
			AIOVG_PLUGIN_VERSION, 
			false 
		);

		wp_enqueue_script( 
			AIOVG_PLUGIN_SLUG . '-contrib-ads', 
			AIOVG_PLUGIN_URL . 'premium/vendor/videojs-plugins/contrib-ads/videojs-contrib-ads.min.js', 
			array(), 
			'6.9.0', 
			false 
		);

		wp_enqueue_script( 
			AIOVG_PLUGIN_SLUG . '-ima', 
			AIOVG_PLUGIN_URL . 'premium/vendor/videojs-plugins/ima/videojs.ima.min.js', 
			array(), 
			'2.0.0', 
			false 
		);

		wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-premium-player' );
	}

	/**
	 * Filters the player page URL.
	 * 
	 * @since  1.5.7
	 * @param  string $url     Player page URL.
	 * @param  int    $post_id Post ID.
	 * @param  array  $atts    Array of video attributes.
	 * @return string $url     Modified Player page URL.
	 */
	public function player_page_url( $url, $post_id, $atts = array() ) {
		if ( isset( $atts['ads'] ) && 'false' == $atts['ads'] ) {
			$url = add_query_arg( 'ads', 0, $url );
		}

		if ( isset( $atts['vast_url'] ) ) {
			$url = add_query_arg( 'vast_url', urlencode( $atts['vast_url'] ), $url );
		}
		
		return $url;		
	}

	/**
	 * Load the necessary styles in the player header.
	 *
	 * @since 1.5.7
	 * @param array $settings Player settings array.
	 */
	public function iframe_player_styles( $settings ) {
		if ( ! $this->is_enabled( $settings ) ) {
			return;
		}

		$vast_url = $this->get_vast_url( $settings );
		if ( empty( $vast_url ) ) {
			return;
		}
		?>
		<link rel="stylesheet" href="<?php echo AIOVG_PLUGIN_URL; ?>premium/vendor/videojs-plugins/contrib-ads/videojs-contrib-ads.css?v=6.9.0" />
		<link rel="stylesheet" href="<?php echo AIOVG_PLUGIN_URL; ?>premium/vendor/videojs-plugins/ima/videojs.ima.css?v=2.0.0" />   
        <?php
	}

	/**
	 * Load the necessary scripts in the player footer.
	 *
	 * @since 1.5.7
	 * @param array $settings Player settings array.
	 */
	public function iframe_player_scripts( $settings ) {
		if ( ! $this->is_enabled( $settings ) ) {
			return;
		}		

		$vast_url = $this->get_vast_url( $settings );
		if ( empty( $vast_url ) ) {
			return;
		}	

		$ads_settings = get_option( 'aiovg_ads_settings' );

		$ima_settings = array(
			'siteURL'     => esc_url( home_url() ),
			'postID'      => $settings['aiovg']['postID'],
			'postTitle'   => $settings['aiovg']['postID'] > 0 ? sanitize_text_field( get_the_title( $settings['aiovg']['postID'] ) ) : '',
			'postExcerpt' => $settings['aiovg']['postID'] > 0 ? sanitize_text_field( aiovg_get_excerpt( $settings['aiovg']['postID'], 160, '' ) ) : '',
			'ipAddress'   => aiovg_get_ip_address(),
			'adTagURL'    => esc_url_raw( $vast_url ),
			'vpaidMode'   => sanitize_text_field( $ads_settings['vpaid_mode'] ),
			'companion'   => ( 0 == $ads_settings['use_gpt'] ? true : false )
		);
		?>
        <script src="https://imasdk.googleapis.com/js/sdkloader/ima3.js?v=<?php echo AIOVG_PLUGIN_VERSION; ?>" type="text/javascript"></script>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>premium/vendor/videojs-plugins/contrib-ads/videojs-contrib-ads.min.js?v=6.9.0" type="text/javascript"></script>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>premium/vendor/videojs-plugins/ima/videojs.ima.min.js?v=2.0.0" type="text/javascript"></script>
		<script type="text/javascript">
			var Ads = function( player, playerSettings, adSettings ) {
				this.player = player;
				this.playerSettings = playerSettings;
				this.adSettings = adSettings;				
				this.initialized = false;

				// Remove controls from the player on iPad to stop native controls from stealing
  				// our click
				try {
					var contentPlayer = document.getElementById( 'player_html5_api' );
					if ( ( navigator.userAgent.match( /iPad/i ) || navigator.userAgent.match( /Android/i ) ) &&	contentPlayer.hasAttribute( 'controls' ) ) {
						contentPlayer.removeAttribute( 'controls' );
					}
				} catch ( err ) {
					// console.log( err );
				}

				// Start ads when the video player is clicked, but only the first time it's
				// clicked.				
				this.startEvent = 'click';
				if ( navigator.userAgent.match( /iPhone/i ) ||	navigator.userAgent.match( /iPad/i ) ||	navigator.userAgent.match( /Android/i ) ) {
					this.startEvent = 'touchend';
				}				

				// ...
				var options = {
					id: 'player',
					adTagUrl: this.getAdTagURL(),
    				adsManagerLoadedCallback: this.adsManagerLoadedCallback.bind( this )
  				};

				switch ( this.adSettings.vpaidMode ) {
					case 'enabled':
						options.vpaidMode = google.ima.ImaSdkSettings.VpaidMode.ENABLED;
						break;
					case 'insecure':
						options.vpaidMode = google.ima.ImaSdkSettings.VpaidMode.INSECURE;
						break;
					case 'disabled':
						options.vpaidMode = google.ima.ImaSdkSettings.VpaidMode.DISABLED;
						break;
				}	

  				this.player.ima( options );

				this.wrapperDiv = document.getElementById( 'player' );
				this.boundInit = this.init.bind( this );
  				this.wrapperDiv.addEventListener( this.startEvent, this.boundInit );
				this.player.one( 'play', this.boundInit );
			};		

			Ads.prototype.init = function() {
				if ( this.initialized ) {
					return;
				}

				this.initialized = true;
				this.player.ima.initializeAdDisplayContainer();
				this.wrapperDiv.removeEventListener( this.startEvent, this.boundInit );
			};

			Ads.prototype.adsManagerLoadedCallback = function() {
				var events = [
					google.ima.AdEvent.Type.ALL_ADS_COMPLETED,
					google.ima.AdEvent.Type.CLICK,
					google.ima.AdEvent.Type.COMPLETE,
					google.ima.AdEvent.Type.CONTENT_RESUME_REQUESTED,
					google.ima.AdEvent.Type.FIRST_QUARTILE,
					google.ima.AdEvent.Type.LOADED,
					google.ima.AdEvent.Type.MIDPOINT,
					google.ima.AdEvent.Type.PAUSED,
					google.ima.AdEvent.Type.RESUMED,
					google.ima.AdEvent.Type.STARTED,
					google.ima.AdEvent.Type.THIRD_QUARTILE
				];

				for ( var index = 0; index < events.length; index++ ) {
					this.player.ima.addEventListener(
						events[ index ],
						this.onAdEvent.bind( this ) );
				}
			};

			Ads.prototype.onAdEvent = function( event ) {
				switch ( event.type ) {
					case google.ima.AdEvent.Type.STARTED:
						// Companion ads
						if ( this.adSettings.companion ) {
							var ad = event.getAd();
							var elements = [];
			
							try {
								elements = window.top.aiovgGetCompanionElements();
							} catch ( error ) { }
							
							if ( elements.length ) {		
								var selectionCriteria = new google.ima.CompanionAdSelectionSettings();
								selectionCriteria.resourceType = google.ima.CompanionAdSelectionSettings.ResourceType.ALL;
								selectionCriteria.creativeType = google.ima.CompanionAdSelectionSettings.CreativeType.ALL;
								selectionCriteria.sizeCriteria = google.ima.CompanionAdSelectionSettings.SizeCriteria.SELECT_NEAR_MATCH;        
								
								for ( var i = 0; i < elements.length; i++ ) {													
									var id = elements[ i ].id;
									var width = elements[ i ].width;
									var height = elements[ i ].height;
									
									try {
										// Get a list of companion ads for an ad slot size and CompanionAdSelectionSettings
										var companionAds = ad.getCompanionAds( width, height, selectionCriteria );
										var companionAd = companionAds[0];
									
										// Get HTML content from the companion ad.
										var content = companionAd.getContent();
								
										// Write the content to the companion ad slot.
										var div = window.top.document.getElementById( id );
										div.innerHTML = content;
									} catch ( adError ) { }				
								};		
							}
						}
						break;
					case google.ima.AdEvent.Type.CONTENT_RESUME_REQUESTED:
						if ( ! this.player.ended() && this.player.paused() ) {
							this.player.play();
						}					
						break;
				}				
			};

			Ads.prototype.getAdTagURL = function() {
				var url = this.adSettings.adTagURL;

				url = url.replace( '[domain]', encodeURIComponent( this.adSettings.siteURL ) );
				url = url.replace( '[player_width]', this.player.currentWidth() );
				url = url.replace( '[player_height]', this.player.currentHeight() );
				url = url.replace( '[random_number]', Date.now() );
				url = url.replace( '[timestamp]', Date.now() );
				url = url.replace( '[page_url]', encodeURIComponent( window.top.location ) );
				url = url.replace( '[referrer]', encodeURIComponent( document.referrer ) );
				url = url.replace( '[ip_address]', this.adSettings.ipAddress );
				url = url.replace( '[post_id]', this.adSettings.postID );
				url = url.replace( '[post_title]', encodeURIComponent( this.adSettings.postTitle ) );
				url = url.replace( '[post_excerpt]', encodeURIComponent( this.adSettings.postExcerpt ) );
				url = url.replace( '[video_file]', encodeURIComponent( this.player.currentSrc() ) );
				url = url.replace( '[video_duration]', this.player.duration() || '' );
				url = url.replace( '[autoplay]', this.playerSettings.autoplay );

				return url;
			};

			// Initialize Ads
			function onPlayerInitialized( player ) {
				var ads = new Ads( player, settings, <?php echo json_encode( $ima_settings ); ?> );	
			}			
		</script>
        <?php		
	}	

	/**
	 * Output the shortcode [companion].
	 *
	 * @since 1.5.7
	 * @param array $atts An associative array of attributes.
	 */
	public function display_companion_ads( $atts ) {
		$ads_settings = get_option( 'aiovg_ads_settings' );		

		$attributes = shortcode_atts( array(
			'id'           => aiovg_get_uniqid(),
			'width'        => '',
			'height'       => '',
			'ad_unit_path' => ''
		), $atts );
		
		$content = '';
		
		if ( ! empty( $attributes['width'] ) && ! empty( $attributes['height'] ) ) {		
			if ( ! empty( $ads_settings['use_gpt'] ) ) {
				wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-gpt-proxy' );
			}
			
			ob_start();
			include AIOVG_PLUGIN_DIR . 'premium/public/templates/companion.php';
			return ob_get_clean();			
		}
		
		return $content;	
	}

	/**
	 * Load the GPT library if necessary.
	 *
	 * @since 1.5.7
	 */
	public function wp_print_scripts() {	
		$ads_settings = get_option( 'aiovg_ads_settings' );

		if ( 0 == $ads_settings['use_gpt'] ) return;
		?>
        <script type='text/javascript'>
       		var googletag = googletag || {};
       		googletag.cmd = googletag.cmd || [];
       		(function() {
         		var gads = document.createElement( 'script' );
         		gads.async = true;
         		gads.type = 'text/javascript';
         		gads.src = '//www.googletagservices.com/tag/js/gpt.js';
         		var node = document.getElementsByTagName( 'script' )[0];
         		node.parentNode.insertBefore( gads, node );
       		})();
     	</script>
        <?php
	}

	/**
	 * Load the necessary scripts in the site footer.
	 *
	 * @since 1.5.7
	 */
	public function wp_print_footer_scripts() {	
		$ads_settings = get_option( 'aiovg_ads_settings' );

		if ( 0 == $ads_settings['use_gpt'] ) :
		?>
        <script type='text/javascript'>
			(function( $ ) {
				'use strict';
				
				window.aiovgGetCompanionElements = function() {					
					var elements = [];
					
					jQuery( '.aiovg-companion' ).each(function() {										
						elements.push({
							id: jQuery( this ).attr( 'id' ),				
							width: parseInt( jQuery( this ).data( 'width' ) ),
							height: parseInt( jQuery( this ).data( 'height' ) )
						});							
					});
					
					return elements;					
				}				
			})( jQuery );
		</script>
        <?php
		endif;		
	}

	/**
	 * Check if the ads enabled.
	 *
	 * @since  2.0.0
	 * @access private
	 * @param  array   $settings Player settings array.
	 * @return bool    $enabled  True if enabled, false if not.
	 */
	private function is_enabled( $settings ) {
		$enabled = true;

		if ( isset( $_GET['ads'] ) ) {
			if ( 0 == (int) $_GET['ads'] ) {
				$enabled = false;
			}				
		} else {		
			if ( 'aiovg_videos' == $settings['aiovg']['postType'] ) {					
				$disable_ads = get_post_meta( $settings['aiovg']['postID'], 'disable_ads', true );
				if ( ! empty( $disable_ads ) ) {
					$enabled = false;
				}						
			}			
		}
		
		return $enabled;		
	}

	/**
	 * Get VAST URL.
	 *
	 * @since  2.0.0
	 * @access private
	 * @param  array   $settings Player settings array.
	 * @return bool    $vast_url VAST URL.
	 */
	private function get_vast_url( $settings ) {
		$vast_url = '';

		if ( isset( $_GET['vast_url'] ) ) {
			$vast_url = $_GET['vast_url'];				
		} else {		
			if ( 'aiovg_videos' == $settings['aiovg']['postType'] ) {	
				$override_vast_url = get_post_meta( $settings['aiovg']['postID'], 'override_vast_url', true );
				if ( $override_vast_url ) {
					$vast_url = get_post_meta( $settings['aiovg']['postID'], 'vast_url', true );
				}					
			}			
		}

		if ( empty( $vast_url ) ) {
			$ads_settings = get_option( 'aiovg_ads_settings' );
			$vast_url = $ads_settings['vast_url'];
		}

		return $vast_url;
	}

}
