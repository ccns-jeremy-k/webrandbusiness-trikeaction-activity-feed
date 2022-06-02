<?php

/**
 * Videos: Slider Template (slider_layout = player).
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
    'slider_autoplay'    => (int) $attributes['slider_autoplay']
);

$slick = array(
    'autoplay'      => ! empty( $attributes['slider_autoplay'] ) ? true : false,
    'autoplaySpeed' => (int) $attributes['autoplay_speed'],
    'arrows'        => ! empty( $attributes['arrows'] ) ? true : false,
    'dots'          => ! empty( $attributes['dots'] ) ? true : false
);
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
    <div id="aiovg-slider-player-<?php echo esc_attr( $attributes['uid'] ); ?>" class="aiovg-slider-player aiovg-slick" data-type="player" data-slick='<?php echo wp_json_encode( $slick ); ?>' data-params='<?php echo wp_json_encode( $data_params ); ?>'>
        <?php
        $i = 0;

        // Start the loop            
        while ( $aiovg_query->have_posts() ) :        
            $aiovg_query->the_post(); 
            $player_url = aiovg_get_player_page_url( $post->ID );
            ?>            
            <div class="aiovg-slick-item">                    
                <?php 
                // Player
                if ( 0 == $i ) {
                    $player = aiovg_get_player_html( $post->ID, array( 'player' => 'iframe', 'width' => '' ) );
                } else {
                    $player = sprintf(
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
                        '<div class="aiovg-excerpt">%s</div>',
                        wp_kses_post( aiovg_get_excerpt( $post->ID, $attributes['excerpt_length'] ) )
                    );
                }

                if ( ! empty( $content ) ) {
                    $content = sprintf(
                        '<div class="aiovg-caption">%s</div>',
                        implode( '', $content )
                    );
                }

                // ...
                if ( ! empty( $content ) ) {
                    printf(
                        '<div class="aiovg-row"><div class="aiovg-col aiovg-col-1-6">%s</div><div class="aiovg-col aiovg-col-1-4">%s</div></div>',
                        $player,
                        $content
                    );
                } else {
                    echo $player;
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
</div>