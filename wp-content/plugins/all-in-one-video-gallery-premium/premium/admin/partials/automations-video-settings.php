<?php

/**
 * Automations: "Video Post Settings" meta box.
 *
 * @link       https://plugins360.com
 * @since      1.6.2
 *
 * @package    All_In_One_Video_Gallery
 * @subpackage All_In_One_Video_Gallery/premium
 */
?>

<table class="aiovg-table widefat">
  	<tbody>
        <tr>
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Video Categories', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>        
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Video Categories', 'all-in-one-video-gallery' ); ?></strong></p>
                <ul class="aiovg-checklist widefat">
                    <?php
                    $args = array(
                    	'taxonomy'      => 'aiovg_categories',
                    	'walker'        => null,
						'checked_ontop' => false,
						'selected_cats' => array_map( 'intval', $post_meta['video_categories'] )
                    ); 
                
                    wp_terms_checklist( 0, $args );
                    ?>
                </ul>
				<p class="description"><?php esc_html_e( 'Assign categories to the imported videos.', 'all-in-one-video-gallery' ); ?></p>
      		</td>
    	</tr>

		<tr>
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Video Tags', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>        
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Video Tags', 'all-in-one-video-gallery' ); ?></strong></p>
				<ul class="aiovg-checklist widefat">
                    <?php
                    $args = array(
                    	'taxonomy'      => 'aiovg_tags',
                    	'walker'        => null,
						'checked_ontop' => false,
						'selected_cats' => array_map( 'intval', $post_meta['video_tags'] ),
						'echo'          => false
                    ); 
                
					$tags_checklist = wp_terms_checklist( 0, $args );
					$tags_checklist = str_replace( 'tax_input[aiovg_tags]', 'tags', $tags_checklist );

					echo $tags_checklist;
                    ?>
                </ul>
				<p class="description"><?php esc_html_e( 'Assign tags to the imported videos.', 'all-in-one-video-gallery' ); ?></p>
      		</td>
    	</tr>

		<tr>
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Video Description', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>        
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Video Description', 'all-in-one-video-gallery' ); ?></strong></p>
				<label>
					<input type="checkbox" name="video_description" value="1" <?php checked( $post_meta['video_description'], 1 ); ?> />
					<?php esc_html_e( 'Check this option to import the video description.', 'all-in-one-video-gallery' ); ?>
				</label>
      		</td>
    	</tr> 

        <tr>
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Video Date', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>        
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Video Date', 'all-in-one-video-gallery' ); ?></strong></p>
      			<select name="video_date" id="aiovg-video_date" class="select">
                	<?php 
					$options = array(
                        'original' => esc_html__( 'Original date on the video service', 'all-in-one-video-gallery' ),
						'imported' => esc_html__( 'Date when the video is imported', 'all-in-one-video-gallery' )
                    );

					foreach ( $options as $key => $label ) {
						printf( '<option value="%s"%s>%s</option>', $key, selected( $key, $post_meta['video_date'], false ), $label );
					}
					?>
        		</select>
				<p class="description"><?php esc_html_e( 'Select whether to use the original posting date on the video service, or the date when the video is imported.', 'all-in-one-video-gallery' ); ?></p>
      		</td>
    	</tr>

		<tr>
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Video Author', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>        
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Video Author', 'all-in-one-video-gallery' ); ?></strong></p>
				<?php
				$args = array(
					'name'     => 'video_author',
					'selected' => (int) $post_meta['video_author']
				); 
			
				wp_dropdown_users( $args );
				?>
				<p class="description"><?php esc_html_e( 'Select the author to whom the video should be assigned.', 'all-in-one-video-gallery' ); ?></p>
      		</td>
    	</tr> 

		<tr>
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Video Status', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>        
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Video Status', 'all-in-one-video-gallery' ); ?></strong></p>
      			<select name="video_status" id="aiovg-video_status" class="select">
                	<?php 
					$options = array(
                        'draft'   => esc_html__( 'Draft', 'all-in-one-video-gallery' ),
						'pending' => esc_html__( 'Pending', 'all-in-one-video-gallery' ),
						'publish' => esc_html__( 'Publish', 'all-in-one-video-gallery' )
                    );

					foreach ( $options as $key => $label ) {
						printf( '<option value="%s"%s>%s</option>', $key, selected( $key, $post_meta['video_status'], false ), $label );
					}
					?>
        		</select>
				<p class="description"><?php esc_html_e( 'Select the default status of the imported videos. Site admin will be notified through email when an import occurs with the "Pending" status.', 'all-in-one-video-gallery' ); ?></p>
      		</td>
    	</tr>   
  	</tbody>
</table>

<?php
// Add a nonce field
wp_nonce_field( 'aiovg_save_automations_video_settings', 'aiovg_automations_video_settings_nonce' );