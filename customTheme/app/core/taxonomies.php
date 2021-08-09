<?php
/**
 * Created by PhpStorm.
 * User: ole.andreynik
 * Date: 7/17/2018
 * Time: 4:42 PM
 */


add_action('init', 'initAllTaxonomies');
function initAllTaxonomies()
{
    $default = [
        'public' => true,
        'hierarchical' => true,
    ];
    $taxonomies = [
        'example' => [
            'category_example' => [
                "rewrite"               => true,
                'labels' => [
                    'name'              => 'Type example',
                    'singular_name'     => 'Type',
                    'search_items'      => 'Search',
                    'all_items'         => 'All types',
                    'view_item '        => 'View',
                    'edit_item'         => 'Edit',
                    'update_item'       => 'Reload',
                    'add_new_item'      => 'Add new',
                    'new_item_name'     => 'New type name',
                    'menu_name'         => 'Type example',
                ],
            ],
        ],
    ];
    foreach ($taxonomies as $post_type => $tax) {
        foreach ($tax as $slug => $args){
            $settings = array_merge($default, $args);
            register_taxonomy($slug, $post_type, $settings);
        }
    }
}
