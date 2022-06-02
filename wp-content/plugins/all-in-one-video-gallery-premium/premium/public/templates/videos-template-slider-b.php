<?php

/**
 * Videos: Slider Template (slider_layout = thumbnails).
 *
 * @link       https://plugins360.com
 * @since      2.4.4
 *
 * @package    All_In_One_Video_Gallery
 * @subpackage All_In_One_Video_Gallery/premium
 */

$data_params = array(
    'is_rtl'             => is_rtl(),
    'arrow_size'         => (int) $attributes['arrow_size'] . 'px',
    'arrow_bg_color'     => sanitize_text_field( $attributes['arrow_bg_color'] ),
    'arrow_icon_size'    => (int) $attributes['arrow_size'] - 5 . 'px',
    'arrow_icon_color'   => sanitize_text_field( $attributes['arrow_icon_color'] ),		
    'arrow_radius'       => (int) $attributes['arrow_radius'] . 'px',
    'arrow_top_offset'   => (int) $attributes['arrow_top_offset'] . '%',
    'arrow_left_offset'  => (int) $attributes['arrow_left_offset'] . 'px',
    'arrow_right_offset' => (int) $attributes['arrow_right_offset'] . 'px',
    'dot_size'           => (int) $attributes['dot_size'] . 'px',
    'dot_color'          => sanitize_text_field( $attributes['dot_color'] ),
    'slider_autoplay'    => (int) $attributes['slider_autoplay']
);

$slick = array(
    'slidesToShow'   => (int) $attributes['columns'],
    'slidesToScroll' => 1,
    'autoplay'       => ! empty( $attributes['slider_autoplay'] ) ? true : false,
    'autoplaySpeed'  => (int) $attributes['autoplay_speed'],
    'arrows'         => ! empty( $attributes['arrows'] ) ? true : false,
    'dots'           => ! empty( $attributes['dots'] ) ? true : false,
    'responsive'     => array()
);

if ( (int) $attributes['columns'] > 3 ) {
    $slick['responsive'][] = array(
        'breakpoint' => 768,
        'settings'   => array(
            'slidesToShow' => 3
        )
    );
}

if ( (int) $attributes['columns'] > 2 ) {
    $slick['responsive'][] = array(
        'breakpoint' => 640,
        'settings'   => array(
            'slidesToShow' => 1,
            'centerMode'   => true
        )
    );
}
?>

<div class="aiovg aiovg-videos aiovg-videos-template-slider aiovg-slider-layout-<?php echo esc_attr( $attributes['slider_layout'] ); ?>">
	<?php
	// Display the videos count
    if ( ! empty( $attributes['show_count'] ) ) : ?>
    	<div class="aiovg-header">
			<?php printf( esc_html__( "%d video(s) found", 'all-in-one-video-gallery' ), $attributes['count'] ); ?>
        </div>
    <?php endif;
                    
    // Display the title (if applicable)
    if ( ! empty( $attributes['title'] ) ) : ?>
        <h3><?php echo esc_html( $attributes['title'] ); ?></h3>
    <?php endif; ?>    
    
    <!-- Thumbnails -->
    <div id="aiovg-slider-thumbnails-<?php esc_attr_e( $attributes['uid'] ); ?>" class="aiovg-slider-thumbnails aiovg-slick" data-type="thumbnails" data-slick='<?php echo wp_json_encode( $slick ); ?>' data-params='<?php echo wp_json_encode( $data_params ); ?>'>
        <?php
        // Start the loop            
        while ( $aiovg_query->have_posts() ) :        
            $aiovg_query->the_post(); 
            ?>            
            <div class="aiovg-slick-item">
                <?php the_aiovg_video_thumbnail( $post, $attributes ); ?>
            </div>            
            <?php           
        // End of the loop
        endwhile;
            
        // Use reset postdata to restore orginal query
        wp_reset_postdata();
        ?>
    </div>
</div>