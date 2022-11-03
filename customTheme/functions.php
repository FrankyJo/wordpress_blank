<?php
/**
 * const
 */
define('APP_DIR', dirname(__FILE__) . "/app");
define('ASSETS_DIR', dirname(__FILE__) . "/public");
define('ASSETS_URI', get_template_directory_uri() . "/public/");

/**
 * require another function
 */
require_once(APP_DIR . '/core/postTypes.php');
require_once(APP_DIR . '/core/customFieldProfile.php');
require_once(APP_DIR . '/core/taxonomies.php');

require_once(APP_DIR . '/front/menuCreator.php');
require_once(APP_DIR . '/front/addStyles.php');
require_once(APP_DIR . '/front/addScripts.php');

require_once (APP_DIR . '/wpFilters.php');
require_once(APP_DIR . '/actionAjax.php');
require_once(APP_DIR . '/themeFunctons.php');


/**
 * classes loader
 */
spl_autoload_register(function ($class) {
    $folders = ['/classes/',];
    foreach ($folders as $folder) {
        if (file_exists(str_replace('\\', '/', APP_DIR . $folder . $class . '.php')))
            include str_replace('\\', '/', APP_DIR . $folder . $class . '.php');
    }
});


function breadcrumbs($sep = ' > ', $l10n = array(), $args = array())
{
    $pb = new Phonexa_Breadcrumbs;
    $l10n = [
        'home' => 'Phonexa',
        'paged' => 'Page %d',
        '_404' => 'Error 404',
        'search' => 'Search',
        'author' => '<li><a href="/blog/"><span>Blog</span></a><li> %s ',
        'year' => 'Archive <b>%d</b> year',
        'month' => 'Archive: <b>%s</b>',
        'day' => '',
        'attachment' => 'Media: %s',
        'tag' => 'Post by tag: <b>%s</b>',
        'tax_tag' => '%1$s in "%2$s" by tag: <b>%3$s</b>',
    ];

    echo $pb->get_crumbs($sep, $l10n, $args);
}


