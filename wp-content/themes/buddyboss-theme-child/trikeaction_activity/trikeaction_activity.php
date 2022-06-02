<?php
require dirname(__FILE__)."/feed.php";

add_filter('bp_has_activities', 'bp_has_activities_sort_desc');

function taa_get_activities($attrs): string
{
    global $wpdb;
    parse_str(str_replace("&amp;", "&", $attrs['filters']), $filters);
    $sort = $attrs['sort'] ?? 'DESC';
    $limit = $attrs['limit'] ?? 15;

    $activities = $wpdb->get_results(
        "SELECT activity_id, user_id, user_nicename, user_email, primary_link, type, ci_bp_activity.action, content, date_recorded FROM {$wpdb->prefix}bp_activity LEFT JOIN ci_bp_activity_meta cbam on ci_bp_activity.id = cbam.activity_id LEFT JOIN ci_users cu on ci_bp_activity.user_id = cu.ID WHERE hide_sitewide = 0 AND is_spam = 0 ORDER BY activity_id {$sort} LIMIT {$limit};"
    );

    $taf = new Feed($activities);
    echo $taf->createTimeline($activities);

    return '';
}



add_shortcode('trikeaction_activities', 'taa_get_activities');
