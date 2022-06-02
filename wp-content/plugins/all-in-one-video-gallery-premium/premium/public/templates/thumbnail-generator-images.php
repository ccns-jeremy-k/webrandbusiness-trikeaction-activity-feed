<?php

/**
 * Thumbnail Generator Images.
 *
 * @link       https://plugins360.com
 * @since      1.6.6
 *
 * @package    All_In_One_Video_Gallery
 * @subpackage All_In_One_Video_Gallery/premium
 */

foreach ( $attributes['images'] as $index => $image ) : ?>
	<div class="aiovg-item aiovg-item-thumbnail">
		<div class="aiovg-item-inner">
			<img src="<?php echo esc_url( $image ); ?>" />

			<div class="aiovg-item-option">
				<input type="radio" name="image_option" value="<?php echo esc_url( $image ); ?>" />
				<input type="hidden" name="images[]" value="<?php echo esc_url( $image ); ?>" />
			</div>
		</div>
	</div>
<?php endforeach;