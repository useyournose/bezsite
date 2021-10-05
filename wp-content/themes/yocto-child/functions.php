<?php

function nice_redirect ()  {
    wp_enqueue_script( 'script', get_stylesheet_directory_uri() . '/assets/js/redirectonframe.js',array(), 1.0, false);
}

add_action( 'wp_enqueue_scripts','nice_redirect');

function my_theme_enqueue_styles() {
    wp_enqueue_style( 'yocto-child-style', get_stylesheet_uri(),
        array( 'yocto-styles' ), 
        wp_get_theme()->get('Version') // this only works if you have Version in the style header
    );
}

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

wp_enqueue_style( 'prefix-font-awesome', 'http://netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.css', array(), '4.7.0' );

// handle framing
function no_frame_headers()
{
    header( "X-Frame-Options: DENY", true );
    header( "Content-Security-Policy: frame-ancestors 'none'", true );
}

add_action( 'login_init',        'no_frame_headers', 1000 );
add_action( 'admin_init',        'no_frame_headers', 1000 );
//add_action( 'template_redirect', 'no_frame_headers', 1000);

//remove ip from comments
function  wpb_remove_commentsip( $comment_author_ip ) { return ' ';}
add_filter( 'pre_comment_user_ip', 'wpb_remove_commentsip' );

// clean up wordpress
// wp version
remove_action('wp_head', 'wp_generator');
//APIs
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
//emoji support
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

// wp Versions-Parameter ?ver=... von Skripts entfernen,
// falls unsere WordPress-Version angegeben wird
function vc_remove_wp_ver_css_js( $src )
{
   if (  strpos($src, 'ver='. get_bloginfo('version') )  )
      $src = remove_query_arg('ver', $src);
   return $src;
}
add_filter('style_loader_src',  'vc_remove_wp_ver_css_js', 9999);
add_filter('script_loader_src', 'vc_remove_wp_ver_css_js', 9999);

// REST Api Hinweise entfernen
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('template_redirect', 'rest_output_link_header', 11, 0);
 
function remove_json_api ()
{
   // REST API Zeilen aus dem HTML Header entfernen
   remove_action('wp_head', 'rest_output_link_wp_head', 10);
   remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
   // REST API endpoint entfernen
   remove_action('rest_api_init', 'wp_oembed_register_route');
   // oEmbed auto discovery entfernen
   add_filter('embed_oembed_discover', '__return_false');
   // oEmbed results nicht filtern
   remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
   // oEmbed discovery links entfernen
   remove_action('wp_head', 'wp_oembed_add_discovery_links');
   // oEmbed-JavaScript entfernen
   remove_action('wp_head', 'wp_oembed_add_host_js');
   // rewrite rules zum Einbetten entfernen
   add_filter('rewrite_rules_array', 'disable_embeds_rewrites');
}

add_action( 'after_setup_theme', 'remove_json_api' );

/* Die XMLRPC-Schnittstelle komplett abschalten */
add_filter( 'xmlrpc_enabled', '__return_false' );

/* Den HTTP-Header vom XMLRPC-Eintrag bereinigen */
add_filter( 'wp_headers', 'AH_remove_x_pingback' );
function AH_remove_x_pingback( $headers ) {
	unset( $headers['X-Pingback'] );
	return $headers;
}
?>