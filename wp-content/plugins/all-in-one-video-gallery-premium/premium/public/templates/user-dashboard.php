<?php

/**
 * User Videos Manager.
 *
 * @link       https://plugins360.com
 * @since      1.6.1
 *
 * @package    All_In_One_Video_Gallery
 * @subpackage All_In_One_Video_Gallery/premium
 */
?>

<div class="aiovg aiovg-user-dashboard">
	<?php if ( isset( $_GET['permission_denied'] ) && 1 == $_GET['permission_denied'] ) : ?>
		<div class="aiovg-notice aiovg-notice-error">
			<?php
			printf( 
				'<p>' . __( 'You do not have sufficient permissions to do this action. <a href="%s">Go back</a>', 'all-in-one-video-gallery' ) . '</p>',
				aiovg_premium_get_user_dashboard_page_url()
			);
			return;
			?>
		</div>
	<?php endif; ?>

	<?php if ( isset( $_GET['pending'] ) && 1 == $_GET['pending'] ) : ?>
		<div class="aiovg-notice aiovg-notice-success">
			<p><?php esc_html_e( 'Your video has been received and it\'s pending review. This review process could take up to 48 hours. Please be patient.', 'all-in-one-video-gallery' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( isset( $_GET['updated'] ) && 1 == $_GET['updated'] ) : ?>
		<div class="aiovg-notice aiovg-notice-success">
			<p><?php esc_html_e( 'Saved', 'all-in-one-video-gallery' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( isset( $_GET['deleted'] ) && 1 == $_GET['deleted'] ) : ?>
		<div class="aiovg-notice aiovg-notice-success">
			<p><?php esc_html_e( 'Deleted', 'all-in-one-video-gallery' ); ?></p>
		</div>
	<?php endif; ?>

	<div class="aiovg-search-form aiovg-search-form-template-horizontal">
		<form method="get" action="<?php echo aiovg_premium_get_user_dashboard_page_url(); ?>">
			<div class="aiovg-row aiovg-no-margin">
				<div class="aiovg-col aiovg-col-p-60">			
					<?php if ( ! get_option('permalink_structure') ) : ?>
						<input type="hidden" name="page_id" value="<?php echo esc_attr( $attributes['page_id'] ); ?>" />
					<?php endif; ?>        
						
					<div class="aiovg-form-group aiovg-field-keyword">
						<input type="text" name="vi" class="aiovg-form-control" placeholder="<?php esc_attr_e( 'Search videos by title', 'all-in-one-video-gallery' ); ?>" value="<?php echo esc_attr( $attributes['vi'] ); ?>" />
					</div>
							
					<div class="aiovg-form-group aiovg-field-submit">
						<input type="submit" class="aiovg-button aiovg-responsive-button" value="<?php esc_attr_e( 'Search', 'all-in-one-video-gallery' ); ?>" /> 
					</div>				
				</div>

				<div class="aiovg-col aiovg-col-p-40 aiovg-text-right">
					<div class="aiovg-form-group">
						<button type="button" class="aiovg-button aiovg-responsive-button" onclick="location.href='<?php echo aiovg_premium_get_video_form_page_url(); ?>'">
							<?php esc_html_e( 'Add New Video', 'all-in-one-video-gallery' ); ?>
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>	

	<br />

	<table id="aiovg-user-videos-list">
		<tr>
			<th class="aiovg-col-image"></th>
			<th class="aiovg-col-caption"><?php esc_html_e( 'Title', 'all-in-one-video-gallery' ); ?></th>
			<th class="aiovg-col-action"><?php esc_html_e( 'Actions', 'all-in-one-video-gallery' ); ?></th>
		</tr>
		<?php		
		if ( $aiovg_query->have_posts() ) {
			// Start the loop
			while ( $aiovg_query->have_posts() ) :        
				$aiovg_query->the_post();  
				
				$post_meta = get_post_meta( get_the_ID() );
				$image     = aiovg_get_image_url( $post_meta['image_id'][0], 'large', $post_meta['image'][0] );
				?>
				<tr>         
					<td class="aiovg-col-image">
						<div class="aiovg-relative">
							<a href="<?php the_permalink(); ?>" class="aiovg-link-image">
								<img src="<?php echo esc_url( $image ); ?>" />
								<?php if ( ! empty( $post_meta['duration'][0] ) ) : ?>
									<div class="aiovg-duration"><small><?php echo esc_html( $post_meta['duration'][0] ); ?></small></div>
								<?php endif; ?>
								
								<img src="<?php echo AIOVG_PLUGIN_URL; ?>public/assets/images/play.png" class="aiovg-play" /> 
							</a> 
						</div>                      
					</td>
					<td class="aiovg-col-caption">
						<div class="aiovg-title">
							<a href="<?php the_permalink(); ?>" class="aiovg-link-title"><?php echo esc_html( get_the_title() ); ?></a>
						</div>

						<?php
						$meta = array();					

						$meta[] = sprintf( esc_html__( 'Posted on %s %s', 'all-in-one-video-gallery' ), get_the_date(), get_the_time() );
						$meta[] = sprintf( esc_html__( '%d views', 'all-in-one-video-gallery' ), $post_meta['views'][0] );

						printf( '<div class="aiovg-user"><small>%s</small></div>', implode( ' | ', $meta ) );
						?>
					
						<?php
						$categories = get_the_terms( get_the_ID(), 'aiovg_categories' );
						if ( ! empty( $categories ) ) {
							$meta = array();
							foreach ( $categories as $category ) {
								$category_url = aiovg_get_category_page_url( $category );
								$meta[]       = sprintf( '<a href="%s" class="aiovg-link-category">%s</a>', esc_url( $category_url ), esc_html( $category->name ) );
							}
							printf( '<div class="aiovg-category"><span class="aiovg-icon-categories"></span> %s</div>', implode( ', ', $meta ) );
						}
						?>

						<?php
						$tags = get_the_terms( get_the_ID(), 'aiovg_tags' );
						if ( ! empty( $tags ) ) {
							$meta = array();
							foreach ( $tags as $tag ) {
								$tag_url = aiovg_get_tag_page_url( $tag );
								$meta[]  = sprintf( '<a href="%s" class="aiovg-link-tag">%s</a>', esc_url( $tag_url ), esc_html( $tag->name ) );
							}
							printf( '<div class="aiovg-tag"><span class="aiovg-icon-tags"></span> %s</div>', implode( ', ', $meta ) );
						}
						?>

						<div class="aiovg-description">
							<strong><?php esc_html_e( 'Status', 'all-in-one-video-gallery' ); ?>:</strong> <?php echo $post->post_status; ?>
						</div>
					</td>		
					<td class="aiovg-col-action">
						<a href="<?php echo aiovg_premium_get_edit_video_page_url( get_the_ID() ); ?>" class="aiovg-link-edit-video"><?php esc_html_e( 'Edit', 'all-in-one-video-gallery' ); ?></a>
						<span class="aiovg-link-separator">|</span>
						<a onclick="return confirm( '<?php esc_html_e( 'Are you SURE you want to delete this video?', 'all-in-one-video-gallery' ); ?>' )" href="<?php echo aiovg_premium_get_delete_video_page_url( get_the_ID() ); ?>"" class="aiovg-link-delete-video">
							<?php esc_html_e( 'Delete', 'all-in-one-video-gallery' ); ?>
						</a>
					</td>
				</tr>         
				<?php              
			// End of the loop
			endwhile;

			// Use reset postdata to restore orginal query
			wp_reset_postdata();
		} else {
			printf( '<tr><td colspan="3">%s</td></tr>', aiovg_get_message( 'videos_empty' ) );
		}				
		?>	
	</table>

	<?php
	// Pagination
	the_aiovg_pagination( $aiovg_query->max_num_pages, "", $attributes['paged'] );
	?>
</div>
