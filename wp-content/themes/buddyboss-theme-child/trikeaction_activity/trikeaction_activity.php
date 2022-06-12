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
    add_action('comment_post', 'create_new_activity', 10, 3);
    taa_add_activities_styles_and_scripts();
    return taa_get_activities($attrs);
}

function create_new_activity($comment_id, $approved, $commend_data): void
{
    $post = get_post($commend_data['comment_post_ID']);
    if ($post->post_type == 'aiovg_videos') {
        global $wpdb;
        $user = get_user_by('ID', bp_loggedin_user_id());
        $action = "<a href=\"http://localhost:8000/members/{$user->user_nicename}/\">{$user->display_name}</a> commented on Video <a href=\"{$post->guid}\">`{$post->post_title}</a>`";
        $content = $commend_data['comment_content'];
        $component = 'activity';
        $type = "activity_comment";
        $primary_link = $post->guid;
        $user_id = bp_loggedin_user_id();
        $item_id = $post->ID;
        $recorded_time = bp_core_current_time();
        $privacy = 'public';
        $wpdb->query(
            $wpdb->prepare(
                "INSERT INTO {$wpdb->prefix}bp_activity (
                              user_id, 
                              component, 
                              type, 
                              action, 
                              content, 
                              primary_link, 
                              item_id, 
                              date_recorded, 
                              hide_sitewide, 
                              is_spam, 
                              privacy)
                       values (%d, %s, %s, %s, %s, %s, %d, %s, %s, %s, %s)",
                $user_id,
                $component,
                $type,
                $action,
                $content,
                $primary_link,
                $item_id,
                $recorded_time,
                false,
                false,
                $privacy
            )
        );
    }
}

function taa_add_activities_styles_and_scripts(): void
{
    wp_enqueue_script('taa_activities_scripts', dirname(__FILE__) . '/taa_activities.js', ['jquery'], null, $footer = true);
    wp_enqueue_style('taa_activities_styles', dirname(__FILE__) . '/taa_activities.css', [], null);
}

add_shortcode('trikeaction_activities', 'taa_init');
