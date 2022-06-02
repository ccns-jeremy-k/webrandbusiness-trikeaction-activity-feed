(function( $ ) {
	'use strict';

	/**
	 * jQuery Plugin: aiovg_ads
	 *
	 * @since 2.4.0
	 */
	$.fn.aiovg_ads = function( config ) {
		// Vars
		var player      = config.player;
		var settings    = config.settings;
		var container   = null;	
		var start_event = 'click';		
		var initialized = false;

		// Init ads
		var init_ads = function() {
			if ( initialized ) {
				return;
			}

			initialized = true;
			player.ima.initializeAdDisplayContainer();
			container.removeEventListener( start_event, init_ads );
		}

		// On Ads manager loaded
		var ads_manager_loaded_callback = function() {
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
				player.ima.addEventListener( events[ index ], on_ad_event );
			}
		}

		// On Ad event
		var on_ad_event = function( event ) {
			switch ( event.type ) {
				case google.ima.AdEvent.Type.STARTED:
					// Companion ads
					if ( settings.companion ) {
						var ad = event.getAd();					
						var elements = [];

						try {
							elements = window.aiovgGetCompanionElements();
						} catch ( error ) { }
						
						if ( elements.length ) {		
							var selection_criteria = new google.ima.CompanionAdSelectionSettings();
							selection_criteria.resourceType = google.ima.CompanionAdSelectionSettings.ResourceType.ALL;
							selection_criteria.creativeType = google.ima.CompanionAdSelectionSettings.CreativeType.ALL;
							selection_criteria.sizeCriteria = google.ima.CompanionAdSelectionSettings.SizeCriteria.SELECT_NEAR_MATCH;        
							
							for ( var i = 0; i < elements.length; i++ ) {													
								var id     = elements[ i ].id;
								var width  = elements[ i ].width;
								var height = elements[ i ].height;
								
								try {
									// Get a list of companion ads for an ad slot size and CompanionAdSelectionSettings
									var companion_ads = ad.getCompanionAds( width, height, selection_criteria );
									var companion_ad = companion_ads[0];
								
									// Get HTML content from the companion ad.
									var content = companion_ad.getContent();
							
									// Write the content to the companion ad slot.
									var div = document.getElementById( id );
									div.innerHTML = content;
								} catch ( adError ) { }				
							};		
						}
					}
					break;
				case google.ima.AdEvent.Type.CONTENT_RESUME_REQUESTED:
					if ( ! player.ended() && player.paused() ) {
						player.play();
					}					
					break;
			}			
		}

		// Get Vast URL
		var get_vast_url = function() {
			var url = settings.vast_url;

			url = url.replace( '[domain]', encodeURIComponent( settings.site_url ) );
			url = url.replace( '[player_width]', player.currentWidth() );
			url = url.replace( '[player_height]', player.currentHeight() );
			url = url.replace( '[random_number]', Date.now() );
			url = url.replace( '[timestamp]', Date.now() );
			url = url.replace( '[page_url]', encodeURIComponent( window.location ) );
			url = url.replace( '[referrer]', encodeURIComponent( document.referrer ) );
			url = url.replace( '[ip_address]', settings.ip_address );
			url = url.replace( '[post_id]', settings.post_id );
			url = url.replace( '[post_title]', encodeURIComponent( settings.post_title ) );
			url = url.replace( '[post_excerpt]', encodeURIComponent( settings.post_excerpt ) );
			url = url.replace( '[video_file]', encodeURIComponent( player.currentSrc() ) );
			url = url.replace( '[video_duration]', player.duration() || '' );
			url = url.replace( '[autoplay]', settings.autoplay );

			return url;
		};

		// Init
		this.init = function() {
			// Remove controls from the player on iPad to stop native controls from stealing
			// our click
			try {
				var content_player = document.getElementById( 'aiovg-player-' + config.id + '_html5_api' );
				if ( ( navigator.userAgent.match( /iPad/i ) || navigator.userAgent.match( /Android/i ) ) &&	content_player.hasAttribute( 'controls' ) ) {
					content_player.removeAttribute( 'controls' );
				}
			} catch ( err ) {
				// console.log( err );
			}

			// Start ads when the video player is clicked, but only the first time it's
			// clicked.
			if ( navigator.userAgent.match( /iPhone/i ) ||	navigator.userAgent.match( /iPad/i ) ||	navigator.userAgent.match( /Android/i ) ) {
				start_event = 'touchend';
			}

			// ...
			var options = {
				id: 'aiovg-player-' + config.id,
				adTagUrl: get_vast_url(),
				adsManagerLoadedCallback: ads_manager_loaded_callback
			};

			switch ( settings.vpaid_mode ) {
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

			player.ima( options );

			container = document.getElementById( 'aiovg-player-' + config.id );
			container.addEventListener( start_event, init_ads );
			player.one( 'play', init_ads );
		}

		// ...
		return this.init();
	}

	/**
	 * Called when the page has loaded.
	 *
	 * @since 2.4.0
	 */
	$(function() {
		
		// Add premium player features
		$( '.aiovg-player-standard' ).on( 'player.init', function( event, config ) {
			// Init http streaming quality selector
			var src = config.player.src();

			if ( /.m3u8/.test( src ) || /.mpd/.test( src ) ) {
				if ( config.settings.player.controlBar.children.indexOf( 'qualitySelector' ) !== -1 ) {
					config.player.qualityMenu();
				};
			};

			// Init ads
			if ( config.settings.vast_url ) {
				$( this ).aiovg_ads( config );
			}					
		});

	});
	
})( jQuery );
