<?php

/** remove all shit! */
/*Выводит ссылку на корневой REST API маршрут в секции <head> на всех страницах сайта.*/
remove_action('wp_head', 'rest_output_link_wp_head', 10);

/*Добавляет ссылки oEmbed обнаружения на сайте*/
remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

/*Добавляет необходимый JavaScript для связи со встроенными ифреймами.*/
remove_action('wp_head', 'wp_oembed_add_host_js');

/*Выводит ссылку на корневой REST API маршрут в секции <head> на всех страницах сайта.*/
remove_action('template_redirect', 'rest_output_link_header', 11);

/*Конвертирует не кликабельные ссылки в тексте*/
remove_filter('comment_text', 'make_clickable', 9);

/*Ссылка на ленту комнтариев*/
remove_action('wp_head', 'feed_links_extra', 3);

/*Показывает линку на главный фид*/
remove_action('wp_head', 'feed_links', 2);

/*Отобразите ссылку на конечную точку службы Really Simple Discovery.*/
remove_action('wp_head', 'rsd_link');

/*Отобразите ссылку на файл манифеста Windows Live Writer.*/
remove_action('wp_head', 'wlwmanifest_link');

/*Отображает реляционные ссылки для сообщений, смежных с текущим сообщением.*/
remove_action('wp_head', 'adjacent_posts_rel_link', 10);

/*Отображает реляционные ссылки для сообщений, смежных с текущим сообщением, для страниц отдельных сообщений.*/
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);

/*Отображает генератор XHTML. Делает WordPress видимой и общедоступной. Можно считать угрозой безопасности.*/
remove_action('wp_head', 'wp_generator');

/*Выводит короткую ссылку записи (поста) в виде мета информации - rel='shortlink'*/
remove_action('wp_head', 'wp_shortlink_wp_head', 10);

/*Отключаем эмодзи*/
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
/** END */


/*Филттры относящиеся к коротким описаниям*/
add_filter('excerpt_length', function () {
    return 25;
});

add_filter('excerpt_more', function($more) {
    return ' ';
});

