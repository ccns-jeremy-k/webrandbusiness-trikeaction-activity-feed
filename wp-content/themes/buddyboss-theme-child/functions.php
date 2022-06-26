<?php
/**
 * @package BuddyBoss Child
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */


/****************************** THEME SETUP ******************************/

/**
 * Sets up theme for translation
 *
 * @since BuddyBoss Child 1.0.0
 */
function buddyboss_theme_child_languages()
{
    /**
     * Makes child theme available for translation.
     * Translations can be added into the /languages/ directory.
     */

    // Translate text from the PARENT theme.
    load_theme_textdomain('buddyboss-theme', get_stylesheet_directory() . '/languages');

    // Translate text from the CHILD theme only.
    // Change 'buddyboss-theme' instances in all child theme files to 'buddyboss-theme-child'.
    // load_theme_textdomain( 'buddyboss-theme-child', get_stylesheet_directory() . '/languages' );

}

add_action('after_setup_theme', 'buddyboss_theme_child_languages');

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function buddyboss_theme_child_scripts_styles()
{
    /**
     * Scripts and Styles loaded by the parent theme can be unloaded if needed
     * using wp_deregister_script or wp_deregister_style.
     *
     * See the WordPress Codex for more information about those functions:
     * http://codex.wordpress.org/Function_Reference/wp_deregister_script
     * http://codex.wordpress.org/Function_Reference/wp_deregister_style
     **/

    // Styles
    wp_enqueue_style('buddyboss-child-css', get_stylesheet_directory_uri() . '/assets/css/custom.css', '', '1.0.0');

    // Javascript
    wp_enqueue_script('buddyboss-child-js', get_stylesheet_directory_uri() . '/assets/js/custom.js', '', '1.0.0');
}

add_action('wp_enqueue_scripts', 'buddyboss_theme_child_scripts_styles', 9999);


/****************************** CUSTOM FUNCTIONS ******************************/

// Add your own custom functions here
function redirect_to_profile($redirect_to_calculated, $redirect_url_specified, $user)
{
    if (! $user || is_wp_error($user)) {
        return $redirect_to_calculated;
    }
// If the redirect is not specified, assume it to be dashboard.
    if (empty($redirect_to_calculated)) {
        $redirect_to_calculated = admin_url();
    }
// if the user is not site admin, redirect to his/her profile.
    if (function_exists('bp_core_get_user_domain') && ! is_super_admin($user->ID)) {
        return bp_core_get_user_domain($user->ID) . "/activity/";
    }
// if site admin or not logged in, do not do anything much.
    return $redirect_to_calculated;
}

add_filter('login_redirect', 'redirect_to_profile', 100, 3);
function aiovg_custom_init()
{
    add_post_type_support('aiovg_videos', 'thumbnail');
}

add_action('init', 'aiovg_custom_init', 11);
add_action('wp_enqueue_scripts', 'enqueue_load_fa');
function enqueue_load_fa()
{
    wp_enqueue_style('load-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
}

function my_excerpt_length($length)
{
    return 35;
}

add_filter('excerpt_length', 'my_excerpt_length');


function bp_has_activities_sort_desc($has)
{
    global $activities_template;
    foreach ($activities_template->activities as &$a) {
        if ($a->children->children) {
            $a->children = bp_has_activities_sort_desc($a->children->children);
        }

        $a->children = array_reverse($a->children);
    }
    return $has;
}

add_action('comment_post', 'create_new_activity', 10, 3);
function create_new_activity($comment_id, $approved, $commend_data): void
{
    $post = get_post($commend_data['comment_post_ID']);
    if ($post->post_type == 'aiovg_videos') {
        global $wpdb;
        $user = get_user_by('ID', bp_loggedin_user_id());
        $action = "<a href=\"".get_site_url()."/members/{$user->user_nicename}/\">{$user->display_name}</a> commented on a video <a href=\"{$post->guid}\">`{$post->post_title}</a>`";
        $content = $commend_data['comment_content'];
        $component = 'activity';
        $type = "video_comment";
        $primary_link = $post->guid;
        $user_id = bp_loggedin_user_id();
        $item_id =  $post->ID;
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


include get_stylesheet_directory() . "/trikeaction_activity/trikeaction_activity.php"
?>
