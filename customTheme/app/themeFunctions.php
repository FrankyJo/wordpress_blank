<?php

add_theme_support('post-thumbnails'); //enable thumbnails for all post types


function get_archive_post_type()
{
    return is_archive() ? get_queried_object()->name : false;
}
