<?php

/**
 * Videos: Popup Template.
 *
 * @link       https://plugins360.com
 * @since      1.0.0
 *
 * @package    All_In_One_Video_Gallery
 * @subpackage All_In_One_Video_Gallery/premium
 */

$player_settings = get_option( 'aiovg_player_settings' );
$player_ratio = ! empty( $player_settings['ratio'] ) ? (float) $player_settings['ratio'] . '%' : '56.25%';

$data_params = array(
    'player_ratio' => $player_ratio,
    'show_title'   => (int) $attributes['show_player_title'],
    'link_title'   => (int) $attributes['link_title']
);
?>

<div id="aiovg-<?php echo esc_attr( $attributes['uid'] ); ?>" class="aiovg aiovg-videos aiovg-videos-template-popup" data-params='<?php echo wp_json_encode( $data_params ); ?>'>
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
    <?php 
    endif;
    
    // The loop
    echo '<div class="aiovg-grid aiovg-row">';
        
    while ( $aiovg_query->have_posts() ) :        
        $aiovg_query->the_post(); 
        $player_url = aiovg_get_player_page_url( $post->ID );          
        ?>            
        <div class="aiovg-col aiovg-col-<?php echo esc_attr( $attributes['columns'] ); ?>">
            <div class="aiovg-popup-item" data-mfp-src="<?php echo $player_url; ?>">
                <?php 
                the_aiovg_video_thumbnail( $post, $attributes );

                // Hidden
                if ( ! empty( $attributes['show_player_title'] ) ) {
                    printf(
                        '<div class="aiovg-hidden-title" style="display: none;">%s</div>',
                        esc_html( get_the_title() )
                    );
                }

                if ( ! empty( $attributes['show_player_description'] ) ) {
                    printf(
                        '<div class="aiovg-hidden-description" style="display: none;">%s</div>',
                        wp_kses_post( $post->post_content )
                    );
                }
                ?>
            </div>            
        </div>                
        <?php 
    endwhile;

    echo '</div>';
        
    // Use reset postdata to restore orginal query
    wp_reset_postdata();        
    
    if ( ! empty( $attributes['show_pagination'] ) ) { // Pagination
        the_aiovg_pagination( $aiovg_query->max_num_pages, "", $attributes['paged'], $attributes );
    } elseif ( ! empty( $attributes['show_more'] ) ) { // More button
        the_aiovg_more_button( $aiovg_query->max_num_pages, $attributes );
    }
    ?>
</div>