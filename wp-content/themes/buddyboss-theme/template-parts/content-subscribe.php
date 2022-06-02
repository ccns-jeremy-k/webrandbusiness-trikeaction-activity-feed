<?php
/**
 * Template part for displaying subscribe content
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package BuddyBoss_Theme
 */

if ( is_single() && ! is_related_posts() ) {

	$blog_newsletter_sign = buddyboss_theme_get_option( 'blog_newsletter_switch' );

	if ( ! empty( $blog_newsletter_sign ) ) {
		$blog_shortcode = buddyboss_theme_get_option( 'blog_shortcode' );
		?>
		<div class="bb-subscribe-wrap">
			<div class="bb-subscribe-content">
				<div class="bb-subscribe-data">
				<?php
				if ( ! empty( $blog_shortcode ) ) {
					echo do_shortcode( $blog_shortcode );
				}
				?>
				</div>
				<span class="bb-subscribe-figure"><i class="bb-icon-l bb-icon-envelope-open"></i></span>
			</div>
		</div>
		<?php
	}
}
