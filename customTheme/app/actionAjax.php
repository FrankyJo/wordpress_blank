<?php

add_theme_support('post-thumbnails'); //enable thumbnails for all post types


add_action('wp_ajax_init_example', 'init_example');
add_action('wp_ajax_nopriv_init_example', 'init_example');
function init_example()
{

}