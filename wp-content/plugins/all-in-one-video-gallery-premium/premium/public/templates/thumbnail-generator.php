<?php

/**
 * Thumbnail Generator.
 *
 * @link       https://plugins360.com
 * @since      1.6.6
 *
 * @package    All_In_One_Video_Gallery
 * @subpackage All_In_One_Video_Gallery/premium
 */

$html5_thumbnail_generator_enabled = ( is_admin() || ! empty( $attributes['enable_html5_thumbnail_generator'] ) ) ? 1 : 0;
?>

<div id="aiovg-thumbnail-generator"<?php if ( ! $html5_thumbnail_generator_enabled ) echo ' style="display: none;"'; ?>>
	<div class="aiovg-inner-wrapper aiovg-toggle-fields aiovg-type-default">
		<!-- Header -->
		<div class="aiovg-header">
			<?php 
			if ( $html5_thumbnail_generator_enabled ) {
				esc_html_e( '(OR) use the "Capture Image" button below to generate an image from your video.', 'all-in-one-video-gallery' ); 
			}
			?>
		</div>

		<!-- Images -->
		<div class="aiovg-body">
			<!-- Capture Button -->
			<?php if ( $html5_thumbnail_generator_enabled ) : ?>			
				<div id="aiovg-html5-thumbnail-generator" class="aiovg-item">
					<div class="aiovg-item-inner">
						<button type="button" class="button button-secondary">
							<svg class="aiovg-svg-icon aiovg-svg-icon-camera" width="24" height="24" viewBox="0 0 32 32">
								<path d="M9.5 19c0 3.59 2.91 6.5 6.5 6.5s6.5-2.91 6.5-6.5-2.91-6.5-6.5-6.5-6.5 2.91-6.5 6.5zM30 8h-7c-0.5-2-1-4-3-4h-8c-2 0-2.5 2-3 4h-7c-1.1 0-2 0.9-2 2v18c0 1.1 0.9 2 2 2h28c1.1 0 2-0.9 2-2v-18c0-1.1-0.9-2-2-2zM16 27.875c-4.902 0-8.875-3.973-8.875-8.875s3.973-8.875 8.875-8.875c4.902 0 8.875 3.973 8.875 8.875s-3.973 8.875-8.875 8.875zM30 14h-4v-2h4v2z"></path>
							</svg>
							<?php _e( 'Capture Image', 'all-in-one-video-gallery' ); ?>
						</button>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<!-- Footer -->
		<div class="aiovg-footer" style="display: none;">
			<?php
			printf(
				'<strong>%s</strong>: %s',
				esc_html__( 'Note', 'all-in-one-video-gallery' ),
				esc_html__( 'When you submit the form, only the image you\'ve selected will be stored in the server to keep the site clean. The other images will be deleted automatically. Still, you can recreate these images using the "Capture Image" button.', 'all-in-one-video-gallery' )
			);
			?>
		</div>

		<?php if ( $html5_thumbnail_generator_enabled ) : ?>
			<!-- Modal -->
			<div id="aiovg-thumbnail-generator-modal" class="aiovg-modal mfp-hide">
				<div id="aiovg-thumbnail-generator-modal-body" class="aiovg-modal-body">
					<div class="aiovg-modal-title">
						<?php esc_html_e( 'Play and capture the scene you wish as a video preview image.', 'all-in-one-video-gallery' ); ?>
					</div>

					<video id="aiovg-thumbnail-generator-player" controls crossorigin="anonymous">
						<source type="video/mp4">
					</video>

					<canvas id="aiovg-thumbnail-generator-canvas"></canvas>

					<div class="aiovg-modal-actions">
						<div class="aiovg-pull-left">
							<?php
							printf(
								__( 'Seek to %s seconds', 'all-in-one-video-gallery' ),
								'<select id="aiovg-thumbnail-generator-seekto" disabled></select>'
							);
							?>
						</div>

						<div class="aiovg-pull-right">
							<button type="button" id="aiovg-thumbnail-generator-button" class="button button-secondary" disabled>
								<?php esc_html_e( 'Capture This Scene', 'all-in-one-video-gallery' ); ?>
							</button>
						</div>

						<div class="aiovg-clearfix"></div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
