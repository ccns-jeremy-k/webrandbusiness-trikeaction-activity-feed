(function( $ ) {
	'use strict';

	/**
	 * Resolve relative file paths as absolute URLs.
	 *
	 * @since  2.4.0
	 * @param  {string} url Input file URL.
 	 * @return {string} url Absolute file URL.
	 */
	function aiovg_resolve_url( url ) {
		if ( ! url ) {
			return url;
		}

		if ( url.indexOf( '://' ) > 0 || url.indexOf( '//' ) === 0 ) {
			return url;
		}
		
		if ( url.indexOf( '/' ) === 0 ) {
			url = aiovg_admin.site_url + url;
		} else {
			url = aiovg_admin.site_url + '/' + url;
		}

		return url;
	}

	/**
	 * Called when the page has loaded.
	 *
	 * @since 1.5.7
	 */
	$(function() {
		// Vars
		var mp4_file_url = aiovg_resolve_url( $( '#aiovg-mp4' ).val() );

		// Dashboard: Toggle fields based on the selected slider layout
		$( 'select[name=slider_layout]', '#aiovg-shortcode-form-videos' ).on( 'change', function() {			
			var layout = $( this ).val();
			
			$( '#aiovg-shortcode-form-videos' ).removeClass(function( index, classes ) {
				var matches = classes.match( /\aiovg-slider-layout-\S+/ig );
				return ( matches ) ? matches.join(' ') : '';	
			}).addClass( 'aiovg-slider-layout-' + layout );
		}).trigger( 'change' );

		// Settings: Toggle fields based on the selected slider layout
		$( 'tr.slider_layout', '#aiovg-videos-settings' ).find( 'select' ).on( 'change', function() {			
			var layout = $( this ).val();
			
			$( '#aiovg-videos-settings' ).removeClass(function( index, classes ) {
				var matches = classes.match( /\aiovg-slider-layout-\S+/ig );
				return ( matches ) ? matches.join(' ') : '';	
			}).addClass( 'aiovg-slider-layout-' + layout );
		}).trigger( 'change' );
		
		// Videos Widget: Toggle fields based on the selected slider layout
		$( document ).on( 'change', '.aiovg-widget-form-videos .aiovg-widget-input-slider_layout', function() {			
			var layout = $( this ).val();
			
			$( this ).closest( '.aiovg-widget-form-videos' ).removeClass(function( index, classes ) {
				var matches = classes.match( /\aiovg-slider-layout-\S+/ig );
				return ( matches ) ? matches.join(' ') : '';	
			}).addClass( 'aiovg-slider-layout-' + layout );
		});

		// Gutenberg: Toggle fields based on the selected videos template
		if ( 'undefined' !== typeof wp && 'undefined' !== typeof wp['hooks'] ) {
			// Toggle Controls
			wp.hooks.addFilter( 'aiovg_block_toggle_controls', 'aiovg/videos', function( value, control, attributes ) {
				switch ( control ) {
					case 'columns':
						if ( 'slider' == attributes.template && 'player' == attributes.slider_layout ) {
							value = false;
						}
						break;
					case 'link_title':
					case 'show_player_title':
					case 'show_player_description':
						if ( 'classic' == attributes.template ) {
							value = false;
						} else {
							if ( 'slider' == attributes.template && 'thumbnails' == attributes.slider_layout ) {
								value = false;
							} else {
								value = true;
							}
						}
						break;
					case 'show_pagination':
						value = ( 'slider' == attributes.template ) ? false : true;
						break;
					case 'slider_layout':
					case 'arrows':
					case 'arrow_size':
					case 'arrow_bg_color':
					case 'arrow_icon_color':
					case 'arrow_radius':
					case 'arrow_top_offset':
					case 'arrow_left_offset':
					case 'arrow_right_offset':
					case 'dots':
					case 'dot_size':
					case 'dot_color':
						value = ( 'slider' == attributes.template ) ? true : false;
						break;
					case 'slider_autoplay':
					case 'autoplay_speed':
						value = ( 'slider' == attributes.template && 'both' != attributes.slider_layout ) ? true : false;
						break;
				}

				return value;
			});
		}

		// Thumbnail Generator: Test FFMPEG
		$( '#aiovg-test-ffmpeg-button' ).on( 'click', function() {
			var data = {
				'action': 'aiovg_ffmpeg_status',				
				'ffmpeg_path': $( '#aiovg-ffmpeg-path' ).val(),
				'security': aiovg_admin.ajax_nonce
			};

			if ( '' == data.ffmpeg_path ) {
				return;
			}

			$( '#aiovg-ffmpeg-status' ).html( '<span class="spinner"></span>' );
		
			$.post( 
				ajaxurl, 
				data, 
				function( response ) {
					if ( 'success' == response.status ) {
						$( '#aiovg-ffmpeg-status' ).html( '<span class="aiovg-text-success">' + response.message + '</span>' );
					} else {
						$( '#aiovg-ffmpeg-status' ).html( '<span class="aiovg-text-error">' + response.message + '</span>' );
					}
				}, 
				'json' 
			);
		});

		// Thumbnail Generator: Capture Image
		if ( $( '#aiovg-html5-thumbnail-generator' ).length ) {
			// Initialize "Capture Image" Popup
			var thumbnail_generator_modal_body = document.getElementById( 'aiovg-thumbnail-generator-modal-body' ).innerHTML;

			$( '#aiovg-html5-thumbnail-generator' ).magnificPopup({
				items: {
					src: '#aiovg-thumbnail-generator-modal',
					type: 'inline'				
				},
				callbacks: {
					open: function() {
						document.getElementById( 'aiovg-thumbnail-generator-modal-body' ).innerHTML = thumbnail_generator_modal_body;
						mp4_file_url = aiovg_resolve_url( document.getElementById( 'aiovg-mp4' ).value );

						if ( mp4_file_url ) {
							var video_elem     = document.getElementById( 'aiovg-thumbnail-generator-player' );
							var canvas_elem    = document.getElementById( 'aiovg-thumbnail-generator-canvas' );
							var canvas_ctx     = canvas_elem.getContext( '2d' );
							var select_seekto  = document.getElementById( 'aiovg-thumbnail-generator-seekto' );
							var capture_button = document.getElementById( 'aiovg-thumbnail-generator-button' );

							// Set the video source
							video_elem.getElementsByTagName( 'source' )[0].setAttribute( 'src', mp4_file_url );

							// Load the video and show it
							video_elem.load();

							// On video playback failed
							video_elem.addEventListener( 'error', function() {
								$( '#aiovg-thumbnail-generator-modal-body' ).prepend( '<p class="aiovg-notice aiovg-notice-error">' + aiovg_premium_admin.i18n.thumbnail_generator_cors_error + '</p>' );
							}, true	);

							// Load metadata of the video to get video duration and dimensions
							video_elem.addEventListener( 'loadedmetadata', function() {
								var video_duration = video_elem.duration,
									duration_options_html = '';

								// Set canvas dimensions same as video dimensions
								canvas_elem.width  = video_elem.videoWidth;
								canvas_elem.height = video_elem.videoHeight;

								// Set options in dropdown at 4 seconds interval
								for ( var i = 0; i < Math.floor( video_duration ); i = i + 4 ) {
									duration_options_html += '<option value="' + i + '">' + i + '</option>';
								}
								
								select_seekto.innerHTML = duration_options_html;
								
								// Enable the dropdown and the "Capture This Scene" button
								select_seekto.disabled  = false;
								capture_button.disabled = false;

								// On changing the duration dropdown, seek the video to that duration
								select_seekto.addEventListener( 'change', function() {
									video_elem.currentTime = $( this ).val();
									
									// Seeking might take a few milliseconds, so disable the dropdown and the "Capture This Scene" button 
									select_seekto.disabled  = true;
									capture_button.disabled = true;
								});

								// On seeking video to the specified duration is complete 
								video_elem.addEventListener( 'timeupdate', function() {									
									// Re-enable the dropdown and the "Capture This Scene" button
									select_seekto.disabled  = false;
									capture_button.disabled = false;
								});

								// On clicking the "Capture This Scene" button, set the video in the canvas and download the base-64 encoded image data
								var capture_button_clicked = false;

								capture_button.addEventListener( 'click', function() {	
									if ( capture_button_clicked ) {
										return;
									}

									capture_button_clicked = true;
									
									var button_label = capture_button.innerHTML;
									capture_button.innerHTML = '<div class="spinner"></div>';
									
									canvas_ctx.drawImage( video_elem, 0, 0, video_elem.videoWidth, video_elem.videoHeight );
									var canvas_data_url = '';

									try {
										canvas_data_url = canvas_elem.toDataURL();
									} catch ( error ) {
										capture_button.innerHTML = button_label;

										select_seekto.disabled  = true;
										capture_button.disabled = true;

										$( '#aiovg-thumbnail-generator-modal-body' ).prepend( '<p class="aiovg-notice aiovg-notice-error">' + aiovg_premium_admin.i18n.thumbnail_generator_cors_error + '</p>' );
										return;
									}

									var data = {
										'action': 'aiovg_upload_base64_image',
										'image_data': canvas_data_url,
										'video': mp4_file_url,
										'index': $( '.aiovg-item-thumbnail', '#aiovg-thumbnail-generator' ).length,
										'security': aiovg_admin.ajax_nonce
									};
									
									$.post( 
										ajaxurl, 
										data, 
										function( response ) {
											capture_button.innerHTML = button_label;

											if ( '' != response ) {
												$( response ).insertBefore( '#aiovg-html5-thumbnail-generator' );

												$( '#aiovg-thumbnail-generator' )
													.find( 'input[type=radio]')
													.last()
													.prop( 'checked', true )
													.trigger( 'change' );
											}					
									
											if ( $( '.aiovg-item-thumbnail', '#aiovg-thumbnail-generator' ).length > 0 ) {
												$( '#aiovg-thumbnail-generator' )
													.find( '.aiovg-header' )
													.html( aiovg_premium_admin.i18n.thumbnail_generator_select_image );

												$( '#aiovg-thumbnail-generator' )
													.find( '.aiovg-footer' )
													.show();
											} else {
												$( '#aiovg-thumbnail-generator' )
													.find( '.aiovg-header' )
													.html( aiovg_premium_admin.i18n.thumbnail_generator_capture_image );

												$( '#aiovg-thumbnail-generator' )
													.find( '.aiovg-footer' )
													.hide();
											};

											$.magnificPopup.close();
										}
									);
								});
							});
						} else {
							// Set error
							$( '#aiovg-thumbnail-generator-modal-body' ).prepend( '<p class="aiovg-notice aiovg-notice-error">' + aiovg_premium_admin.i18n.thumbnail_generator_video_not_found + '</p>' );
						}
					},
					close: function() {
						document.getElementById( 'aiovg-thumbnail-generator-modal-body' ).innerHTML = '';
					}
				}
			});			
		}		

		// Thumbnail Generator: Generate images using FFMPEG
		$( '#aiovg-mp4' ).on( 'blur file.uploaded', function() {
			if ( ! aiovg_premium_admin.ffmpeg_enabled ) {
				return;
			}

			var __mp4_file_url = aiovg_resolve_url( $( this ).val() );

			if ( ! __mp4_file_url || mp4_file_url == __mp4_file_url ) {
				return;
			}

			mp4_file_url = __mp4_file_url;

			$( '#aiovg-thumbnail-generator' )
				.addClass( 'aiovg-processing' )
				.find( '.aiovg-header' )
				.html( '<span class="spinner"></span> <span>' + aiovg_premium_admin.i18n.thumbnail_generator_processing + '</span>' );

			var data = {
				'action': 'aiovg_ffmpeg_generate_images',
				'url': mp4_file_url,
				'security': aiovg_admin.ajax_nonce
			};
			
			$.post( 
				ajaxurl, 
				data, 
				function( response ) {
					$( '#aiovg-thumbnail-generator' )
						.removeClass( 'aiovg-processing' )
						.find( '.aiovg-header' )
						.html( '' );	

					$( '#aiovg-thumbnail-generator' )
						.find( '.aiovg-item-thumbnail' )
						.remove();

					if ( '' != response ) {
						if ( $( '#aiovg-html5-thumbnail-generator' ).length ) {
							$( response ).insertBefore( '#aiovg-html5-thumbnail-generator' );
						} else {
							$( '#aiovg-thumbnail-generator' )
								.find( '.aiovg-body' )
								.html( response );
						}		

						if ( '' == $( '#aiovg-image' ).val() ) {
							$( '#aiovg-thumbnail-generator' )
								.find( 'input[type=radio]' )
								.first()
								.prop( 'checked', true )
								.trigger( 'change' );
						}						
					}					
			
					if ( $( '.aiovg-item-thumbnail', '#aiovg-thumbnail-generator' ).length > 0 ) {
						$( '#aiovg-thumbnail-generator' )
							.find( '.aiovg-header' )
							.html( aiovg_premium_admin.i18n.thumbnail_generator_select_image );

						$( '#aiovg-thumbnail-generator' )
							.find( '.aiovg-footer' )
							.show();
					} else {
						$( '#aiovg-thumbnail-generator' )
							.find( '.aiovg-header' )
							.html( '<span class="aiovg-text-error">' + aiovg_premium_admin.i18n.ffmpeg_thumbnail_generation_failed + '</span> <span>' + aiovg_premium_admin.i18n.thumbnail_generator_capture_image + '</span>' );

						$( '#aiovg-thumbnail-generator' )
							.find( '.aiovg-footer' )
							.hide();
					};					
				}
			);			
		});
		
		// Thumbnail Generator: On an image selected
		$( '#aiovg-thumbnail-generator' ).on( 'click', '.aiovg-item-thumbnail', function() {
			$( this )
				.find( 'input[type=radio]' )
				.prop( 'checked', true )
				.trigger( 'change' );
		});

		// Thumbnail Generator: Bind selected image URL in the "Image" field
		$( '#aiovg-thumbnail-generator' ).on( 'change', 'input[type=radio]', function() {			
			var image = $( 'input[type=radio]:checked', '#aiovg-thumbnail-generator' ).val();
			$( '#aiovg-image' ).val( image );
		});

		// Automations: Toggle fields based on the selected source type
		$( 'input[name=type]', '#aiovg-automations-sources' ).on( 'change', function( e ) { 
            e.preventDefault();
 
 			var type  = $( 'input[name=type]:checked', '#aiovg-automations-sources' ).val();
			var $elem = $( '#aiovg-automations-sources' ).find( '.aiovg-table' );

			$elem.removeClass(function( index, classes ) {
				var matches = classes.match( /\aiovg-automations-type-\S+/ig );
				return ( matches ) ? matches.join( ' ' ) : '';
			});

			$elem.addClass( 'aiovg-automations-type-' + type );
		});

		// Automations: Test run
		$( document ).on( 'click', '.aiovg-automations-preview', function( e ) {
			e.preventDefault();
			
			$( '.aiovg-modal-body', '#aiovg-automations-preview-modal' ).html( aiovg_premium_admin.i18n.loading_api_data );

			var data = {
				'action': 'aiovg_automations_test_run',				
				'api_key': ( $( '#aiovg-api-key' ).length ? $( '#aiovg-api-key' ).val() : '' ),
				'service': $( '#aiovg-service' ).val(),
				'type': ( $( '#aiovg-type' ).length ? $( '#aiovg-type' ).val() : $( 'input[name=type]:checked' ).val() ),
				'exclude': $( '#aiovg-exclude' ).val(),
				'order': $( '#aiovg-order' ).val(),
				'limit': $( '#aiovg-limit' ).val(),
				'video_date': $( '#aiovg-video_date' ).val(),
				'security': aiovg_admin.ajax_nonce
			};

			data.src = $( '#aiovg-' + data.type ).val();
			
			$.post( 
				ajaxurl, 
				data, 
				function( response ) {
					$( '.aiovg-modal-body', '#aiovg-automations-preview-modal' ).html( response );
				}
			);
		});
		
		// Ads: Toggle custom VAST URL field
		$( '#override_vast_url' ).on( 'change', function() {			
			if ( this.checked ) {
				$( '#vast_url' ).show();
			} else {
				$( '#vast_url' ).hide();
			}			
		}).trigger( 'change' );
	});	

})( jQuery );
