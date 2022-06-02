<?php

/**
 * Videos: Slider Template (slider_layout = both).
 *
 * @link       https://plugins360.com
 * @since      2.4.4
 *
 * @package    All_In_One_Video_Gallery
 * @subpackage All_In_One_Video_Gallery/premium
 */

$player_settings = get_option( 'aiovg_player_settings' );
$player_ratio = ! empty( $player_settings['ratio'] ) ? (float) $player_settings['ratio'] . '%' : '56.25%';

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
    'link_title'         => (int) $attributes['link_title']
);

$slick = array(
    'videos'     => array(
        'arrows' => ! empty( $attributes['arrows'] ) ? true : false,
        'dots'   => ! empty( $attributes['dots'] ) ? true : false
    ),
    'thumbnails' => array(
        'slidesToShow'   => (int) $attributes['columns'],
	    'slidesToScroll' => 1,
        'arrows'         => ! empty( $attributes['arrows'] ) ? true : false,
        'dots'           => ! empty( $attributes['dots'] ) ? true : false,
        'responsive'     => array()
    )
);

if ( (int) $attributes['columns'] > 3 ) {
    $slick['thumbnails']['responsive'][] = array(
        'breakpoint' => 768,
        'settings'   => array(
            'slidesToShow' => 3
        )
    );
}

if ( (int) $attributes['columns'] > 2 ) {
    $slick['thumbnails']['responsive'][] = array(
        'breakpoint' => 640,
        'settings'   => array(
            'slidesToShow' => 1,
            'centerMode'   => true
        )
    );
}

if ( 'both' == $attributes['slider_layout'] ) {	
    $slick['videos'] = array(
        'asNavFor' => '#aiovg-slider-thumbnails-' . $attributes['uid'],
        'arrows'   => false,
        'dots'     => false,
        'fade'     => true
    );
    
    $slick['thumbnails']['asNavFor'] = '#aiovg-slider-player-' . $attributes['uid'];
    $slick['thumbnails']['focusOnSelect'] = true;
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
    
    <!-- Videos -->
    <div id="aiovg-slider-player-<?php echo esc_attr( $attributes['uid'] ); ?>" class="aiovg-slider-player aiovg-slick" data-type="player" data-slick='<?php echo wp_json_encode( $slick['videos'] ); ?>' data-params='<?php echo wp_json_encode( $data_params ); ?>'>
        <?php
        $i = 0;

        // Start the loop            
        while ( $aiovg_query->have_posts() ) :        
            $aiovg_query->the_post(); 
            $player_url = aiovg_get_player_page_url( $post->ID );
            ?>            
            <div class="aiovg-slick-item">                    
                <?php 
                if ( 0 == $i ) {
                    the_aiovg_player( $post->ID, array( 'player' => 'iframe', 'width' => '' ) );
                } else {
                    printf(
                        '<div class="aiovg-player-container"><div class="aiovg-player" style="padding-bottom: %s" data-src="%s"></div></div>',
                        $player_ratio,
                        esc_url( $player_url )
                    );
                }

                // Content
                $content = array();

                if ( ! empty( $attributes['show_player_title'] ) ) {
                    $classes = array( 'aiovg-link-title' );

                    if ( empty( $attributes['link_title'] ) ) {
                        $classes[] = 'aiovg-disable-mouse-events';
                    }

                    $content[] = sprintf(
                        '<div class="aiovg-title"><a href="%s" class="%s">%s</a></div>',                        
                        esc_url( get_permalink() ),
                        implode( ' ', $classes ),
                        esc_html( get_the_title() )                            
                    );
                }

                if ( ! empty( $attributes['show_player_description'] ) ) {
                    $content[] = sprintf(
                        '<div class="aiovg-description">%s</div>',
                        wp_kses_post( $post->post_content )
                    );
                }

                if ( ! empty( $content ) ) {
                    printf(
                        '<div class="aiovg-caption">%s</div>',
                        implode( '', $content )
                    );
                }
                ?>
            </div>            
            <?php 
            ++$i;

        // End of the loop
        endwhile;
            
        // Use reset postdata to restore orginal query
        wp_reset_postdata();
        ?>
    </div>

    <!-- Thumbnails -->
    <div id="aiovg-slider-thumbnails-<?php esc_attr_e( $attributes['uid'] ); ?>" class="aiovg-slider-thumbnails aiovg-slick" data-type="thumbnails" data-slick='<?php echo wp_json_encode( $slick['thumbnails'] ); ?>' data-params='<?php echo wp_json_encode( $data_params ); ?>'>
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