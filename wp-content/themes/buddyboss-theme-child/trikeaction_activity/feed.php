<?php

class feed
{
    private bool $hide_comments = false;
    private array $dataset;

    /**
     * Get activity items, as specified by parameters.
     *
     * @param array $args {
     *     An array of arguments. All items are optional.
     * @type int $page Which page of results to fetch. Using page=1 without per_page will result
     *                                           in no pagination. Default: 1.
     * @type int|bool $per_page Number of results per page. Default: 25.
     * @type int|bool $max Maximum number of results to return. Default: false (unlimited).
     * @type string $fields Activity fields to return. Pass 'ids' to get only the activity IDs.
     *                                           'all' returns full activity objects.
     * @type string $sort ASC or DESC. Default: 'DESC'.
     * @type string $order_by Column to order results by.
     * @type array $exclude Array of activity IDs to exclude. Default: false.
     * @type array $in Array of ids to limit query by (IN). Default: false.
     * @type array $meta_query Array of meta_query conditions. See WP_Meta_Query::queries.
     * @type array $date_query Array of date_query conditions. See first parameter of
     *                                           WP_Date_Query::__construct().
     * @type array $filter_query Array of advanced query conditions. See BP_Activity_Query::__construct().
     * @type string|array $scope Pre-determined set of activity arguments.
     * @type array $filter See BP_Activity_Activity::get_filter_sql().
     * @type string $search_terms Limit results by a search term. Default: false.
     * @type string $privacy Limit results by a privacy. Default: public.
     * @type bool $display_comments Whether to include activity comments. Default: false.
     * @type bool $show_hidden Whether to show items marked hide_sitewide. Default: false.
     * @type string $spam Spam status. Default: 'ham_only'.
     * @type bool $update_meta_cache Whether to pre-fetch metadata for queried activity items. Default: true.
     * @type string|bool $count_total If true, an additional DB query is run to count the total activity items
     *                                           for the query. Default: false.
     * }
     * @return array The array returned has two keys:
     *               - 'total' is the count of located activities
     *               - 'activities' is an array of the located activities
     * @since BuddyPress 2.9.0 Introduced the `$order_by` parameter.
     *
     * @see BP_Activity_Activity::get_filter_sql() for a description of the
     *      'filter' parameter.
     * @see WP_Meta_Query::queries for a description of the 'meta_query'
     *      parameter format.
     *
     * @since BuddyPress 1.2.0
     * @since BuddyPress 2.4.0 Introduced the `$fields` parameter.
     */

    public function __construct($attrs)
    {
        if (! is_admin() && (! defined('DOING_AJAX') || ! DOING_AJAX)) {
            if ($attrs['hide_child_comments']) {
                $this->hide_comments = (bool)$attrs['hide_child_comments'];
                unset($attrs['hide_child_comments']);
            }
            $this->dataset = BP_Activity_Activity::get($attrs)['activities'];
            $this->create_activity_timeline();
        }
    }

    /**
     * @param mixed $default_avatar
     */
    public function set_default_avatar($default_avatar): void
    {
        $this->default_avatar = $default_avatar;
    }

    /**
     * @return mixed
     */
    public function get_profile_avatar($activity)
    {
        $avatar = get_avatar_url($activity->user_id, array('size' => 300));
        if (str_contains($avatar, 'no_profile')) {
            global $wpdb;
            $avatar = $wpdb->get_results("SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = 'bp-default-custom-profile-avatar'")[0]->option_value;
        }
        ?>
        <div class="activity-avatar item-avatar">
            <a href="<?=get_site_url()?>members/<?= $activity->user_nicename ?>/"><img
                        src="<?= $avatar ?>"
                        class="avatar user-8-avatar avatar-300 photo" width="300" height="300"
                        alt="Profile photo of <?= $activity->user_fullname ?>"></a>
        </div>
        <?php
    }

    public function get_activity_header($activity)
    {
        if ($activity->type === 'activity_comment') {
            if (str_contains($activity->primary_link, 'aiovg_videos')) {
                $activity->action = str_replace('photo', 'video', $activity->action);
                $activity->link = $activity->secondary_item_id !== 0 ? $activity->primary_link :  "?ac=<?= $activity->id ?>/#ac-form-<?= $activity->id ?>";
            }
        }
        ?>
        <div class="activity-header">
            <p>
                <?= $activity->action ?>
            </p>
            <p>
                    <a
                            href="<?=$activity->link?>/"
                            class="view activity-time-since"><span
                                class="time-since"
                                data-livestamp="<?= $activity->date_recorded ?>+0000"><?= bp_core_time_since($activity->date_recorded) ?></span>
                    </a>
            </p>
            <p class="activity-date">
                <a href="<?=get_site_url()?>/news-feed/p/<?= $activity->id ?>/"><?= bp_core_time_since($activity->date_recorded) ?></span></a>
            </p>

        </div>
        <?php
    }

    public function create_activity_timeline(): void
    {
        ?>
        <div id="buddypress" class="buddypress-wrap bp-dir-hori-nav activity">
            <ul class="activity-list item-list bp-list">
                <?php
                foreach ($this->dataset as $activity) {
                    ?>
                    <li class="activity <?= $activity->type ?> activity-item wp-link-embed"
                        id="activity-<?= $activity->id ?>" data-bp-activity-id="<?= $activity->id ?>"
                        data-bp-timestamp="<?= (DateTime::createFromFormat('Y-m-d H:i:s', $activity->date_recorded)->getTimestamp()) ?>"
                        data-bp-activity="<?= htmlspecialchars(json_encode($activity)) ?>">
                        <div class="bp-activity-head">
                            <?php $this->get_profile_avatar($activity);?>
                            <?php $this->get_activity_header($activity); ?>
                        </div>
                        <?php
                        if ($activity->type === 'mpp_media_upload') {
                            $this->activity_update($activity);
                        } else {
                            call_user_func(array($this, $activity->type), $activity);
                        }
                        if (! $this->hide_comments) {
                            $this->_get_activity_comments($activity);
                        }
                        ?>

                    </li>
                    <?php

                }
                ?>
            </ul>
        </div>
        <?php
    }

    private function activity_comment($activity)
    {
        ?>
            <div class="activity-content ">
                <div class="activity-inner ">
                    <?=$activity->content?>
                </div>
            </div>
        <?php

    }

    private function comment_comment($activity)
    {
        ?>
        <div class="activity-content ">
            <?= $activity->content ?>
        </div>

        <div class="bp-generic-meta activity-meta action">
            <div class="generic-button"><a href="<?=get_site_url()?>news-feed/favorite/<?= $activity->id ?>/"
                                           class="button fav bp-secondary-action" aria-pressed="false"><span
                            class="bp-screen-reader-text">Like</span> <span class="like-count">Like</span></a></div>
            <div class="generic-button"><a id="acomment-comment-<?= $activity->id ?>"
                                           class="button acomment-reply bp-primary-action"
                                           aria-expanded="false"
                                           href="?ac=<?= $activity->id ?>/#ac-form-<?= $activity->id ?>"
                                           role="button"><span
                            class="bp-screen-reader-text">Comment</span> <span class="comment-count">Comment</span></a>
            </div>
        </div>
        <?php
    }

    private function _get_media_attachment_id($media_id): int
    {
        global $wpdb;
        return (int)$wpdb->get_results("SELECT attachment_id FROM {$wpdb->prefix}bp_media WHERE id = {$media_id}")[0]->attachment_id;
    }

    private function _get_activity_comments($activity)
    {
        global $wpdb;
        $comments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bp_activity WHERE item_id = {$activity->id} AND type = 'activity_comment'");
        if (count($comments) > 0) {
            ?>
            <div class="activity-state  has-comments">
                <a href="<?= $activity->primary_link ?>" class="activity-state-comments"
                   style="border: none; color: #787878 !important;">
				<span class="comments-count">
					<?= count($comments) ?> Comments </span>
                </a>
            </div>
            <div class="activity-comments">
                <ul>
                    <?php
                    foreach ($comments as $comment):
                        ?>
                        <li id="acomment-<?= $comment->id ?>" class=" comment-item"
                            data-bp-activity-comment-id="<?= $comment->id ?>">
                            <?php
                            $user = get_user_by('id', $comment->user_id);
                            $this->get_profile_avatar($comment);
                            ?>

                            <div class="acomment-meta">
                                <a class="author-name"
                                   href="<?= $comment->primary_link ?>"><?= $user->user_nicename ?></a> <a
                                        href="<?=get_site_url()?>news-feed/p/102/#acomment-<?= $comment->id ?>"
                                        class="activity-time-since">
                                    <time class="time-since" datetime="<?= $comment->date_recorded ?>"
                                          data-bp-timestamp="<?= (DateTime::createFromFormat('Y-m-d H:i:s', $comment->date_recorded)->getTimestamp()) ?>"><?= bp_core_time_since($activity->date_recorded) ?></time>
                                </a>
                            </div>

                            <div class="acomment-content">
                                <?= $comment->content ?>
                            </div>

                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php
        }

    }

    private function activity_update($activity)
    {
        $media = bp_media_get_activity_media($activity->id);
        ?>

        <div class="activity-content  media-activity-wrap">
            <div class="activity-inner bb-empty-content">
                <?= $media['content'] ?? '' ?>
            </div>
        </div>

        <?php
    }

    private function new_blog_aiovg_videos($activity)
    {
        $image = get_post_meta($activity->secondary_item_id, 'image');
        ?>
        <div class="activity-content">
            <div class="activity-inner">
                <div class="bb-content-wrp">
                    <?= $activity->action ?>
                    </a>
                </div>
            </div>
            <a
                    aria-expanded="false"
                    href="<?= $activity->primary_link ?>">
                <img src="<?= $image[0] ?>">
            </a>
        </div>
        <?php
    }

    private function bbp_reply_create($activity)
    {
        ?>
        <div class="activity-content ">
            <div class="activity-inner ">
                <p class="activity-discussion-title-wrap"><?= $activity->action ?></p>
                <div class="bb-content-inr-wrap">
                    <?= $activity->content ?>
                </div>
                <div class="activity-inner-meta action">
                    <div class="generic-button"><a class="button bb-icon-l bb-icon-comments-square bp-secondary-action"
                                                   aria-expanded="false"
                                                   href="<?= $activity->primary_link ?>"><span
                                    class="bp-screen-reader-text">Join Discussion</span> <span class="comment-count">Join Discussion</span></a>
                    </div>
                </div>
            </div>

            <div class="activity-state  ">
                <a href="javascript:void(0);" class="activity-state-likes">
			<span class="like-text hint--bottom hint--medium hint--multiline" data-hint="">
							</span>
                </a>
                <span class="ac-state-separator">·</span>
            </div>
        </div>
        <div class="bp-generic-meta activity-meta action">
            <div class="generic-button"><a
                        href="<?=get_site_url()?>news-feed/favorite/<?= $activity->id ?>/?_wpnonce=9a8b278147"
                        class="button fav bp-secondary-action" aria-pressed="false"><span
                            class="bp-screen-reader-text">Like</span> <span class="like-count">Like</span></a></div>
        </div>
        <?php
    }

    private function bbp_topic_create($activity)
    {
        $user = get_user_by('id', $activity->user_id);
        $content = (strlen($activity->content) <= 160)
            ? $activity->content
            : substr($activity->content, 0, 157)."...</p>
                <p>
                    <span class=\"activity-read-more\" id=\"activity-read-more-{$activity->id}\"><a target=\"_blank\"
                                                                                      href=\"{$activity->primary_link}\"
                                                                                      rel=\"nofollow\"> Read more</a></span>
                    </p>"

        ?>
        <div class="activity-content ">
            <div class="activity-inner ">
                <p class="activity-discussion-title-wrap"><a
                            href="<?=get_site_url()?>forums/discussion/where-do-we-go-from-here/"> Where do we go
                        from here?</a></p>
                <div class="bb-content-inr-wrap">
                    <?=$content?>
                </div>
                <div class="activity-inner-meta action">
                    <div class="generic-button"><a class="button bb-icon-l bb-icon-comments-square bp-secondary-action"
                                                   aria-expanded="false"
                                                   href="<?=$activity->primary_link?>"><span
                                    class="bp-screen-reader-text">Join Discussion</span> <span class="comment-count">Join Discussion</span></a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }


    private function time_elapsed_string($datetime, $full = false): string
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7) - 1;
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

        if (! $full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    private function get_comments($post_id): object
    {
        $comments = get_comments(['post_id' => $post_id]);
        $video_comments = [];
        foreach ($comments as $comment) {
            if (in_array($comment['id'], array_column($this->dataset, 'item_id'))) {
                $this->dataset = (object)array_filter((array)$this->dataset, function ($activity) use ($comment) {
                    return ($activity['item_id'] !== $comment['id']);
                });
                $video_comments[] = $comment;
            }
        }

        return (object)$video_comments;
    }
}
