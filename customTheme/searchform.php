<form class="search" role="search" method="get" id="searchform" action="<?php echo home_url('/') ?>">
    <div class="search__field-wrapper">
        <input type="text" class="search__field" value="<?php echo get_search_query() ?>" name="s" id="s"/>
        <button type="submit" id="searchsubmit" class="search__btn icon icon-search"></button>

    </div>
</form>