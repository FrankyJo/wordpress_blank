<?php

add_action('init', 'initAllPostTypes');

function initAllPostTypes()
{
    $default = [
        'public' => true,
        'publicly_queryable' => null,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_tagcloud' => true,
        'hierarchical' => false,
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions'],
        'has_archive' => true,
        'rewrite' => true,
        'query_var' => true,
    ];
    $post_types = [
        'example' => [
            'public' => true,
            'menu_icon' => 'dashicons-list-view',
            'taxonomies' => ['category_example'],
            'labels' => [
                'name' => 'Example',
                'singular_name' => 'Example page',
                'add_new' => 'Add Example',
                'add_new_item' => 'Add Example',
                'edit_item' => 'Edit',
                'new_item' => 'New',
                'view_item' => 'See',
                'search_items' => 'Search',
                'not_found' => 'No items',
                'not_found_in_trash' => 'No items in trash',
                'parent_item_colon' => '',
                'menu_name' => 'Table Example',
            ],
        ],
    ];
    foreach ($post_types as $post_type => $args) {
        $settings = array_merge($default, $args);
        register_post_type($post_type, $settings);
    }
}
