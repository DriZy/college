<?php

    require get_theme_file_path('/includes/like-route.php');

    require get_theme_file_path('/includes/search-route.php');

    function my_theme_custom_rest()
    {
      register_rest_field('post', 'authorName', array(
        'get_callback' => function() {return get_the_author();}
      ));
	    register_rest_field('note', 'userNoteCount', array(
		    'get_callback' => function() {return count_user_posts(get_current_user_id(), 'note');}
	    ));
    }
    add_action('rest_api_init', 'my_theme_custom_rest');
    
    function my_theme_css_and_js_files()
    {
      wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=AIzaSyBh9b1rNCp6kOi5JeMHiRP4klDymBeoEWk', NULL, '1.0', true);
      wp_enqueue_script('my_theme_main_js', get_theme_file_uri('/js/scripts-bundled.js'), NULL, microtime(), true);
      wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
      wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
      wp_enqueue_style('my_theme_main_style', get_stylesheet_uri());
      wp_localize_script('my_theme_main_js', 'collegeData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest')
      )); 
    }

    add_action('wp_enqueue_scripts', 'my_theme_css_and_js_files');


    function my_theme_features(){
        register_nav_menu('headerMenuLocation', 'Main Menu');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_image_size('professorLandscape', 400, 260, true);
        add_image_size('professorPortrait', 480, 650, true);
        add_image_size('pageBanner', 1500, 350, true);
    }
    add_action('after_setup_theme', 'my_theme_features');

    function my_theme_adjust_queries($query) {

      if (!is_admin() AND is_post_type_archive('campus') AND is_main_query()) {
        $query->set('posts_per_page', -1);
      }
        if (!is_admin() AND is_post_type_archive('program') AND is_main_query()) {
            $query->set('orderby', 'title');
            $query->set('order', 'ASC');
            $query->set('posts_per_page', -1);
          }

        if (!is_admin() AND is_post_type_archive('event') AND is_main_query()) {
          $today = date('Ymd');
          $query->set('meta_key', 'event_date');
          $query->set('orderby', 'meta_value_num');
          $query->set('order', 'ASC');
          $query->set('meta_query', array(
                    array(
                      'key' => 'event_date',
                      'compare' => '>=',
                      'value' => $today,
                      'type' => 'numeric'
                    )
                  ));
        }
      }
      
      add_action('pre_get_posts', 'my_theme_adjust_queries');

      function my_theme_pageBanner($args = NULL) {
  
        if (!$args['title']) {
          $args['title'] = get_the_title();
        }
      
        if (!$args['subtitle']) {
          $args['subtitle'] = get_field('page_banner_subtitle');
        }
      
        if (!$args['photo']) {
          if (get_field('page_banner_image')) {
            $args['photo'] = get_field('page_banner_image')['sizes']['pageBanner'];
          } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
          }
        }
      
        ?>
        <div class="page-banner">
          <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>);"></div>
          <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
            <div class="page-banner__intro">
              <p><?php echo $args['subtitle']; ?></p>
            </div>
          </div>  
        </div>
      <?php }


function drizyMapKey($api)
{
  $api['key'] = 'AIzaSyBh9b1rNCp6kOi5JeMHiRP4klDymBeoEWk';
  return $api;
}

add_filter('acf/fields/google_map/api', 'drizyMapKey');


// Redirect subscriber accounts out of admin and onto homepage
add_action('admin_init', 'redirectSubsToFrontend');

function redirectSubsToFrontend() {
  $ourCurrentUser = wp_get_current_user();

  if (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
    wp_redirect(site_url('/'));
    exit;
  }
}

add_action('wp_loaded', 'noSubsAdminBar');

function noSubsAdminBar() {
  $ourCurrentUser = wp_get_current_user();

  if (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
    show_admin_bar(false);
  }
}

// Customize Login Screen
add_filter('login_headerurl', 'ourHeaderUrl');

function ourHeaderUrl() {
  return esc_url(site_url('/'));
}

add_action('login_enqueue_scripts', 'ourLoginCSS');

function ourLoginCSS() {
  wp_enqueue_style('university_main_styles', get_stylesheet_uri());
  wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
}

add_filter('login_headertitle', 'ourLoginTitle');

function ourLoginTitle() {
  return get_bloginfo('name');
}

//Force note post to be private

add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);

function makeNotePrivate($data, $postarr){
    if (count_user_posts(get_current_user_id(), 'note') > 5 AND !$postarr['ID']){
        die("Sorry you have reached your note limmit.");
    }
    if ($data['post_type']== 'note'){
        $data['post_title'] = sanitize_text_field($data['post_title']);
        $data['post_content'] = sanitize_textarea_field($data['post_content']);
    }
    if ($data['post_type']== 'note' AND $data['post_status']!= 'trash'){
        $data['post_status'] = 'private';
    }
    return $data;
}