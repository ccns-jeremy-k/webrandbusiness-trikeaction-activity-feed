<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.0.5.3
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="directorist-search-field">

	<?php if ( !empty($data['label']) ): ?>
		<label><?php echo esc_html( $data['label'] ); ?></label>
	<?php endif; ?>

	<div class="directorist-form-group">
		<input class="directorist-form-element" type="time" name="custom_field[<?php echo esc_attr( $data['field_key'] ); ?>]" value="<?php echo esc_attr( $value ); ?>" <?php echo ! empty( $data['required'] ) ? 'required="required"' : ''; ?>>
	</div>

</div>