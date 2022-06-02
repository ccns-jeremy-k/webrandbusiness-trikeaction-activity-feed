<?php

/**
 * Popup.
 *
 * @link       https://plugins360.com
 * @since      1.5.7
 *
 * @package    All_In_One_Video_Gallery
 * @subpackage All_In_One_Video_Gallery/premium
 */
 
// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AIOVG_Premium_Public_Popup class.
 *
 * @since 1.5.7
 */
class AIOVG_Premium_Public_Popup {

	/**
	 * Register "Popup" template.
	 *
	 * @since  1.5.7
	 * @param  array $templates Core templates array.
	 * @return array $templates Updated templates array.
	 */
	public function add_popup_template( $templates ) {	
		$templates['popup'] = __( 'Popup', 'all-in-one-video-gallery' );
		return $templates;		
	}
	
	/**
	 * Get filtered php template file path.
	 *
	 * @since  1.5.7
	 * @param  array  $template   PHP file path.
	 * @param  array  $attributes An associative array of attributes.
	 * @return string             Filtered file path.
	 */
	public function load_template( $template, $attributes = array() ) {
		if ( 'videos-template-classic.php' == basename( $template ) ) {
			if ( 'popup' == $attributes['template'] ) {
				// Enqueue script / style dependencies
				wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-magnific-popup' );
				wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-premium-public' );
	
				wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-magnific-popup' );
				wp_enqueue_script( AIOVG_PLUGIN_SLUG . '-premium-public' );
			
				$template = AIOVG_PLUGIN_DIR . 'premium/public/templates/videos-template-popup.php';
			}
		}		

		return $template;
	}	

}
