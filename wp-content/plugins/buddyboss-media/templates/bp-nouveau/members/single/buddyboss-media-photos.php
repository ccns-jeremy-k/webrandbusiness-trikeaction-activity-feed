<?php
/**
 * BuddyPress Activity templates
 *
 * @since 2.3.0
 */

?>

<?php bp_nouveau_before_activity_directory_content(); ?>

<?php if ( is_user_logged_in() ) : ?>

	<?php bp_get_template_part( 'activity/post-form' ); ?>

<?php endif; ?>

<?php bp_nouveau_template_notices(); ?>

<?php if ( ! bp_nouveau_is_object_nav_in_sidebar() ) : ?>

	<?php bp_get_template_part( 'common/nav/directory-nav' ); ?>

<?php endif; ?>

<div class="screen-content">

	<div id="activity-stream" class="activity" data-bp-list="activity">
		<div id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'directory-activity-loading' ); ?></div>
	</div><!-- .activity -->

	<?php bp_nouveau_after_activity_directory_content(); ?>

</div><!-- // .screen-content -->

