<?php
require dirname(__FILE__) . "/feed.php";

/**
 * Trikeaction Activities
 *
 * Usage: [trikeaction_activities limit=15 order=DESC]
 *
 * @param $attrs
 * @return string
 */

function taa_get_activities($attrs): string
{
    if (! defined('REST_REQUEST')) {
        new Feed($attrs);
    }

    return '';
}

function taa_init($attrs): string
{
    taa_add_activities_styles_and_scripts();
    return taa_get_activities($attrs);
}

function taa_add_activities_styles_and_scripts(): void
{
    wp_enqueue_script('taa_activities_scripts', dirname(__FILE__) . '/taa_activities.js', ['jquery'], null, $footer = true);
    wp_enqueue_style('taa_activities_styles', dirname(__FILE__) . '/taa_activities.css', [], null);
}

add_shortcode('trikeaction_activities', 'taa_init');
