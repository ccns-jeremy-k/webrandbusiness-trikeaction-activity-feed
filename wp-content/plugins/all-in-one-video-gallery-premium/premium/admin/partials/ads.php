<?php

/**
 * "Video Ads" metabox.
 *
 * @link       https://plugins360.com
 * @since      1.5.7
 *
 * @package    All_In_One_Video_Gallery
 * @subpackage All_In_One_Video_Gallery/premium
 */
?>
    
<div class="wrap"> 
	<label>
    	<input type="checkbox" name="disable_ads" value="1" <?php checked( $disable_ads, 1 ); ?> />
        <strong><?php esc_html_e( 'Disable Ads', 'all-in-one-video-gallery' ); ?></strong>
   	</label>
</div>

<div class="wrap"> 
	<label>
    	<input type="checkbox" id="override_vast_url" name="override_vast_url" value="1" <?php checked( $override_vast_url, 1 ); ?> />
        <?php esc_html_e( 'Override VAST URL', 'all-in-one-video-gallery' ); ?>		
   	</label>
</div>

<div id="vast_url" class="wrap" style="display: none;">
	<input type="text" name="vast_url" class="text widefat" placeholder="<?php esc_attr_e( 'Enter your VAST URL', 'all-in-one-video-gallery' ); ?>" value="<?php echo esc_attr( $vast_url ); ?>" />
</div>

<?php
// Add a nonce field
wp_nonce_field( 'aiovg_video_save_ads', 'aiovg_video_ads_nonce' );