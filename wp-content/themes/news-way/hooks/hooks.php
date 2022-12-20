<?php //Front Page Banner
if (!function_exists('newsway_front_page_banner_section')) :
    /**
     *
     * @since Newsway
     *
     */
    function newsway_front_page_banner_section()
    {
        if (is_front_page() || is_home()) {
         

            $main_banner_section_background_image = newsup_get_option('main_banner_section_background_image');
            $main_banner_section_background_image_url = wp_get_attachment_image_src($main_banner_section_background_image, 'full');
        if(!empty($main_banner_section_background_image)){ ?>
        <section class="mg-fea-area over" style="background-image:url('<?php echo esc_url($main_banner_section_background_image_url[0]); ?>');">
        <?php }else{ ?>
        <section class="mg-fea-area">
        <?php  } ?>
            <div class="overlay">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col"><?php dynamic_sidebar( 'front-page-canvas-sidebar' ); ?></div>
                    </div>
                </div>
            </div>
        </section>
        <?php }
    }
endif;
add_action('newsway_action_front_page_main_section_1', 'newsway_front_page_banner_section', 40);