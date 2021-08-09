<div class="breadcrumbs__wrapper">
  <div class="container">
    <div class="breadcrumbs">
      <?php
        if(function_exists('kama_breadcrumbs')) {
          kama_breadcrumbs( '', ['home' => 'Home']);
        }
      ?>
    </div>
  </div>
</div>
