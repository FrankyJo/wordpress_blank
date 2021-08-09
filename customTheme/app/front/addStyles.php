<?php

add_action('wp_enqueue_scripts', 'addHeaderStyles');
add_action('wp_footer', 'addFooterStyles');
function addHeaderStyles()
{
    if (isMainPage()) {
        wp_enqueue_style('home_page', get_template_directory_uri() . '/assets/css/home.css');
    } else {
        wp_enqueue_style('inner_page', get_template_directory_uri() . '/assets/css/inner.css');
    }
}

function addFooterStyles()
{
}
