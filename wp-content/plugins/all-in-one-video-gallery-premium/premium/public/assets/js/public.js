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
			url = aiovg_premium.site_url + url;
		} else {
			url = aiovg_premium.site_url + '/' + url;
		}

		return url;
	}

	/**
	 * Set field error.
	 *
	 * @since 1.6.1
	 * @param string name    Field name.
	 * @param string message Error message.
	 */
	function aiovg_premium_set_field_error( name, message ) {
		$( '#aiovg-field-' + name )
			.addClass( 'aiovg-field-invalid' )
			.find( '.aiovg-field-error' )
			.html( '<span>' + message + '</span>' );
	}

	/**
	 * Clear field error.
	 *
	 * @since 1.6.1
	 * @param string name Field name.
	 */
	function aiovg_premium_clear_field_error( name ) {
		$( '#aiovg-field-' + name )
			.removeClass( 'aiovg-field-invalid' )
			.find( '.aiovg-field-error' )
			.html( '' );
	}

	/**
	 * Reset file uploader.
	 *
	 * @since 1.6.1
	 * @param string name Field name.
	 */
	function aiovg_premium_reset_uploader( name ) {
		$( '#aiovg-field-' + name )
			.find( '.aiovg-media-uploader' )
			.removeClass( 'aiovg-uploading' )
			.find( '.aiovg-upload-progress' )
			.html( '' );
	}	

	/**
	 * jQuery Plugin: aiovg_premium_video_form_validate
	 *
	 * @since 1.6.1
	 */
	$.fn.aiovg_premium_video_form_validate = function() {
		// Private vars
		var root = this,
			form = {},
			is_valid = true;

		// Private methods
		var validate = function( name, event ) {
			var can_validate  = false;
			var is_required   = false;
			var pattern       = '';
			var error_message = '';
	
			switch ( name ) {
				case 'title':
					can_validate = true;
					is_required  = true;
					break;
				case 'mp4':
					if ( 'default' == aiovg_premium.video_form.type.value ) {
						can_validate = true;
						is_required  = true;
						pattern      = /mp4|webm|ogv|m4v|mov/;
					}
					break;
				case 'webm':
				case 'ogv':
					if ( 'default' == aiovg_premium.video_form.type.value ) {
						can_validate = true;
						pattern      = new RegExp( name );
					}
					break;
				case 'youtube':
					if ( name == aiovg_premium.video_form.type.value ) {
						can_validate = true;
						is_required  = true;
						pattern      = new RegExp( 'youtube.com|youtu.be' );
					}
					break;
				case 'vimeo':
				case 'dailymotion':
				case 'facebook':
					if ( name == aiovg_premium.video_form.type.value ) {
						can_validate = true;
						is_required  = true;
						pattern      = new RegExp( name + '.com' );
					}
					break;
				case 'adaptive':
					if ( name == aiovg_premium.video_form.type.value ) {
						can_validate = true;
						is_required  = true;
						pattern      = /mpd|m3u8/;
					}				
					break;
				case 'image':
					can_validate = true;
					pattern      = /jpg|jpeg|png|gif/;
					break;
				case 'tos':
					can_validate = true;
					is_required  = true;
					break;
			}	
			
			if ( can_validate ) {
				if ( is_required && '' == form[ name ].value ) {
					error_message = aiovg_premium.i18n.required;
				} else if ( '' != form[ name ].value ) {
					if ( 'input' != event && '' != pattern ) {
						if ( ! pattern.test( form[ name ].value.toLowerCase() ) ) {
							error_message = aiovg_premium.i18n.invalid;
						}
					}							
				}

				if ( '' != error_message ) {
					is_valid = false;

					if ( form[ name ].error != error_message ) {
						form[ name ].error = error_message;
						aiovg_premium_set_field_error( name, error_message );					
					}
				} else {
					if ( '' != form[ name ].error ) {
						form[ name ].error = '';	
						aiovg_premium_clear_field_error( name );	
					}
				}
			}
		}

		// Public methods
    	this.initialize = function() {			
			// On field edit
			$( '.aiovg-field-validate', root ).each(function( e ) {
				var name = $( this ).attr( 'name' );
	
				form[ name ] = {
					value: '',
					error: ''
				}
	
				// Validate
				if ( 'tos' == name ) {
					$( '#aiovg-' + name ).on( 'change', function( e ) {
						form[ name ].value = $( this ).is( ':checked' ) ? 1 : '';
						validate( name, 'change' );
					});
				} else {
					$( '#aiovg-' + name ).on( 'input', function( e ) {
						form[ name ].value = $( this ).val();
						validate( name, 'input' );
					}).on( 'blur', function( e ) {
						form[ name ].value = $( this ).val();
						validate( name, 'blur' );
					});
				}				
			});	

			// On form submit
			root.on( 'submit', function( e ) {
				// Check for pending file uploads
				if ( root.hasClass( 'aiovg-uploading' ) ) {
					e.preventDefault();
					
					alert( aiovg_premium.i18n.pending_upload );
					return;
				}

				is_valid = true;

				// Validate fields
				$( '.aiovg-field-validate', root ).each(function() {
					var name = $( this ).attr( 'name' );

					if ( 'tos' == name ) {
						form[ name ].value = $( this ).is( ':checked' ) ? 1 : '';
					} else {
						form[ name ].value = $( this ).val();
					}

					validate( name, 'submit' );
				});
				
				// Prevent form submission if invalid
				if ( ! is_valid ) {
					e.preventDefault();
	
					$( 'html, body' ).animate({
						scrollTop: ( $( '.aiovg-field-invalid', root ).first().offset().top - 50 )
					}, 300);
				}			
			});

			// ...
			return this;			
		};		
		
		// ...
		return this.initialize();		
	}

	/**
	 * Initialize the popup.
	 *
	 * @since 2.5.1
	 */
	 function aiovg_init_popup( $this ) {
		var params = $this.data( 'params' );

		$this.find( '.aiovg-responsive-container' ).addClass( 'aiovg-disable-mouse-events' );

		if ( ! params.link_title ) {
			$this.find( '.aiovg-link-title' ).addClass( 'aiovg-disable-mouse-events' );
		}

		$this.magnificPopup({
			delegate: '.aiovg-popup-item',
			type: 'iframe',
			iframe: {
				markup: '<div class="mfp-iframe-scaler" style="padding-top: ' + params.player_ratio + ';">' +
					'<div class="mfp-close"></div>' +
					'<iframe class="mfp-iframe" frameborder="0" scrolling="no" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>' +								
				'</div>' + 
				'<div class="mfp-bottom-bar aiovg-popup-content">' + 
					'<h2 class="mfp-title"></h2>' + 
					'<div class="mfp-description"></div>' +
				'</div>',
			},
			callbacks: { // To assign title, description								
				markupParse: function( template, values, item ) {
					if ( params.show_title ) {							
						values.title = item.el.find( '.aiovg-hidden-title' ).html();
					}					
					
					values.description = item.el.find( '.aiovg-hidden-description' ).html();				
				}																			
			},
			gallery: {
			  enabled: true
			}
		});	
	}

	/**
	 * Initialize the slick slider.
	 *
	 * @since 1.5.7
	 */
	function aiovg_init_slick( $this ) {
		$this.addClass( 'aiovg-slick-initialized' );

		var params = $this.data( 'params' );			
		var arrow_styles = 'top: ' + params.arrow_top_offset + '; width: ' + params.arrow_size  + '; height: ' + params.arrow_size + '; background: ' + params.arrow_bg_color + '; border-radius: ' + params.arrow_radius + '; font-size: ' + params.arrow_icon_size + '; color: ' + params.arrow_icon_color + '; line-height: ' + params.arrow_size + ';';
		
		// Slick			
		$this.slick({
			rtl: params.is_rtl,
			prevArrow: '<div class="aiovg-slick-prev" style="left: ' + params.arrow_left_offset + '; ' + arrow_styles + '" role="button">&#10094;</div>',
			nextArrow: '<div class="aiovg-slick-next" style="right: ' + params.arrow_right_offset + '; ' + arrow_styles + '" role="button">&#10095;</div>',
			dotsClass: 'aiovg-slick-dots',
			adaptiveHeight: true,
			customPaging: function( slider, i ) {					
				return '<div class="aiovg-slick-dot" style="color: ' + params.dot_color + '; font-size: ' + params.dot_size + '" role="button">&#9679;</div>';
			}
		});
	}	

	/**
	 * Called when the page has loaded.
	 *
	 * @since 1.5.7
	 */
	$(function() {
		// Vars
		aiovg_premium.video_form = {
			type: {
				value: '',
				error: ''
			}
		}

		var mp4_file_url = aiovg_resolve_url( $( '#aiovg-mp4' ).val() ),
			pause_slider = 0;

		// Video Form: Insert the Magic Field
		if ( aiovg_premium.magic_field ) {
			// Post via AJAX			 
			var data = {
				'action': 'aiovg_public_get_magic_field',
				'security': aiovg_premium.ajax_nonce
			};
	
			$.post( aiovg_premium.ajax_url, data, function( response ) {
				if ( response ) {
					$( '#aiovg-form-video' ).append( response );
				};
			});
		}

		// Video Form: Toggle fields based on the selected video source type
		$( '#aiovg-video-type' ).on( 'change', function( e ) { 
      		e.preventDefault();
 
			aiovg_premium.video_form.type.value = $( this ).val();
			
			$( '.aiovg-toggle-fields' ).hide();
			$( '.aiovg-type-' + aiovg_premium.video_form.type.value ).css( 'display', 'flex' );
		}).trigger( 'change' );

		// Video Form: Initialize the File Uploader
		$( '.aiovg-button-upload', '#aiovg-form-video' ).on( 'click', function( e ) { 
			e.preventDefault();
			
			var format = $( this ).data( 'format' );
			$( '#aiovg-upload-format' ).val( format );

			$( '#aiovg-upload-media' ).trigger( 'click' ); 
		});

		// Video Form: Upload Files	
		$( "#aiovg-upload-media" ).change(function() {			
			var selected = $( this )[0].files.length;
			if ( ! selected ) {
				return false;	
			}		
		
			var format = $( '#aiovg-upload-format' ).val();

			aiovg_premium_clear_field_error( format );

			var $progress = $( '#aiovg-field-' + format )
				.find( '.aiovg-media-uploader' )
				.addClass( 'aiovg-uploading' )
				.find( '.aiovg-upload-progress' )
				.html( '<div class="aiovg-spinner"></div>' );
						
			var options = {				
				url: aiovg_premium.ajax_url,
				type: 'POST',
				dataType: 'json',
				data: {
					security: aiovg_premium.ajax_nonce
				},
				uploadProgress: function( event, position, total, percentComplete ) {
					if ( 100 == percentComplete ) {
						$progress.html( aiovg_premium.i18n.processing );						
					} else {
						$progress.html( aiovg_premium.i18n.loaded + ' ' + Math.min( percentComplete, 99 ) + '%' );
					}					
				},
        		success: function( json, statusText, xhr, $form ) {
					aiovg_premium_reset_uploader( format );	
					
					if ( true == json.success ) {
						$( '#aiovg-' + format )
							.val( json.data.url )
							.trigger( 'file.uploaded' );
					} else {
						aiovg_premium_set_field_error( format, aiovg_premium.i18n.unknown_error );
					}					
				},
				error: function( data ) {
					aiovg_premium_reset_uploader( format );
					aiovg_premium_set_field_error( format, data.responseText );
				}
    		}; 

    		// Submit form using 'ajaxSubmit' 
			var form = $( '#aiovg-form-upload' ).ajaxSubmit( options );	
			var xhr  = form.data('jqxhr');	
			
			// Abort Upload
			$( '#aiovg-field-' + format )
				.find ( '.aiovg-upload-cancel' )
				.off( 'click' )
				.on( 'click', function( e ) {
					e.preventDefault();

					xhr.abort();
					aiovg_premium_reset_uploader( format );
				});
		});

		// Video Form: Validate
		$( '#aiovg-form-video' ).aiovg_premium_video_form_validate();
		
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
							var video_elem  = document.getElementById( 'aiovg-thumbnail-generator-player' );
							var canvas_elem = document.getElementById( 'aiovg-thumbnail-generator-canvas' );
							var canvas_ctx  = canvas_elem.getContext( '2d' );
							var select_seekto  = document.getElementById( 'aiovg-thumbnail-generator-seekto' );
							var capture_button = document.getElementById( 'aiovg-thumbnail-generator-button' );

							// Set the video source
							video_elem.getElementsByTagName( 'source' )[0].setAttribute( 'src', mp4_file_url );

							// Load the video and show it
							video_elem.load();

							// On video playback failed
							video_elem.addEventListener( 'error', function() {
								$( '#aiovg-thumbnail-generator-modal-body' ).prepend( '<p class="aiovg-notice aiovg-notice-error">' + aiovg_premium.i18n.thumbnail_generator_cors_error + '</p>' );
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
									capture_button.innerHTML = '<div class="aiovg-spinner"></div>';		
									
									canvas_ctx.drawImage( video_elem, 0, 0, video_elem.videoWidth, video_elem.videoHeight );
									var canvas_data_url = '';

									try {
										canvas_data_url = canvas_elem.toDataURL();
									} catch ( error ) {
										capture_button.innerHTML = button_label;

										select_seekto.disabled  = true;
										capture_button.disabled = true;

										$( '#aiovg-thumbnail-generator-modal-body' ).prepend( '<p class="aiovg-notice aiovg-notice-error">' + aiovg_premium.i18n.thumbnail_generator_cors_error + '</p>' );
										return;
									}

									var data = {
										'action': 'aiovg_upload_base64_image',
										'image_data': canvas_data_url,
										'video': mp4_file_url,
										'index': $( '.aiovg-item-thumbnail', '#aiovg-thumbnail-generator' ).length,
										'security': aiovg_premium.ajax_nonce
									};
									
									$.post( 
										aiovg_premium.ajax_url, 
										data, 
										function( response ) {											
											capture_button.innerHTML = button_label;
											
											if ( '' != response ) {
												$( response ).insertBefore( '#aiovg-html5-thumbnail-generator' );

												$( '#aiovg-thumbnail-generator' )
													.find( 'input[type=radio]' )
													.last()
													.prop( 'checked', true )
													.trigger( 'change' );
											}					
									
											if ( $( '.aiovg-item-thumbnail', '#aiovg-thumbnail-generator' ).length > 0 ) {
												$( '#aiovg-thumbnail-generator' )
													.find( '.aiovg-header' )
													.html( aiovg_premium.i18n.thumbnail_generator_select_image );

												$( '#aiovg-thumbnail-generator' )
													.find( '.aiovg-footer' )
													.show();
											} else {
												$( '#aiovg-thumbnail-generator' )
													.find( '.aiovg-header' )
													.html( aiovg_premium.i18n.thumbnail_generator_capture_image );

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
							$( '#aiovg-thumbnail-generator-modal-body' ).prepend( '<p class="aiovg-notice aiovg-notice-error">' + aiovg_premium.i18n.thumbnail_generator_video_not_found + '</p>' );
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
			if ( ! aiovg_premium.ffmpeg_enabled ) {
				return;
			}

			var __mp4_file_url = aiovg_resolve_url( $( this ).val() );

			if ( ! __mp4_file_url || mp4_file_url == __mp4_file_url ) {
				return;
			}

			mp4_file_url = __mp4_file_url;

			$( '#aiovg-thumbnail-generator' )
				.show()
				.addClass( 'aiovg-processing' )
				.find( '.aiovg-header' )
				.html( '<span class="aiovg-spinner"></span> <span>' + aiovg_premium.i18n.thumbnail_generator_processing + '</span>' );

			var data = {
				'action': 'aiovg_ffmpeg_generate_images',
				'url': mp4_file_url,
				'security': aiovg_premium.ajax_nonce
			};
			
			$.post( 
				aiovg_premium.ajax_url, 
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
						if ( aiovg_premium.html5_thumbnail_generator_enabled ) {
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
							.html( aiovg_premium.i18n.thumbnail_generator_select_image );

						$( '#aiovg-thumbnail-generator' )
							.find( '.aiovg-footer' )
							.show();
					} else {
						var message = '';
						if ( aiovg_premium.html5_thumbnail_generator_enabled ) {
							message = '<span class="aiovg-field-error">' + aiovg_premium.i18n.ffmpeg_thumbnail_generation_failed + '</span> <span>' + aiovg_premium.i18n.thumbnail_generator_capture_image + '</span>';
						} else {
							message = '<span class="aiovg-field-error">' + aiovg_premium.i18n.ffmpeg_thumbnail_generation_failed + '</span>';
						}

						$( '#aiovg-thumbnail-generator' )
							.find( '.aiovg-header' )
							.html( message );

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
				
		// Popup: Initialize the Popup
		$( '.aiovg-videos-template-popup' ).each(function() {
			aiovg_init_popup( $( this ) );					
		});

		// Popup: Re-initialize the Popup on gallery updated
		$( document ).on( 'AIOVG.onGalleryUpdated', '.aiovg-videos-template-popup', function() {
			aiovg_init_popup( $( this ) );
		});
		
		// Popup: Enable click event on child elements
		$( '.aiovg-videos-template-popup' ).on( 'click', '.aiovg-caption a', function( event ) {
			event.stopPropagation();            
		});
		
		// Slider: Initialize the Slider
		$( '.aiovg-slick' ).each(function() {						
			aiovg_init_slick( $( this ) );
			
			// On before slide change
			var type = $( this ).data( 'type' );

			if ( 'player' == type ) {				
				$( this ).on( 'beforeChange', function( event, slick, current_slide, next_slide ) {													   
					if ( 1 == pause_slider ) {
						throw "pause-slider";
					}
					
					var $current_slide = $( slick.$slides[ current_slide ] );
					$current_slide.find( '.aiovg-player' ).html( '' );
					
					var $next_slide = $( slick.$slides[ next_slide ] );
					var src = $next_slide.find( '.aiovg-player' ).data( 'src' );
					$next_slide.find( '.aiovg-player' ).html( '<iframe src="' + src + '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>' );					
				});				
			}										   
		});			
		
		// Slider: Disable click event on thumbnail element
		$( '.aiovg-slider-layout-both' ).each( function( event ) {
			var params = $( this ).find( '.aiovg-slider-thumbnails' ).data( 'params' );

			$( this ).find( '.aiovg-responsive-container' ).addClass( 'aiovg-disable-mouse-events' );

			if ( ! params.link_title ) {
				$( this ).find( '.aiovg-link-title' ).addClass( 'aiovg-disable-mouse-events' );
			}  			
		});

		// Slider: Toggle Pause
		$( '.aiovg-slider-layout-both' ).on( 'mouseenter', 'a', function( event ) {
			pause_slider = 1;
		}).on( 'mouseleave', 'a', function( event ) {
			pause_slider = 0;
		});

		// Slider: Initialize the Popup
		$( '.aiovg-slider-layout-popup' ).each(function() {
			var $slick = $( this ).find( '.aiovg-slick' );
			var params = $slick.data( 'params' );

			$( this ).find( '.aiovg-responsive-container' ).addClass( 'aiovg-disable-mouse-events' );

			if ( ! params.link_title ) {
				$( this ).find( '.aiovg-link-title' ).addClass( 'aiovg-disable-mouse-events' );
			}

			$( this ).magnificPopup({
				delegate: '.slick-slide',
				type: 'iframe',
				iframe: {
					markup: '<div class="mfp-iframe-scaler" style="padding-top: ' + params.player_ratio + ';">' +
						'<div class="mfp-close"></div>' +
						'<iframe class="mfp-iframe" frameborder="0" scrolling="no" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>' +								
					'</div>' + 
					'<div class="mfp-bottom-bar aiovg-popup-content">' + 
						'<h2 class="mfp-title"></h2>' + 
						'<div class="mfp-description"></div>' +
					'</div>',
				},
				callbacks: { // To assign title, description								
					markupParse: function( template, values, item ) {
						if ( params.show_title ) {							
							values.title = item.el.find( '.aiovg-hidden-title' ).html();
						}					
						
						values.description = item.el.find( '.aiovg-hidden-description' ).html();				
					},
					open: function() {
						if ( params.slider_autoplay ) {
							$slick.slick( 'slickPause' );
						}
					},
					close: function() {
						if ( params.slider_autoplay ) {
							$slick.slick( 'slickPlay' );
						}
					}																			
				},
				gallery: {
				  enabled: true
				}
			});			
		});		

		// Slider: Enable click event on child elements
		$( '.aiovg-slider-layout-both, .aiovg-slider-layout-popup' ).on( 'click', 'a', function( event ) {
			event.stopPropagation();  			
		});

		// Gutenberg: On block init
		if ( 'undefined' !== typeof wp && 'undefined' !== typeof wp['hooks'] ) {
			var aiovg_block_interval;
			var aiovg_block_interval_retry_count;

			wp.hooks.addFilter( 'aiovg_block_init', 'aiovg/videos', function( attributes ) {				
				if ( 'slider' == attributes.template ) {
					if ( aiovg_block_interval_retry_count > 0 ) {
						clearInterval( aiovg_block_interval );
					}

					aiovg_block_interval_retry_count = 0;

					aiovg_block_interval = setInterval(
						function() {
							aiovg_block_interval_retry_count++;
							var $aiovg_sliders = $( '.aiovg-slick:not(.aiovg-slick-initialized)' );

							if ( $aiovg_sliders.length > 0 || aiovg_block_interval_retry_count >= 10 ) {
								clearInterval( aiovg_block_interval );
								aiovg_block_interval_retry_count = 0;

								$aiovg_sliders.each(function() {		
									aiovg_init_slick( $( this ) );
								});
							}
						}, 
						1000
					);
				}
			});
		}
	});
})( jQuery );
