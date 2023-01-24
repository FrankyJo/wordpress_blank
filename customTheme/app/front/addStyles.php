<?php

add_action('wp_enqueue_scripts', 'addHeaderStyles');
add_action('wp_footer', 'addFooterStyles');
function addHeaderStyles()
{
    if (is_front_page()) {
        wp_enqueue_style('home_page', get_template_directory_uri() . '/assets/css/home.css');
    } else {
        wp_enqueue_style('inner_page', get_template_directory_uri() . '/assets/css/inner.css');
    }
}

function addFooterStyles()
{
}

/*
* SHOW CRITICAL CSS
*/
function loadCriticalCss()
{
    global $post;

    $page_template = get_page_template_slug($post->ID);

    if ($page_template) {

        $pattern = '/^templates\/page-(.*).php$/';
        preg_match($pattern, $page_template, $result);
        $criticalCss = file_get_contents(ASSETS_DIR . '/css/critical/critical-' . $result[1] . '.css');
    }

    if (get_archive_post_type()) {
        $criticalCss = file_get_contents(ASSETS_DIR . '/css/critical/critical-' . get_archive_post_type() . '.css');
    }

    if ($criticalCss) {
        $style = '<style type="text/css">';
        $style .= $criticalCss;
        $style .= '</style>';

        echo $style;
    }
}

function hardLoadCriticalCss()
{

    if (is_search()) {
        $criticalCss = file_get_contents(ASSETS_DIR . '/css/critical/critical-blog.css');
    }

    if (is_single()) {
        $criticalCss = file_get_contents(ASSETS_DIR . '/css/critical/critical-single-blog.css');
    }

    if (is_category() || is_tag()) {
        $criticalCss = file_get_contents(ASSETS_DIR . '/css/critical/critical-blog.css');
    }

    if( is_page_template('templates/page-event-details-ny.php') ){
        $criticalCss = file_get_contents(ASSETS_DIR . '/css/critical/critical-event-details.css');
    }

    if ($criticalCss) {
        $style = '<style type="text/css">';
        $style .= $criticalCss;
        $style .= '</style>';

        echo $style;
    }

}

