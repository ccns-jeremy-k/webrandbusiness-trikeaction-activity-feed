<?php

/**
 * Automations: "Configure Import Sources" meta box.
 *
 * @link       https://plugins360.com
 * @since      1.6.2
 *
 * @package    All_In_One_Video_Gallery
 * @subpackage All_In_One_Video_Gallery/premium
 */
?>

<table class="aiovg-table widefat aiovg-automations-service-youtube aiovg-automations-type-<?php echo esc_attr( $post_meta['type'] ); ?>">
  	<tbody>
    	<tr class="aiovg-automations-field-service">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Video Service', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>        
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Video Service', 'all-in-one-video-gallery' ); ?></strong></p>
				<?php
				$options = aiovg_premium_get_automations_services();

				if ( ! empty( $post_meta['import_status'] ) ) : ?>
					<p class="description"><?php echo esc_html( $options[ $post_meta['service'] ] ); ?></p>
					<input type="hidden" name="service" id="aiovg-service" value="<?php echo esc_attr( $post_meta['service'] ); ?>" /> 
				<?php else: ?>
					<select name="service" id="aiovg-service" class="select">
						<?php 
						foreach ( $options as $key => $label ) {
							printf( '<option value="%s"%s>%s</option>', $key, selected( $key, $post_meta['service'], false ), $label );
						}
						?>
					</select>
				<?php endif; ?>
      		</td>
    	</tr>

        <tr class="aiovg-automations-field-type">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Source Type', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>        
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Source Type', 'all-in-one-video-gallery' ); ?></strong></p>
				<?php
				$options = aiovg_premium_get_automations_types();

				if ( ! empty( $post_meta['import_status'] ) ) : ?>
					<p class="description"><?php echo esc_html( $options[ $post_meta['type'] ] ); ?></p>
					<input type="hidden" name="type" id="aiovg-type" value="<?php echo esc_attr( $post_meta['type'] ); ?>" /> 
				<?php else: ?>
					<ul class="aiovg-radio horizontal">
						<?php
						foreach ( $options as $key => $label ) {
							printf( '<li><label><input type="radio" name="type" id="aiovg-type-%1$s" value="%1$s"%2$s/>%3$s</label></li>', $key, checked( $key, $post_meta['type'], false ), $label );
						}
						?>
					</ul>
				<?php endif; ?>
      		</td>
    	</tr>

        <tr class="aiovg-automations-field-search">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Search Keyword', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Search Keyword', 'all-in-one-video-gallery' ); ?></strong></p>
				<?php
				if ( ! empty( $post_meta['import_status'] ) ) : ?>
					<p class="description"><?php echo esc_html( $post_meta['search'] ); ?></p>
					<input type="hidden" name="search" id="aiovg-search" value="<?php echo esc_attr( $post_meta['search'] ); ?>" /> 
				<?php else: ?>
					<div class="aiovg-input-wrap">
						<input type="text" name="search" id="aiovg-search" class="text" placeholder="<?php printf( '%s: Cartoon', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_attr( $post_meta['search'] ); ?>" />
						<p class="description"><?php esc_html_e( 'Enter search terms (space:AND, -:NOT, |:OR)', 'all-in-one-video-gallery' ); ?></p>
					</div>
				<?php endif; ?>
      		</td>
    	</tr>

        <tr class="aiovg-automations-field-playlist">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Playlist URL', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Playlist URL', 'all-in-one-video-gallery' ); ?></strong></p>
				<?php
				if ( ! empty( $post_meta['import_status'] ) ) : ?>
					<p class="description"><?php echo esc_url( $post_meta['playlist'] ); ?></p>
					<input type="hidden" name="playlist" id="aiovg-playlist" value="<?php echo esc_url( $post_meta['playlist'] ); ?>" /> 
				<?php else: ?>
					<div class="aiovg-input-wrap">
						<input type="text" name="playlist" id="aiovg-playlist" class="text" placeholder="<?php printf( '%s: https://www.youtube.com/playlist?list=XXXXXXXXXX', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $post_meta['playlist'] ); ?>" />
					</div>
				<?php endif; ?>
      		</td>
    	</tr>

        <tr class="aiovg-automations-field-channel">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Channel URL', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Channel URL', 'all-in-one-video-gallery' ); ?></strong></p>
				<?php
				if ( ! empty( $post_meta['import_status'] ) ) : ?>
					<p class="description"><?php echo esc_url( $post_meta['channel'] ); ?></p>
					<input type="hidden" name="channel" id="aiovg-channel" value="<?php echo esc_url( $post_meta['channel'] ); ?>" /> 
				<?php else: ?>
					<div class="aiovg-input-wrap">
						<input type="text" name="channel" id="aiovg-channel" class="text" placeholder="<?php printf( '%s: https://www.youtube.com/channel/XXXXXXXXXX', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $post_meta['channel'] ); ?>" />
					</div>
				<?php endif; ?>
      		</td>
    	</tr>

        <tr class="aiovg-automations-field-username">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Username', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Username', 'all-in-one-video-gallery' ); ?></strong></p>
				<?php
				if ( ! empty( $post_meta['import_status'] ) ) : ?>
					<p class="description"><?php echo esc_html( $post_meta['username'] ); ?></p>
					<input type="hidden" name="username" id="aiovg-username" value="<?php echo esc_attr( $post_meta['username'] ); ?>" /> 
				<?php else: ?>
					<div class="aiovg-input-wrap">
						<input type="text" name="username" id="aiovg-username" class="text" placeholder="<?php printf( '%s: SanRosh', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_attr( $post_meta['username'] ); ?>" />
					</div>
				<?php endif; ?>
      		</td>
    	</tr>

        <tr class="aiovg-automations-field-videos">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Video URLs', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Video URLs', 'all-in-one-video-gallery' ); ?></strong></p>
				<?php
				if ( ! empty( $post_meta['import_status'] ) ) : ?>
					<p class="description aiovg-checklist"><?php echo wp_kses_post( nl2br( $post_meta['videos'] ) ); ?></p>
					<input type="hidden" name="videos" id="aiovg-videos" value="<?php echo esc_textarea( $post_meta['videos'] ); ?>" /> 
				<?php else: ?>
					<div class="aiovg-input-wrap">
						<textarea name="videos" id="aiovg-videos" rows="8" placeholder="<?php printf( '%s: https://www.youtube.com/watch?v=XXXXXXXXXX', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>"/><?php echo esc_textarea( $post_meta['videos'] ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Enter one video per line.', 'all-in-one-video-gallery' ); ?></p>
					</div>
				<?php endif; ?>
      		</td>
    	</tr>

		<tr class="aiovg-automations-field-exclude">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Exclude URLs', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Exclude URLs', 'all-in-one-video-gallery' ); ?></strong></p>
      			<div class="aiovg-input-wrap">
          			<textarea name="exclude" id="aiovg-exclude" rows="8" placeholder="<?php printf( '%s: https://www.youtube.com/watch?v=XXXXXXXXXX', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>"/><?php echo esc_textarea( $post_meta['exclude'] ); ?></textarea>
                    <p class="description"><?php esc_html_e( 'Enter the list of video URLs those should be excluded during the import. Enter one video per line.', 'all-in-one-video-gallery' ); ?></p>
       			</div>
      		</td>
    	</tr>

        <tr class="aiovg-automations-field-order">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Order By', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>        
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Order By', 'all-in-one-video-gallery' ); ?></strong></p>
      			<select name="order" id="aiovg-order" class="select">
                	<?php 
					$options = array(
                        'date'      => esc_html__( 'Date', 'all-in-one-video-gallery' ),
						'rating'    => esc_html__( 'Rating', 'all-in-one-video-gallery' ),
						'relevance' => esc_html__( 'Relevance', 'all-in-one-video-gallery' ),
						'title'     => esc_html__( 'Title', 'all-in-one-video-gallery' ),
						'viewCount' => esc_html__( 'Views Count', 'all-in-one-video-gallery' )
                    );

					foreach ( $options as $key => $label ) {
						printf( '<option value="%s"%s>%s</option>', $key, selected( $key, $post_meta['order'], false ), $label );
					}
					?>
        		</select>
      		</td>
    	</tr>  

        <tr class="aiovg-automations-field-limit">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Batch Limit', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Batch Limit', 'all-in-one-video-gallery' ); ?></strong></p>
      			<div class="aiovg-input-wrap">
          			<input type="text" name="limit" id="aiovg-limit" class="text" value="<?php echo esc_attr( $post_meta['limit'] ); ?>" />
					<p class="description"><?php esc_html_e( 'Enter the maximum amount of videos to be imported per batch. We recommend keeping this value less than 500.', 'all-in-one-video-gallery' ); ?></p>
       			</div>
      		</td>
    	</tr> 

        <tr class="aiovg-automations-field-schedule">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Schedule', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>        
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Schedule', 'all-in-one-video-gallery' ); ?></strong></p>
      			<select name="schedule" id="aiovg-schedule" class="select">
                	<?php 
					$options = array(
                        '0'       => esc_html__( 'Only Once', 'all-in-one-video-gallery' ),
						'3600'    => esc_html__( 'Every 1 Hour', 'all-in-one-video-gallery' ),
						'86400'   => esc_html__( 'Every 1 Day', 'all-in-one-video-gallery' ),
						'604800'  => esc_html__( 'Every 1 Week', 'all-in-one-video-gallery' ),
						'2419200' => esc_html__( 'Every 1 Month', 'all-in-one-video-gallery' )
                    );

					foreach ( $options as $key => $label ) {
						printf( '<option value="%s"%s>%s</option>', $key, selected( $key, $post_meta['schedule'], false ), $label );
					}
					?>
        		</select>
				<p class="description"><?php esc_html_e( 'Configure how frequent the plugin the plugin should import videos.', 'all-in-one-video-gallery' ); ?></p>
      		</td>
    	</tr>

		<tr class="aiovg-automations-field-reschedule">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Reschedule', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>        
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg"><strong><?php esc_html_e( 'Reschedule', 'all-in-one-video-gallery' ); ?></strong></p>
				<label>
					<input type="checkbox" name="reschedule" value="1" <?php checked( $post_meta['reschedule'], 1 ); ?> />
					<?php esc_html_e( 'Check this option if the plugin should check for new videos after the import has been completed.', 'all-in-one-video-gallery' ); ?>
				</label>
      		</td>
    	</tr>   
  	</tbody>
</table>

<?php
// Add a nonce field
wp_nonce_field( 'aiovg_save_automations_sources', 'aiovg_automations_sources_nonce' );