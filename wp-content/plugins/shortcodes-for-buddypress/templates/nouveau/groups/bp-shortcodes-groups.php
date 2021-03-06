<?php
/**
 * BP Nouveau - Groups Directory
 *
 * @since 3.0.0
 * @version 3.0.0
 */
global $groups_atts;
?>
<div id="buddypress" class="buddypress-wrap bp-dir-hori-nav <?php echo esc_attr( $groups_atts['container_class'] ); ?>">

	<?php bp_nouveau_before_groups_directory_content(); ?>

	<?php bp_nouveau_template_notices(); ?>

	<?php // if ( ! bp_nouveau_is_object_nav_in_sidebar() ) : ?>

		<?php // bp_get_template_part( 'common/nav/directory-nav' ); ?>

	<?php // endif; ?>

	<div class="screen-content">

		<input type="hidden" data-bp-filter="groups" value="<?php echo esc_attr( $groups_atts['bpsh_query'] ); ?>" />		
		<?php // bp_get_template_part( 'common/search-and-filters-bar' ); ?>
		
		
		
		<div id="groups-dir-list" class="groups dir-list" data-bp-list="groups">
			<div id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'directory-groups-loading' ); ?></div>
		</div><!-- #groups-dir-list -->

	<?php bp_nouveau_after_groups_directory_content(); ?>
	</div><!-- // .screen-content -->
</div>

