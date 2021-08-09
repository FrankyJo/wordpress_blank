<?php get_header();

get_template_part('blocks/breadcrumbs/breadcrumbs_v1');

?>

<div class="container">
    <div class="page-title"><?php pll_e('global_searchResult'); ?></div>

    <div class="search-list">
        <?php while ( have_posts() ) :?>
            <?php the_post();?>
            <article class="search-list__item">
                <a href="<?php the_permalink();?>" class="list-title"><?php the_title();?></a>
                <p><?php echo kama_excerpt(['text'=>get_the_content(), 'maxchar'=>500]); ?></p>
            </article>
        <?php endwhile;?>
    </div>
</div>

<?php get_footer(); ?>
