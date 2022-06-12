<?php

/**
 * Video Submission Form.
 *
 * @link       https://plugins360.com
 * @since      1.6.1
 *
 * @package    All_In_One_Video_Gallery
 * @subpackage All_In_One_Video_Gallery/premium
 */
?>

<div class="aiovg aiovg-video-form">
	<?php if ( isset( $_GET['status'] ) && 'maybe_spam1' == $_GET['status'] ) : ?>
		<div class="aiovg-notice aiovg-notice-error">
			<p><?php esc_html_e( 'Aborted due to spam detection. Sorry, if you are a real user and please try again. If the issue repeats, kindly write to the site administrator.', 'all-in-one-video-gallery' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( isset( $_GET['status'] ) && 'maybe_spam2' == $_GET['status'] ) : ?>
		<div class="aiovg-notice aiovg-notice-error">
			<p><?php esc_html_e( 'The form submission is too fast than we expected. So, our system has aborted the action considering a spam. Sorry, if you are a real user and please try again. If the issue repeats, kindly write to the site administrator.', 'all-in-one-video-gallery' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( isset( $_GET['updated'] ) && 1 == $_GET['updated'] ) : ?>
		<div class="aiovg-notice aiovg-notice-success">
			<p><?php esc_html_e( 'Saved', 'all-in-one-video-gallery' ); ?></p>
		</div>
	<?php endif; ?>

	<form id="aiovg-form-video" action="<?php echo aiovg_premium_get_video_form_page_url(); ?>" method="post">
		<div class="aiovg-form-user-notes">
			<div class="aiovg-notes-required-fields"><?php printf( esc_html__( 'Fields marked with an %s are required', 'all-in-one-video-gallery' ), '<span class="aiovg-required-symbol">*</span>' ); ?></div>
			<?php if ( ! empty( $attributes['allow_file_uploads'] ) ) : ?>
				<div class="aiovg-notes-max-upload-size"><?php printf( esc_html__( 'Maximum upload file size: %s', 'all-in-one-video-gallery' ), '<span class="aiovg-text-highlight">' . $attributes['max_upload_size'] . '</span>' ); ?></div>
			<?php endif; ?>
		</div>

		<!-- Video Title -->
		<div id="aiovg-field-title" class="aiovg-row aiovg-form-group">
			<div class="aiovg-col aiovg-col-p-25">
				<label class="aiovg-form-label">
					<?php esc_html_e( 'Video Title', 'all-in-one-video-gallery' ); ?>
					<span class="aiovg-required-symbol">*</span>
				</label>
			</div>
			<div class="aiovg-col aiovg-col-p-75">
				<input type="text" name="title" id="aiovg-title" class="aiovg-form-control aiovg-field-validate" placeholder="<?php esc_attr_e( 'Enter your video title here', 'all-in-one-video-gallery' ); ?>" value="<?php echo esc_attr( $attributes['title'] ); ?>" />
				<div class="aiovg-field-error"></div>
			</div>
		</div>

		<?php if ( ! empty( $attributes['assign_categories'] ) ) : ?>
			<!-- Select Categories -->
			<div id="aiovg-field-categories" class="aiovg-row aiovg-form-group">
				<div class="aiovg-col aiovg-col-p-25">
					<label class="aiovg-form-label"><?php esc_html_e( 'Select Categories', 'all-in-one-video-gallery' ); ?></label>
				</div>
				<div class="aiovg-col aiovg-col-p-75">
					<ul class="aiovg-form-control aiovg-checklist">
						<?php
						$categories_args = array(
							'taxonomy'      => 'aiovg_categories',
							'walker'        => null,
							'checked_ontop' => false,
							'selected_cats' => array_map( 'intval', $attributes['catids'] ),
							'exclude'       => array(),
							'echo'          => 0
						);

						$categories_excluded = get_terms( array(
							'taxonomy'   => 'aiovg_categories',
							'hide_empty' => false,
							'fields'     => 'ids',
							'meta_key'   => 'exclude_video_form',
							'meta_value' => 1
						) );

						if ( ! empty( $categories_excluded ) && ! is_wp_error( $categories_excluded ) ) {
							$categories_args['exclude']	= array_map( 'intval', $categories_excluded );

							foreach ( $categories_args['selected_cats'] as $index => $id ) {
								if ( in_array( $id, $categories_args['exclude'] ) ) {
									unset( $categories_args['selected_cats'][ $index ] );
								}
							}
						}

						$categories_args = apply_filters( 'aiovg_video_form_categories_args', $categories_args );
						$categories = wp_terms_checklist( 0, $categories_args );

						if ( ! empty( $categories_args['exclude'] ) ) {
							foreach ( $categories_args['exclude'] as $id ) {
								$categories = str_replace(
									"li id='aiovg_categories-" . $id . "'",
									"li id='aiovg_categories-" . $id . "' style='display: none;'",
									$categories
								);
							}
						}

						echo $categories;
						?>
					</ul>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( 1 == count( $attributes['allowed_source_types'] ) ) : ?>
			<!-- Type -->
			<input type="hidden" name="type" id="aiovg-video-type" value="<?php echo esc_attr( $attributes['type'] ); ?>" />
		<?php elseif ( count( $attributes['allowed_source_types'] ) > 1 ) : ?>
			<!-- Type -->
			<div id="aiovg-field-type" class="aiovg-row aiovg-form-group">
				<div class="aiovg-col aiovg-col-p-25">
					<label class="aiovg-form-label"><?php esc_html_e( 'Select Source', 'all-in-one-video-gallery' ); ?></label>
				</div>
				<div class="aiovg-col aiovg-col-p-75">
					<select name="type" id="aiovg-video-type" class="aiovg-form-control">
						<?php
						foreach ( $attributes['allowed_source_types'] as $key => $label ) {
							printf( '<option value="%s"%s>%s</option>', $key, selected( $key, $attributes['type'], false ), $label );
						}
						?>
					</select>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( array_key_exists( 'default', $attributes['allowed_source_types'] ) ) : ?>
			<!-- MP4 -->
			<div id="aiovg-field-mp4" class="aiovg-row aiovg-form-group aiovg-toggle-fields aiovg-type-default">
				<div class="aiovg-col aiovg-col-p-25">
					<label class="aiovg-form-label">
						<?php esc_html_e( 'Video', 'all-in-one-video-gallery' ); ?>
						<span class="aiovg-required-symbol">*</span>
						<span class="aiovg-text-muted">(mp4, webm, ogv, m4v, mov)</span>
					</label>
				</div>
				<div class="aiovg-col aiovg-col-p-75">
					<?php if ( ! empty( $attributes['allow_file_uploads'] ) ) : ?>
						<div class="aiovg-row aiovg-media-uploader">
							<div class="aiovg-col aiovg-col-p-75">
								<input type="text" name="mp4" id="aiovg-mp4" class="aiovg-form-control aiovg-field-validate" placeholder="<?php esc_attr_e( 'Enter your direct file URL (OR) upload your file using the button here', 'all-in-one-video-gallery' ); ?> &rarr;" value="<?php echo esc_attr( $attributes['mp4'] ); ?>" />
							</div>
							<div class="aiovg-col aiovg-col-p-25">
								<button type="button" id="aiovg-button-upload-mp4" class="aiovg-button aiovg-button-upload" data-format="mp4"><?php esc_html_e( 'Upload File', 'all-in-one-video-gallery' ); ?></button>
								<div class="aiovg-upload-status">
									<div class="aiovg-upload-progress"></div>
									<div class="aiovg-upload-cancel"><a href="javascript: void(0);"><?php esc_html_e( 'cancel', 'all-in-one-video-gallery' ); ?></a></div>
								</div>
							</div>
						</div>
					<?php else : ?>
						<input type="text" name="mp4" id="aiovg-mp4" class="aiovg-form-control aiovg-field-validate" placeholder="<?php esc_attr_e( 'Enter your direct file URL here', 'all-in-one-video-gallery' ); ?>" value="<?php echo esc_attr( $attributes['mp4'] ); ?>" />
					<?php endif; ?>

					<div class="aiovg-field-error"></div>
				</div>
			</div>

			<?php if ( ! empty( $attributes['webm'] ) ) : ?>
				<!-- WebM -->
				<div id="aiovg-field-webm" class="aiovg-row aiovg-form-group aiovg-toggle-fields aiovg-type-default">
					<div class="aiovg-col aiovg-col-p-25">
						<label class="aiovg-form-label"><?php esc_html_e( 'WebM', 'all-in-one-video-gallery' ); ?></label>
						<div class="aiovg-text-highlight">(<?php esc_html_e( 'deprecated', 'all-in-one-video-gallery' ); ?>)</div>
					</div>
					<div class="aiovg-col aiovg-col-p-75">
					<div class="aiovg-image-thumb"></div>	<?php if ( ! empty( $attributes['allow_file_uploads'] ) ) : ?>
							<div class="aiovg-row aiovg-media-uploader">
								<div class="aiovg-col aiovg-col-p-75">
									<input type="text" name="webm" id="aiovg-webm" class="aiovg-form-control aiovg-field-validate" placeholder="<?php esc_attr_e( 'Enter your direct file URL (OR) upload your file using the button here', 'all-in-one-video-gallery' ); ?> &rarr;" value="<?php echo esc_attr( $attributes['webm'] ); ?>" /></div>
								</div>
								<div class="aiovg-col aiovg-col-p-25">
									<button type="button" id="aiovg-button-upload-webm" class="aiovg-button aiovg-button-upload" data-format="webm"><?php esc_html_e( 'Upload File', 'all-in-one-video-gallery' ); ?></button>
									<div class="aiovg-upload-status">
										<div class="aiovg-upload-progress"></div>
										<div class="aiovg-upload-cancel"><a href="javascript: void(0);"><?php esc_html_e( 'cancel', 'all-in-one-video-gallery' ); ?></a></div>
									</div>
								</div>
							</div>
						<?php else : ?>
							<input type="text" name="webm" id="aiovg-webm" class="aiovg-form-control aiovg-field-validate" placeholder="<?php esc_attr_e( 'Enter your direct file URL here', 'all-in-one-video-gallery' ); ?>" value="<?php echo esc_attr( $attributes['webm'] ); ?>" />
						<?php endif; ?>

						<div class="aiovg-field-error"></div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $attributes['ogv'] ) ) : ?>
				<!-- OGV -->
				<div id="aiovg-field-ogv" class="aiovg-row aiovg-form-group aiovg-toggle-fields aiovg-type-default">
					<div class="aiovg-col aiovg-col-p-25">
						<label class="aiovg-form-label"><?php esc_html_e( 'OGV', 'all-in-one-video-gallery' ); ?></label>
						<div class="aiovg-text-highlight">(<?php esc_html_e( 'deprecated', 'all-in-one-video-gallery' ); ?>)</div>
					</div>
					<div class="aiovg-col aiovg-col-p-75">
						<?php if ( ! empty( $attributes['allow_file_uploads'] ) ) : ?>
							<div class="aiovg-row aiovg-media-uploader">
								<div class="aiovg-col aiovg-col-p-75">
									<input type="text" name="ogv" id="aiovg-ogv" class="aiovg-form-control" placeholder="<?php esc_attr_e( 'Enter your direct file URL (OR) upload your file using the button here', 'all-in-one-video-gallery' ); ?> &rarr;" value="<?php echo esc_attr( $attributes['ogv'] ); ?>" />
								</div>
								<div class="aiovg-col aiovg-col-p-25">
									<button type="button" id="aiovg-button-upload-ogv" class="aiovg-button aiovg-button-upload" data-format="ogv"><?php esc_html_e( 'Upload File', 'all-in-one-video-gallery' ); ?></button>
									<div class="aiovg-upload-status">
										<div class="aiovg-upload-progress"></div>
										<div class="aiovg-upload-cancel"><a href="javascript: void(0);"><?php esc_html_e( 'cancel', 'all-in-one-video-gallery' ); ?></a></div>
									</div>
								</div>
							</div>
						<?php else : ?>
							<input type="text" name="ogv" id="aiovg-ogv" class="aiovg-form-control" placeholder="<?php esc_attr_e( 'Enter your direct file URL here', 'all-in-one-video-gallery' ); ?>" value="<?php echo esc_attr( $attributes['ogv'] ); ?>" />
						<?php endif; ?>

						<div class="aiovg-field-error"></div>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( array_key_exists( 'youtube', $attributes['allowed_source_types'] ) ) : ?>
			<!-- YouTube -->
			<div id="aiovg-field-youtube" class="aiovg-row aiovg-form-group aiovg-toggle-fields aiovg-type-youtube">
				<div class="aiovg-col aiovg-col-p-25">
					<label class="aiovg-form-label">
						<?php esc_html_e( 'YouTube', 'all-in-one-video-gallery' ); ?>
						<span class="aiovg-required-symbol">*</span>
					</label>
				</div>
				<div class="aiovg-col aiovg-col-p-75">
					<input type="text" name="youtube" id="aiovg-youtube" class="aiovg-form-control aiovg-field-validate" placeholder="<?php printf( '%s: https://www.youtube.com/watch?v=twYp6W6vt2U', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $attributes['youtube'] ); ?>" />
					<div class="aiovg-field-error"></div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( array_key_exists( 'vimeo', $attributes['allowed_source_types'] ) ) : ?>
			<!-- Vimeo -->
			<div id="aiovg-field-vimeo" class="aiovg-row aiovg-form-group aiovg-toggle-fields aiovg-type-vimeo">
				<div class="aiovg-col aiovg-col-p-25">
					<label class="aiovg-form-label">
						<?php esc_html_e( 'Vimeo', 'all-in-one-video-gallery' ); ?>
						<span class="aiovg-required-symbol">*</span>
					</label>
				</div>
				<div class="aiovg-col aiovg-col-p-75">
					<input type="text" name="vimeo" id="aiovg-vimeo" class="aiovg-form-control aiovg-field-validate" placeholder="<?php printf( '%s: https://vimeo.com/108018156', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $attributes['vimeo'] ); ?>" />
					<div class="aiovg-field-error"></div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( array_key_exists( 'dailymotion', $attributes['allowed_source_types'] ) ) : ?>
			<!-- Dailymotion -->
			<div id="aiovg-field-dailymotion" class="aiovg-row aiovg-form-group aiovg-toggle-fields aiovg-type-dailymotion">
				<div class="aiovg-col aiovg-col-p-25">
					<label class="aiovg-form-label">
						<?php esc_html_e( 'Dailymotion', 'all-in-one-video-gallery' ); ?>
						<span class="aiovg-required-symbol">*</span>
					</label>
				</div>
				<div class="aiovg-col aiovg-col-p-75">
					<input type="text" name="dailymotion" id="aiovg-dailymotion" class="aiovg-form-control aiovg-field-validate" placeholder="<?php printf( '%s: https://www.dailymotion.com/video/x11prnt', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $attributes['dailymotion'] ); ?>" />
					<div class="aiovg-field-error"></div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( array_key_exists( 'facebook', $attributes['allowed_source_types'] ) ) : ?>
			<!-- Facebook -->
			<div id="aiovg-field-facebook" class="aiovg-row aiovg-form-group aiovg-toggle-fields aiovg-type-facebook">
				<div class="aiovg-col aiovg-col-p-25">
					<label class="aiovg-form-label">
						<?php esc_html_e( 'Facebook', 'all-in-one-video-gallery' ); ?>
						<span class="aiovg-required-symbol">*</span>
					</label>
				</div>
				<div class="aiovg-col aiovg-col-p-75">
					<input type="text" name="facebook" id="aiovg-facebook" class="aiovg-form-control aiovg-field-validate" placeholder="<?php printf( '%s: https://www.facebook.com/facebook/videos/10155278547321729', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $attributes['facebook'] ); ?>" />
					<div class="aiovg-field-error"></div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( array_key_exists( 'adaptive', $attributes['allowed_source_types'] ) ) : ?>
			<!-- HLS | M(PEG)-DASH -->
			<div id="aiovg-field-adaptive" class="aiovg-row aiovg-form-group aiovg-toggle-fields aiovg-type-adaptive">
				<div class="aiovg-col aiovg-col-p-25">
					<label class="aiovg-form-label">
						<?php esc_html_e( 'HLS', 'all-in-one-video-gallery' ); ?> | <?php esc_html_e( 'M(PEG)-DASH', 'all-in-one-video-gallery' ); ?>
						<span class="aiovg-required-symbol">*</span>
					</label>
				</div>
				<div class="aiovg-col aiovg-col-p-75">
					<input type="text" name="adaptive" id="aiovg-adaptive" class="aiovg-form-control aiovg-field-validate" placeholder="<?php printf( '%s: https://www.mysite.com/stream.m3u8', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $attributes['adaptive'] ); ?>" />
					<div class="aiovg-field-error"></div>
				</div>
			</div>
		<?php endif; ?>

		<!-- Image -->
		<div id="aiovg-field-image" class="aiovg-row aiovg-form-group">
			<div class="aiovg-col aiovg-col-p-25">
				<label class="aiovg-form-label"><?php esc_html_e( 'Image', 'all-in-one-video-gallery' ); ?></label>
			</div>
			<div class="aiovg-col aiovg-col-p-75">
				<?php if ( ! empty( $attributes['allow_file_uploads'] ) ) : ?>
					<div class="aiovg-row aiovg-media-uploader">
						<div class="aiovg-col aiovg-col-p-75">
							<input type="text" name="image" id="aiovg-image" class="aiovg-form-control aiovg-field-validate" placeholder="<?php esc_attr_e( 'Enter your direct file URL (OR) upload your file using the button here', 'all-in-one-video-gallery' ); ?> &rarr;" value="<?php echo esc_attr( $attributes['image'] ); ?>" />
						</div>
						<div class="aiovg-col aiovg-col-p-25">
							<button type="button" id="aiovg-button-upload-image" class="aiovg-button aiovg-button-upload" data-format="image"><?php esc_html_e( 'Upload File', 'all-in-one-video-gallery' ); ?></button>
							<div class="aiovg-upload-status">
								<div class="aiovg-upload-progress"></div>
								<div class="aiovg-upload-cancel"><a href="javascript: void(0);"><?php esc_html_e( 'cancel', 'all-in-one-video-gallery' ); ?></a></div>
							</div>
						</div>
					</div>
				<?php else : ?>
					<input type="text" name="image" id="aiovg-image" class="aiovg-form-control aiovg-field-validate" placeholder="<?php esc_attr_e( 'Enter your direct file URL here', 'all-in-one-video-gallery' ); ?>" value="<?php echo esc_attr( $attributes['image'] ); ?>" />
				<?php endif; ?>

				<div class="aiovg-field-error"></div>

				<!-- Thumbnail Generator -->
				<?php the_aiovg_premium_thumbnail_generator(); ?>
			</div>
		</div>

		<!-- Video Description -->
		<div id="aiovg-field-description" class="aiovg-row aiovg-form-group">
			<div class="aiovg-col aiovg-col-p-25">
				<label class="aiovg-form-label"><?php esc_html_e( 'Video Description', 'all-in-one-video-gallery' ); ?></label>
			</div>
			<div class="aiovg-col aiovg-col-p-75">
				<textarea name="description" id="aiovg-description" class="aiovg-form-control" rows="6" placeholder="<?php esc_attr_e( 'Enter your video description here', 'all-in-one-video-gallery' ); ?>"><?php echo wp_kses_post( $attributes['description'] ); ?></textarea>
			</div>
		</div>

		<?php if ( ! empty( $attributes['assign_tags'] ) ) : $uid = aiovg_get_uniqid(); ?>
			<!-- Select Tags -->
			<div id="aiovg-field-tags" class="aiovg-row aiovg-form-group aiovg-field-tag">
				<div class="aiovg-col aiovg-col-p-25">
					<label class="aiovg-form-label"><?php esc_html_e( 'Select Tags', 'all-in-one-video-gallery' ); ?></label>
				</div>
				<div class="aiovg-col aiovg-col-p-75">
					<div class="aiovg-autocomplete" data-uid="<?php echo esc_attr( $uid ); ?>">
						<input type="text" id="aiovg-autocomplete-input-<?php echo esc_attr( $uid ); ?>" class="aiovg-form-control aiovg-autocomplete-input" placeholder="<?php esc_attr_e( 'Start typing for suggestions', 'all-in-one-video-gallery' ); ?>" autocomplete="off" />

						<?php
						$tags_args = array(
							'taxonomy'   => 'aiovg_tags',
							'orderby'    => 'name',
							'order'      => 'asc',
							'hide_empty' => false
						);

						$terms = get_terms( $tags_args );

						// Source
						echo '<select id="aiovg-autocomplete-select-' . esc_attr( $uid ) . '" class="aiovg-autocomplete-select" style="display: none;">';

						if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
							foreach ( $terms as $term ) {
								printf(
									'<option value="%d">%s</option>',
									$term->term_id,
									esc_html( $term->name )
								);
							}
						}

						echo '</select>';
						?>
					</div>

					<div id="aiovg-autocomplete-tags-<?php echo esc_attr( $uid ); ?>" class="aiovg-autocomplete-tags">
						<?php
						if ( ! empty( $attributes['tagids'] ) ) {
							$selected_tags = array_map( 'intval', $attributes['tagids'] );

							if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
								foreach ( $terms as $term ) {
									if ( in_array( $term->term_id, $selected_tags ) ) {
										$html  = '<span class="aiovg-tag-item aiovg-tag-item-' . $term->term_id . '">';
										$html .= '<span class="aiovg-tag-item-name">' . esc_html( $term->name ) . '</span>';
										$html .= '<span class="aiovg-tag-item-close">&times;</span>';
										$html .= '<input type="hidden" name="ta[]" value="' . $term->term_id . '" />';
										$html .= '</span>';

										echo $html;
									}
								}
							}
						}
						?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<!-- Hook for developers to add new fields -->
        <?php do_action( 'aiovg_video_form_fields', $attributes ); ?>

		<?php if ( ! empty( $attributes['terms_and_conditions'] ) ) :
			$terms_and_conditions = apply_filters( 'aiovg_terms_and_conditions_page_url', $attributes['terms_and_conditions'] );
			?>
			<!-- Terms and Conditions -->
			<div id="aiovg-field-tos" class="aiovg-row aiovg-form-group">
				<div class="aiovg-col aiovg-col-p-25">&nbsp;</div>
				<div class="aiovg-col aiovg-col-p-75">
					<label class="aiovg-form-label">
						<input type="checkbox" name="tos" id="aiovg-tos" class="aiovg-field-validate" />
						<?php printf( __( 'I agree to the <a href="%s" target="_blank">terms and conditions</a>', 'all-in-one-video-gallery' ), esc_url_raw( $terms_and_conditions ) ); ?>
						<span class="aiovg-required-symbol">*</span>
					</label>
					<div class="aiovg-field-error"></div>
				</div>
			</div>
		<?php endif; ?>

		<!-- Action Buttons -->
		<div id="aiovg-field-action-buttons" class="aiovg-row aiovg-form-group">
			<div class="aiovg-col aiovg-col-p-25">&nbsp;</div>
			<div class="aiovg-col aiovg-col-p-75">
				<?php wp_nonce_field( 'aiovg_save_video', 'aiovg_video_nonce' ); ?>
				<input type="hidden" name="post_type" value="aiovg_videos" />
				<input type="hidden" name="post_id" value="<?php echo esc_attr( $attributes['post_id'] ); ?>" />
				<?php if ( $attributes['is_new'] ) : ?>
					<input type="submit" name="action" class="aiovg-button aiovg-button-publish" value="<?php esc_attr_e( 'Publish', 'all-in-one-video-gallery' ); ?>" />
					<input type="submit" name="action" class="aiovg-button aiovg-button-draft" value="<?php esc_attr_e( 'Save Draft', 'all-in-one-video-gallery' ); ?>" />
				<?php else : ?>
					<input type="submit" name="action" class="aiovg-button aiovg-button-publish" value="<?php esc_attr_e( 'Save Changes', 'all-in-one-video-gallery' ); ?>" />
				<?php endif; ?>
				&nbsp;
				<a href="<?php echo aiovg_premium_get_user_dashboard_page_url(); ?>">
					<?php esc_html_e( 'Cancel', 'all-in-one-video-gallery' ); ?>
				</a>
			</div>
		</div>
	</form>

	<form id="aiovg-form-upload" method="post" action="#" enctype="multipart/form-data" style="display: none;">
		<input type="hidden" name="post_id" value="<?php echo (int) $attributes['post_id']; ?>" />
		<input type="file" name="media" id="aiovg-upload-media" />
		<input type="hidden" name="format" id="aiovg-upload-format" />
        <input type="hidden" name="action" value="aiovg_public_upload_media" />
	</form>
</div>
