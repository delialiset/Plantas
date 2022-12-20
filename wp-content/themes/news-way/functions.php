<?php
/**
 * Theme functions and definitions
 *
 * @package News-way
 */
if ( ! function_exists( 'newsway_enqueue_styles' ) ) :
	/**
	 * @since 0.1
	 */
	function newsway_enqueue_styles() {
		wp_enqueue_style( 'newsup-style-parent', get_template_directory_uri() . '/style.css' );
		wp_enqueue_style( 'newsway-style', get_stylesheet_directory_uri() . '/style.css', array( 'newsup-style-parent' ), '1.0' );
		wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.css');
		wp_dequeue_style( 'newsup-default',get_template_directory_uri() .'/css/colors/default.css');
		wp_enqueue_style( 'newsway-default-css', get_stylesheet_directory_uri()."/css/colors/default.css" );
		if(is_rtl()){
		wp_enqueue_style( 'newsup_style_rtl', trailingslashit( get_template_directory_uri() ) . 'style-rtl.css' );
	    }
		
	}

endif;
add_action( 'wp_enqueue_scripts', 'newsway_enqueue_styles', 9999 );


add_action( 'customize_register', 'newsway_customizer_rid_values', 1000 );
function newsway_customizer_rid_values($wp_customize) {

	$selective_refresh = isset( $wp_customize->selective_refresh ) ? 'postMessage' : 'refresh';

  $wp_customize->remove_control('main_banner_section_background_image');

  $wp_customize->remove_control('select_slider_news_category');

  $wp_customize->remove_control('show_main_news_section');

  $wp_customize->remove_control('main_slider_section_title');

  $wp_customize->remove_control('tabbed_section_title');

  $wp_customize->remove_control('latest_tab_title');

  $wp_customize->remove_control('popular_tab_title');

  $wp_customize->remove_control('trending_tab_title');

  $wp_customize->remove_control('select_trending_tab_news_category');

  $wp_customize->remove_control('banner_advertisement_section');

  $wp_customize->remove_control('banner_advertisement_section_url'); 

  $wp_customize->remove_control('newsup_open_on_new_tab');

  $wp_customize->remove_control('newsup_select_slider_setting'); 

  $wp_customize->remove_control('newsup_select_slider_setting');

  $wp_customize->selective_refresh->add_partial('newsup_header_fb_link', array(
				'selector'        => '.mg-social',
  ));


		    

 }

function newsway_theme_setup() {

//Load text domain for translation-ready
load_theme_textdomain('newsway', get_stylesheet_directory() . '/languages');

require( get_stylesheet_directory() . '/hooks/hooks.php' );
require( get_stylesheet_directory() . '/hooks/hook-header-section.php' );


// custom header Support
			$args = array(
			'default-image'		=>  '',
			'width'			=> '1600',
			'height'		=> '600',
			'flex-height'		=> false,
			'flex-width'		=> false,
			'header-text'		=> true,
			'default-text-color'	=> '#fff'
		);
		add_theme_support( 'custom-header', $args );
} 
add_action( 'after_setup_theme', 'newsway_theme_setup' );


/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function newsway_widgets_init() {
	

	register_sidebar( array(
		'name'          => esc_html__( 'Front-Page Canvas Section', 'news-way'),
		'id'            => 'front-page-canvas-sidebar',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="mg-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="mg-wid-title"><h6>',
		'after_title'   => '</h6></div>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Front-Page Left Sidebar Section', 'news-way'),
		'id'            => 'front-left-page-sidebar',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="mg-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="mg-wid-title"><h6>',
		'after_title'   => '</h6></div>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Front-Page Right Sidebar Section', 'news-way'),
		'id'            => 'front-right-page-sidebar',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="mg-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="mg-wid-title"><h6>',
		'after_title'   => '</h6></div>',
	) );

	

}
add_action( 'widgets_init', 'newsway_widgets_init' );


function newsway_remove_some_widgets(){
// Unregister Frontpage sidebar
unregister_sidebar( 'front-page-sidebar' );
}
add_action( 'widgets_init', 'newsway_remove_some_widgets', 11 );

function newsway_menu(){ ?>
<script>
jQuery('a,input').bind('focus', function() {
    if(!jQuery(this).closest(".menu-item").length && ( jQuery(window).width() <= 992) ) {
    jQuery('.navbar-collapse').removeClass('show');
}})
</script>
<?php }
add_action( 'wp_footer', 'newsway_menu' );