<?php


add_action('wp_enqueue_scripts', 'addHeaderScripts');
add_action('wp_footer', 'addFooterScripts');

function addHeaderScripts()

{
}

function addFooterScripts()
{
  global $wp_query;

  wp_enqueue_script('general_script', ASSETS_URI . 'js/general.js');
  wp_enqueue_script('main_script', ASSETS_URI . 'js/main.js');


  if ( is_page_template('page-claint-form.php') ) {
    wp_enqueue_script('', ASSETS_URI . 'js/pages/page-claim-form.js');
  }

}


/* Remove loading jQuery */
function my_init() {
  if (!is_admin()) {
    wp_deregister_script('jquery');
  }
}

add_action('init', 'my_init');

