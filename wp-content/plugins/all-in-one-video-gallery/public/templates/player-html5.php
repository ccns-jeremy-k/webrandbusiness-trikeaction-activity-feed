<?php

/**
 * Video Player: Video.js.
 *
 * @link     https://plugins360.com
 * @since    2.0.0
 *
 * @package All_In_One_Video_Gallery
 */

// Video Sources
$sources = array();
$allowed_types = array( 'mp4', 'webm', 'ogv', 'youtube', 'vimeo', 'dailymotion', 'facebook' );

if ( ! empty( $post_meta ) ) {
	$type = $post_meta['type'][0];

	if ( 'default' == $type ) {
		$types = array( 'mp4', 'webm', 'ogv' );

		foreach ( $types as $type ) {
			if ( ! empty( $post_meta[ $type ][0] ) ) {
				$ext   = $type;
				$label = '';

				if ( 'mp4' == $type ) {
					$ext = aiovg_get_file_ext( $post_meta[ $type ][0] );
					if ( ! in_array( $ext, array( 'webm', 'ogv' ) ) ) {
						$ext = 'mp4';
					}

					if ( ! empty( $post_meta['quality_level'][0] ) ) {
						$label = $post_meta['quality_level'][0];
					}
				}

				$sources[ $type ] = array(
					'type'  => "video/{$ext}",
					'src'   => $post_meta[ $type ][0],
					'label' => $label
				);
			}
		}

		if ( ! empty( $post_meta['sources'][0] ) ) {
			$_sources = unserialize( $post_meta['sources'][0] );

			foreach ( $_sources as $source ) {
				if ( ! empty( $source['quality'] ) && ! empty( $source['src'] ) ) {	
					$ext = aiovg_get_file_ext( $source['src'] );
					if ( ! in_array( $ext, array( 'webm', 'ogv' ) ) ) {
						$ext = 'mp4';
					}

					$label = $source['quality'];

					$sources[ $label ] = array(
						'type'  => "video/{$ext}",
						'src'   => $source['src'],
						'label' => $label
					);
				}
			}
		}
	} else {
		if ( in_array( $type, $allowed_types ) && ! empty( $post_meta[ $type ][0] ) ) {
			$src = $post_meta[ $type ][0];

			$sources[ $type ] = array(
				'type' => "video/{$type}",
				'src'  => $src
			);
		}
	}
} else {
	foreach ( $allowed_types as $type ) {		
		if ( isset( $_GET[ $type ] ) && ! empty( $_GET[ $type ] ) ) {
			$src = aiovg_sanitize_url( $_GET[ $type ] );

			$sources[ $type ] = array(
				'type' => "video/{$type}",
				'src'  => $src
			);
		}	
	}
}

$sources = apply_filters( 'aiovg_video_sources', $sources );

// Video Tracks
$tracks_enabled = isset( $_GET['tracks'] ) ? (int) $_GET['tracks'] : isset( $player_settings['controls']['tracks'] );
$tracks = array();

if ( $tracks_enabled && ! empty( $post_meta['track'] ) ) {
	foreach ( $post_meta['track'] as $track ) {
		$tracks[] = unserialize( $track );
	}	
}

$tracks = apply_filters( 'aiovg_video_tracks', $tracks );

// Video Attributes
$attributes = array( 
	'id'          => 'player',
	'style'       => 'width: 100%; height: 100%;',
	'controls'    => '',
	'playsinline' => ''
);

$attributes['preload'] = esc_attr( $player_settings['preload'] );

$loop = isset( $_GET['loop'] ) ? (int) $_GET['loop'] : (int) $player_settings['loop'];
if ( $loop ) {
	$attributes['loop'] = true;
}

if ( isset( $_GET['poster'] ) ) {
	$attributes['poster'] = $_GET['poster'];
} elseif ( ! empty( $post_meta ) ) {
	$attributes['poster'] = aiovg_get_image_url( $post_meta['image_id'][0], 'large', $post_meta['image'][0], 'player' );
}

if ( ! empty( $attributes['poster'] ) ) {
	$attributes['poster'] = aiovg_sanitize_url( aiovg_resolve_url( $attributes['poster'] ) );
} else {
	unset( $attributes['poster'] );
}

$attributes = apply_filters( 'aiovg_video_attributes', $attributes );

// Player Settings
$settings = array(
	'controlBar'    => array(),
	'autoplay'      => isset( $_GET['autoplay'] ) ? (int) $_GET['autoplay'] : (int) $player_settings['autoplay'],
	'muted'         => isset( $_GET['muted'] ) ? (int) $_GET['muted'] : (int) $player_settings['muted'],
	'playbackRates' => array( 0.5, 0.75, 1, 1.5, 2 ),
	'aiovg'         => array(
		'postID'           => $post_id,
		'postType'         => $post_type,		
		'showLogo'         => 0,
		'contextmenuLabel' => ''
	)
);

$controls = array( 
	'playpause'  => 'PlayToggle', 
	'current'    => 'CurrentTimeDisplay', 
	'progress'   => 'progressControl', 
	'duration'   => 'durationDisplay',
	'tracks'     => 'SubtitlesButton',
	'audio'      => 'AudioTrackButton',
	'quality'    => 'qualitySelector',
	'speed'      => 'PlaybackRateMenuButton',  
	'volume'     => 'VolumePanel', 
	'fullscreen' => 'fullscreenToggle'
);

foreach ( $controls as $index => $control ) {
	$enabled = isset( $_GET[ $index ] ) ? (int) $_GET[ $index ] : isset( $player_settings['controls'][ $index ] );

	if ( $enabled && 'tracks' == $index ) {
		$player_settings['controls']['audio'] = 1;
	}

	if ( ! $enabled ) {	
		unset( $controls[ $index ] );	
	}	
}

$settings['controlBar']['children'] = array_values( $controls );
if ( empty( $settings['controlBar']['children'] ) ) {
	$attributes['class'] = 'vjs-no-control-bar';
}

if ( isset( $sources['youtube'] ) ) {
	$settings['techOrder'] = array( 'youtube' );
	$settings['youtube']   = array( 
		'iv_load_policy' => 3 
	);

	parse_str( $sources['youtube']['src'], $queries );

	if ( isset( $queries['start'] ) ) {
		$settings['aiovg']['start'] = (int) $queries['start'];
	}

	if ( isset( $queries['t'] ) ) {
		$settings['aiovg']['start'] = (int) $queries['t'];
	}

	if ( isset( $queries['end'] ) ) {
		$settings['aiovg']['end'] = (int) $queries['end'];
	}
}

if ( isset( $sources['vimeo'] ) ) {
	$settings['techOrder'] = array( 'vimeo2' );

	if ( strpos( $sources['vimeo']['src'], 'player.vimeo.com' ) !== false ) {
		$oembed = aiovg_get_vimeo_oembed_data( $sources['vimeo']['src'] );
        $sources['vimeo']['src'] = 'https://vimeo.com/' . $oembed['video_id'];
	}
}

if ( isset( $sources['dailymotion'] ) ) {
	if ( empty( $attributes['poster'] ) ) {
		$settings['bigPlayButton'] = false;
	}
	
	$settings['techOrder'] = array( 'dailymotion' );
}

if ( isset( $sources['facebook'] ) ) {
	if ( empty( $attributes['poster'] ) ) {
		$settings['bigPlayButton'] = false;
	}
	$settings['autoplay'] = 0;
	$settings['techOrder'] = array( 'facebook' );

	$sources['facebook']['src'] = add_query_arg( 'nocache', rand(), $sources['facebook']['src'] );
}

if ( ! empty( $brand_settings ) ) {
	$settings['aiovg']['showLogo'] = ! empty( $brand_settings['logo_image'] ) ? (int) $brand_settings['show_logo'] : 0;
	$settings['aiovg']['logoImage'] = aiovg_sanitize_url( aiovg_resolve_url( $brand_settings['logo_image'] ) );
	$settings['aiovg']['logoLink'] = esc_url_raw( $brand_settings['logo_link'] );
	$settings['aiovg']['logoPosition'] = sanitize_text_field( $brand_settings['logo_position'] );
	$settings['aiovg']['logoMargin'] = (int) $brand_settings['logo_margin'];
	$settings['aiovg']['contextmenuLabel'] = apply_filters( 'aiovg_translate_strings', sanitize_text_field( $brand_settings['copyright_text'] ), 'copyright_text' );
}

$settings = apply_filters( 'aiovg_video_settings', $settings );
?>
<!DOCTYPE html>
<html translate="no">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">

    <?php if ( $post_id > 0 ) : ?>    
        <title><?php echo wp_kses_post( get_the_title( $post_id ) ); ?></title>    
        <link rel="canonical" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" />
    <?php endif; ?>

	<link rel="stylesheet" href="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/video-js.min.css?v=7.18.1" />

	<?php if ( in_array( 'qualitySelector', $settings['controlBar']['children'] ) && ( isset( $sources['mp4'] ) || isset( $sources['webm'] ) || isset( $sources['ogv'] ) ) ) : ?>
		<link rel="stylesheet" href="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs-plugins/quality-selector/quality-selector.css?v=1.2.5" />
	<?php endif; ?>

	<?php if ( ! empty( $settings['aiovg']['showLogo'] ) ) : ?>
		<link rel="stylesheet" href="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs-plugins/overlay/videojs-overlay.css?v=2.1.5" />
	<?php endif; ?>

	<style type="text/css">
        html, 
        body,
		.video-js {
            width: 100%;
            height: 100%;
            margin: 0; 
            padding: 0; 
            overflow: hidden;
        }

		.vjs-no-control-bar .vjs-control-bar {
			display: none;
		}

		.video-js .vjs-current-time,
		.vjs-no-flex .vjs-current-time,
		.video-js .vjs-duration,
		.vjs-no-flex .vjs-duration {
			display: block;
		}

		.video-js .vjs-subtitles-button .vjs-icon-placeholder:before {
			content: "\f10d";
		}

		.video-js .vjs-menu li.vjs-selected:focus,
		.video-js .vjs-menu li.vjs-selected:hover {
			background-color: #fff;
			color: #2b333f;
		}

		.vjs-youtube-mobile .vjs-poster {
			display: none;
		}

		.video-js .vjs-big-play-button {
			width: 1.5em;
			height: 1.5em;
			top: 50%;
			left: 50%;
			margin-top: 0;
			margin-left: 0;
			background-color: rgba( 0, 0, 0, 0.6 );
			border: none;
			border-radius: 50%;
			font-size: 6em;
			line-height: 1.5em;			
			transform: translateX( -50% ) translateY( -50% );
		}

		.video-js:hover .vjs-big-play-button,
		.video-js .vjs-big-play-button:focus {
			background-color: rgba( 0, 0, 0, 0.7 );
		}

		.vjs-waiting .vjs-big-play-button {
			display: none !important;
		}	

		.vjs-ended .vjs-control-bar,
		.vjs-ended .vjs-text-track-display,
		.vjs-ended .vjs-logo {
			display: none;
		}

		.vjs-ended .vjs-poster,
		.vjs-ended .vjs-big-play-button {
			display: block;
		}

		.vjs-logo {
			opacity: 0;
			cursor: pointer;
		}

		.vjs-has-started .vjs-logo {
			opacity: 0.5;
			transition: opacity 0.1s;
		}

		.vjs-has-started.vjs-user-inactive.vjs-playing .vjs-logo {
			opacity: 0;
			transition: opacity 1s;
		}

		.vjs-has-started .vjs-logo:hover {
			opacity: 1;
		}

		.vjs-logo img {
			max-width: 100%;
		}

		.contextmenu {
            position: absolute;
            top: 0;
            left: 0;
            margin: 0;
            padding: 0;
            background-color: #2B333F;
  			background-color: rgba( 43, 51, 63, 0.7 );
			border-radius: 2px;
            z-index: 9999999999; /* make sure it shows on fullscreen */
        }
        
        .contextmenu-item {
            margin: 0;
            padding: 8px 12px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #FFF;		
            white-space: nowrap;
            cursor: pointer;
        }
    </style>

	<?php do_action( 'aiovg_player_head', $settings, $attributes, $sources, $tracks ); ?>
</head>
<body id="body" class="vjs-waiting">
    <video-js <?php the_aiovg_video_attributes( $attributes ); ?>>
        <?php 
		// Video Sources
		foreach ( $sources as $source ) {
			printf( 
				'<source type="%s" src="%s" label="%s"/>', 
				esc_attr( $source['type'] ), 
				esc_attr( aiovg_resolve_url( $source['src'] ) ),
				( isset( $source['label'] ) ? esc_attr( $source['label'] ) : '' ) 
			);
		}
		
		// Video Tracks
		foreach ( $tracks as $track ) {
        	printf( 
				'<track src="%s" kind="subtitles" srclang="%s" label="%s">', 
				esc_attr( aiovg_resolve_url( $track['src'] ) ), 
				esc_attr( $track['srclang'] ), 
				esc_attr( $track['label'] )
			);
		}
       ?>       
	</video-js>

	<?php if ( ! empty( $settings['aiovg']['contextmenuLabel'] ) ) : ?>
		<div id="contextmenu" class="contextmenu" style="display: none;">
            <div class="contextmenu-item"><?php echo esc_html( $settings['aiovg']['contextmenuLabel'] ); ?></div>
        </div>
	<?php endif; ?>
    
	<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/video.min.js?v=7.18.1" type="text/javascript"></script>

	<?php if ( in_array( 'qualitySelector', $settings['controlBar']['children'] ) && ( isset( $sources['mp4'] ) || isset( $sources['webm'] ) || isset( $sources['ogv'] ) ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs-plugins/quality-selector/silvermine-videojs-quality-selector.min.js?v=1.2.5" type="text/javascript"></script>
	<?php endif; ?>

	<?php if ( isset( $sources['youtube'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs-plugins/youtube/Youtube.min.js?v=2.6.1" type="text/javascript"></script>
	<?php endif; ?>

	<?php if ( isset( $settings['aiovg']['start'] ) || isset( $settings['aiovg']['end'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs-plugins/offset/videojs-offset.min.js?v=2.1.3" type="text/javascript"></script>
	<?php endif; ?>

	<?php if ( isset( $sources['vimeo'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>public/assets/videojs-plugins/vimeo/videojs-vimeo2.min.js?v=1.3.0" type="text/javascript"></script>
	<?php endif; ?>

	<?php if ( isset( $sources['dailymotion'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>public/assets/videojs-plugins/dailymotion/videojs-dailymotion.min.js?v=1.2.0" type="text/javascript"></script>
	<?php endif; ?>

	<?php if ( isset( $sources['facebook'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>public/assets/videojs-plugins/facebook/videojs-facebook.min.js?v=1.4.0" type="text/javascript"></script>
	<?php endif; ?>

	<?php if ( ! empty( $settings['aiovg']['showLogo'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs-plugins/overlay/videojs-overlay.min.js?v=2.1.5" type="text/javascript"></script>
	<?php endif; ?>

	<?php if ( ! empty( $settings['autoplay'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs-plugins/can-autoplay/can-autoplay.min.js?v=3.0.2" type="text/javascript"></script>
    <?php endif; ?>

	<?php do_action( 'aiovg_player_footer', $settings, $attributes, $sources, $tracks ); ?>

    <script type="text/javascript">
		'use strict';			
			
		// Vars
		var settings = <?php echo json_encode( $settings ); ?>;

		settings.html5 = {
			vhs: {
      			overrideNative: !videojs.browser.IS_ANY_SAFARI,
    		}
		};

		/**
		 * Merge attributes.
		 *
		 * @since  2.0.0
		 * @param  {array}  attributes Attributes array.
		 * @return {string} str        Merged attributes string to use in an HTML element.
		 */
		function combineAttributes( attributes ) {
			var str = '';

			for ( var key in attributes ) {
				str += ( key + '="' + attributes[ key ] + '" ' );
			}

			return str;
		}

		/**
		 * Update video views count.
		 *
		 * @since 2.0.0
		 */
		function updateViewsCount() {
			var xmlhttp;

			if ( window.XMLHttpRequest ) {
				xmlhttp = new XMLHttpRequest();
			} else {
				xmlhttp = new ActiveXObject( 'Microsoft.XMLHTTP' );
			};
			
			xmlhttp.onreadystatechange = function() {				
				if ( 4 == xmlhttp.readyState && 200 == xmlhttp.status ) {					
					if ( xmlhttp.responseText ) {
						// Do nothing
					}						
				}					
			};	

			xmlhttp.open( 'GET', '<?php echo admin_url( 'admin-ajax.php' ); ?>?action=aiovg_update_views_count&post_id=<?php echo $post_id; ?>&security=<?php echo wp_create_nonce( "aiovg_video_{$post_id}_views_nonce" ); ?>', true );
			xmlhttp.send();							
		}

		/**
		 * Check unmuted autoplay support.
		 *
		 * @since 2.0.0
		 */
		function checkUnmutedAutoplaySupport() {
			canAutoplay
				.video({ timeout: 100, muted: false })
				.then(function( response ) {
					if ( response.result === false ) {
						// Unmuted autoplay is not allowed
						checkMutedAutoplaySupport();
					} else {
						// Unmuted autoplay is allowed
						settings.autoplay = true;
						initPlayer();
					}
				});
		}

		/**
		 * Check muted autoplay support.
		 *
		 * @since 2.0.0
		 */
		function checkMutedAutoplaySupport() {
			canAutoplay
				.video({ timeout: 100, muted: true })
				.then(function( response ) {
					if ( response.result === false ) {
						// Muted autoplay is not allowed
						settings.autoplay = false;
					} else {
						// Muted autoplay is allowed
						settings.autoplay = true;
						settings.muted = true;
					}
					initPlayer();
				});
		}

		/**
		 * Initialize the player.
		 *
		 * @since 2.0.0
		 */		
		function initPlayer() {
			var player = videojs( 'player', settings );			

			// Maintained for backward compatibility
			if ( typeof window['onPlayerInitialized'] === 'function' ) {
				window.onPlayerInitialized( player );
			}

			// Dispatch an event
			var evt = document.createEvent( 'CustomEvent' );
			evt.initCustomEvent( 'player.init', false, false, { player: player } );
			window.dispatchEvent( evt );

			// On player ready
			player.ready(function() {
				document.getElementById( 'body' ).className = '';
			});

			// Fired the first time a video is played
			player.one( 'play', function() {
				if ( 'aiovg_videos' == settings.aiovg.postType ) {
					updateViewsCount();
				}
			});

			// Offset
			var offset = {};

			if ( settings.aiovg.start ) {
				offset.start = settings.aiovg.start;
			}

			if ( settings.aiovg.end ) {
				offset.end = settings.aiovg.end;
			}
			
			if ( Object.keys( offset ).length > 1 ) {
				offset.restart_beginning = false;
				player.offset( offset );
			}

			// Logo overlay
			if ( settings.aiovg.showLogo ) {
				var attributes = [];
				attributes['src'] = settings.aiovg.logoImage;

				if ( settings.aiovg.logoMargin ) {
					settings.aiovg.logoMargin = settings.aiovg.logoMargin - 5;
				}

				var align;
				switch ( settings.aiovg.logoPosition ) {
					case 'topleft':
						align = 'top-left';
						attributes['style'] = 'margin: ' + settings.aiovg.logoMargin + 'px;';
						break;
					case 'topright':
						align = 'top-right';
						attributes['style'] = 'margin: ' + settings.aiovg.logoMargin + 'px;';
						break;					
					case 'bottomright':
						align = 'bottom-right';
						attributes['style'] = 'margin: ' + settings.aiovg.logoMargin + 'px;';
						break;
					default:						
						align = 'bottom-left';
						attributes['style'] = 'margin: ' + settings.aiovg.logoMargin + 'px;';
						break;					
				}

				if ( settings.aiovg.logoLink ) {
					attributes['onclick'] = "top.window.location.href='" + settings.aiovg.logoLink + "';";
				}

				player.overlay({
					content: '<img ' +  combineAttributes( attributes ) + '/>',
					class: 'vjs-logo',
					align: align,
					showBackground: false					
				});
			}
		}

		if ( settings.autoplay ) {
			checkUnmutedAutoplaySupport();
		} else {
			initPlayer();
		}					

		// Custom contextmenu
		if ( settings.aiovg.contextmenuLabel ) {
			var contextmenu = document.getElementById( 'contextmenu' );
			var timeout_handler = '';
			
			document.addEventListener( 'contextmenu', function( e ) {						
				if ( 3 === e.keyCode || 3 === e.which ) {
					e.preventDefault();
					e.stopPropagation();
					
					var width = contextmenu.offsetWidth,
						height = contextmenu.offsetHeight,
						x = e.pageX,
						y = e.pageY,
						doc = document.documentElement,
						scrollLeft = ( window.pageXOffset || doc.scrollLeft ) - ( doc.clientLeft || 0 ),
						scrollTop = ( window.pageYOffset || doc.scrollTop ) - ( doc.clientTop || 0 ),
						left = x + width > window.innerWidth + scrollLeft ? x - width : x,
						top = y + height > window.innerHeight + scrollTop ? y - height : y;
			
					contextmenu.style.display = '';
					contextmenu.style.left = left + 'px';
					contextmenu.style.top = top + 'px';
					
					clearTimeout( timeout_handler );
					timeout_handler = setTimeout(function() {
						contextmenu.style.display = 'none';
					}, 1500 );				
				}														 
			});
			
			if ( settings.aiovg.logoLink ) {
				contextmenu.addEventListener( 'click', function() {
					top.window.location.href = settings.aiovg.logoLink;
				});
			}
			
			document.addEventListener( 'click', function() {
				contextmenu.style.display = 'none';								 
			});	
		}
    </script>	
</body>
</html>