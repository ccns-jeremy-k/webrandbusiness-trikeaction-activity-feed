<?php

/**
 * Videos: Slider Template.
 *
 * @link       https://plugins360.com
 * @since      1.0.0
 *
 * @package    All_In_One_Video_Gallery
 * @subpackage All_In_One_Video_Gallery/premium
 */

switch ( $attributes['slider_layout'] ) {
    case 'player':
        include AIOVG_PLUGIN_DIR . 'premium/public/templates/videos-template-slider-a.php';
        break;
    case 'thumbnails':
        include AIOVG_PLUGIN_DIR . 'premium/public/templates/videos-template-slider-b.php';
        break;
    case 'popup':
        include AIOVG_PLUGIN_DIR . 'premium/public/templates/videos-template-slider-c.php';
        break;
    case 'both':
        include AIOVG_PLUGIN_DIR . 'premium/public/templates/videos-template-slider-d.php';
        break;
}
