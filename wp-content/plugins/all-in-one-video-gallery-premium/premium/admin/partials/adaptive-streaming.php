<?php

/**
 * Fields: Adaptive / Live Streaming
 *
 * @link       https://plugins360.com
 * @since      1.5.7
 *
 * @package    All_In_One_Video_Gallery
 * @subpackage All_In_One_Video_Gallery/premium
 */
?>

<tr class="aiovg-toggle-fields aiovg-type-adaptive">
    <td class="label aiovg-hidden-xs">
        <label><?php esc_html_e( 'HLS', 'all-in-one-video-gallery' ); ?></label>
    </td>
    <td>
        <p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg">
            <strong><?php esc_html_e( 'HLS', 'all-in-one-video-gallery' ); ?></strong>
        </p>
        
        <div class="aiovg-input-wrap">
            <input type="text" name="hls" id="aiovg-hls" class="text" placeholder="<?php printf( '%s: https://www.mysite.com/stream.m3u8', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $hls ); ?>" />
        </div>
    </td>
</tr>
<tr class="aiovg-toggle-fields aiovg-type-adaptive">
    <td class="label aiovg-hidden-xs">
        <label><?php esc_html_e( 'M(PEG)-DASH', 'all-in-one-video-gallery' ); ?></label>
    </td>
    <td>
        <p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg">
            <strong><?php esc_html_e( 'M(PEG)-DASH', 'all-in-one-video-gallery' ); ?></strong>
        </p>

        <div class="aiovg-input-wrap">
            <input type="text" name="dash" id="aiovg-dash" class="text" placeholder="<?php printf( '%s: https://www.mysite.com/stream.mpd', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $dash ); ?>" />
        </div>
    </td>
</tr>
