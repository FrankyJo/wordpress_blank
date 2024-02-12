<?php


get_header();
wp_enqueue_style('page_default', get_template_directory_uri() . '/public/css/pages/default/default.css');
get_template_part('blocks/breadcrumbs/breadcrumbs_v1');


?>

<section class="default-page">
    <div class="default-page__wrapper container">
        <h1 class="default-page__title"><?php the_title() ?></h1>
        <div class="default-page__content">
            <?php the_content(); ?>
        </div>
    </div>
</section>

<?php
get_footer()

?>
