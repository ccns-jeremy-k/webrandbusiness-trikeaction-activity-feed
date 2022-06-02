<?php

class feed
{
    public $DEFAULT_AVATAR;
    public $DEFAULT_USER = "Deleted User";

    private $types = [
        'bbp_topic_create' => "new discussion",
        'bbp_reply_create' => "new comment on discussion",
        'activity_comment' => "new comment",
        'activity_update' => "new image uploaded",
        'new_blog_aiovg_videos' => "new video posted"
    ];

    public function __construct()
    {
        echo $this->DEFAULT_AVATAR;
        $this->DEFAULT_AVATAR = $this->get_default_profile_avatar();
    }

    public function get_default_profile_avatar()
    {
        global $wpdb;
        return $wpdb->get_results("SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = 'bp-default-custom-profile-avatar'")[0]->option_value;
    }

    public function createTimeline($activities): string
    {
        $timeline = "
            <div 
                id='activity-stream' 
                class='activity' 
                data-bp-list='activity'
            >
                <ul class='activity-list item-list bp-list'>
        ";
        foreach ($activities as $activity) {
            $timeline .= $this->createTimelineElement($activity);
        }
        $timeline .= "</ul></div>";
        return $timeline;
    }


    private function bbp_topic_create($activity) {}
    private function bbp_reply_create($activity) {}
    private function activity_comment($activity) {}
    private function activity_update($activity) {}
    private function new_blog_aiovg_videos($activity ) {}

    private function createTimelineElement($activity): string
    {
        $a = "<li 
            class='bbpress bbp_reply_create activity-item' 
            id='activity-{$activity->activity_id}' 
            data-bp-activity-id='{$activity->activity_id}' 
            data-bp-timestamp=''> ";
        $a .= $this->types[$activity->type];
        $a .= json_encode($activity, JSON_PRETTY_PRINT);
        $a .= "</li>";

        return $a;




//        return "
//        <li
//            class='bbpress bbp_reply_create activity-item'
//            id='activity-{$activity->activity_id}'
//            data-bp-activity-id='{$activity->activity_id}'
//            data-bp-timestamp=''>
//           	    <div
//           	        class='bb-activity-more-options-wrap action'>
//           	            <span
//           	                class='bb-activity-more-options-action'
//           	                data-balloon-pos='up'
//           	                data-balloon='More Options
//           	           '>
//           	                <i class='bb-icon-f bb-icon-ellipsis-h'></i>
//           	            </span>
//           	            <div class='bb-activity-more-options'>
//           	                <div class='generic-button'>
//           	                    <a href='http://localhost:8000/news-feed/delete/{$activity->activity_id}/?_wpnonce=46bf0dc757' class='button item-button bp-secondary-action delete-activity confirm'>
//           	                        <span class='bp-screen-reader-text'>Delete</span>
//           	                        <span class='delete-label'>Delete</span>
//           	                    </a>
//           	                </div>
//           	            </div>
//           	    </div>
//	            <div class='bp-activity-head'>
//		            <div class='activity-avatar item-avatar'>
//			            <a href='{$activity->primary_link}'>
//			                <img
//			                    src='{$this->DEFAULT_AVATAR}'
//			                    class='avatar user-3-avatar avatar-300 photo'
//			                    width='300' height='300'
//			                    alt='Profile photo of Antawn'/>
//			            </a>
//		            </div>
//
//                    <div class='activity-header'>
//                        <p>
//                            {$activity->action}
//                            <a href='http://localhost:8000/forums/forum/trike-discussion/'>Trike Discussion</a>
//                            <a href='https://clean.trikeaction.com/forums/discussion/so-many-things-to-discuss-where-to-start/#post-1776' class='view activity-time-since'>
//                                <span class='time-since' data-livestamp='".str_replace(" ", "T", $activity->date_recorded)."+0000'>".$this->time_elapsed_string($activity->date_recorded)."</span>
//                            </a>
//                        </p>
//                        <p class='activity-date'>
//                            <a href='https://clean.trikeaction.com/forums/discussion/so-many-things-to-discuss-where-to-start/#post-1776'>{$this->time_elapsed_string($activity->date_recorded)}</a>
//                        </p>
//
//                    </div>
//                </div>
//
//                <div class='activity-content'>
//                    <div class='activity-inner'>
//                        <p class='activity-discussion-title-wrap'>
//                            <a href='http://localhost:8000/forums/discussion/so-many-things-to-discuss-where-to-start/#post-1776'>
//                                <span class='bb-reply-lable'>Reply to</span> So many things to discuss, where to start
//                            </a>
//                        </p>
//                        <div class='bb-content-inr-wrap'>
//                            <blockquote>
//                                <p>Great topic thanks for sharing! 😀 </p>
//                            </blockquote>
//                        </div>
//
//                        <div class='activity-inner-meta action'>
//                            <div class='generic-button'>
//                                <a
//                                    class='button bb-icon-l bb-icon-comments-square bp-secondary-action'
//                                    aria-expanded='false'
//                                    href='http://localhost:8000/forums/discussion/so-many-things-to-discuss-where-to-start/#post-1776'
//                                >
//                                    <span class='bp-screen-reader-text'>Join Discussion</span>
//                                    <span class='comment-count'>Join Discussion</span>
//                                </a>
//                            </div>
//                        </div>
//                    </div>
//
//                    <div class='activity-state  '>
//                        <a href='javascript:void(0);' class='activity-state-likes'>
//                            <span
//                                class='like-text hint--bottom hint--medium hint--multiline'
//                                data-hint=''
//                            />
//                        </a>
//                        <span class='ac-state-separator'>·</span>
//                    </div>
//                </div>
//
//	            <div class='bp-generic-meta activity-meta action'>
//                    <div class='generic-button'>
//                        <a
//                            href='http://localhost:8000/news-feed/favorite/{$activity->activity_id}/'
//                            class='button fav bp-secondary-action'
//                            aria-pressed='false'
//                        >
//                            <span class='bp-screen-reader-text'>Like</span>
//                            <span class='like-count'>Like</span>
//                        </a>
//                    </div>
//                </div>
//        </li>
//    ";
    }



    private function time_elapsed_string($datetime, $full = false): string
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7)-1;
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}
