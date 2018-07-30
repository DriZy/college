<?php

  get_header();

  while(have_posts()) {
    the_post();
      my_theme_pageBanner();
    ?>
    

  <div class="container container--narrow page-section">

   <?php 

    $myThemeParentPage = wp_get_post_parent_id(get_the_iD());
   
    if($myThemeParentPage){
        
      ?>
        <div class="metabox metabox--position-up metabox--with-home-link">
        <p><a class="metabox__blog-home-link" href="<?php echo get_permalink($myThemeParentPage); ?>"><i class="fa fa-home" aria-hidden="true"></i> Back to <?php echo get_the_title($myThemeParentPage); ?></a> <span class="metabox__main"><?php the_title(); ?></span></p>
      </div>
    <?php   }
   
   ?>
    
    <?php 
    $pageMenu = get_pages(array(
      'child_of' => get_the_ID()
    ));
    if($myThemeParentPage or $pageMenu){
    ?>
    <div class="page-links">
      <h2 class="page-links__title"><a href="<?php echo get_permalink($myThemeParentPage);?>"><?php echo get_the_title($myThemeParentPage); ?></a></h2>
      <ul class="min-list">
        <?php 
          if($myThemeParentPage){
            $findChildrenOf = $myThemeParentPage;
          }else{
            $findChildrenOf = get_the_ID();
          }

          wp_list_pages(array(
            'title_li' => NULL,
            'child_of' => $findChildrenOf, 
          ));
        ?>
      </ul>
    </div>
        <?php }?>
   

    <div class="generic-content">
      <?php the_content(); ?>
    </div>

  </div>
    
  <?php }

  get_footer();

?>