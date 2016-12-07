<?php

if (file_exists(__DIR__ . '/../ys-seo/plugin.php') ) {
  require_once(__DIR__ . '/../ys-seo/plugin.php');
}

require_once __DIR__ . '/formatter.php';

/*
* GET Queries
*/

/* Get all pages */
function merw_get_all_pages() {
  $pages = get_posts([
    'post_type' => 'page',
    'posts_per_page' => -1,
    'order' => 'ASC',
    'post_status' => array('publish', 'draft', 'private'),
    'orderby' => 'post_title'
  ]);

  $response = Formatter::format_pages($pages);
  return wp_send_json($response);
}

function merw_get_all_settings() {
  $settings = array(
    'site' => array(
        'title' => get_field('site_title', 'option'),
        'siteUrl' => get_field('siteUrl', 'option'),
    ),
    'logo' => Formatter::format_image( get_field('site_logo', 'option') ) ?: [],
    'secondarylogo' => Formatter::format_image( get_field('secondarmeitelogo', 'option') ) ?: [],
    'favicon' => Formatter::format_image( get_field('favicon', 'option') ) ?: [],
    'disclaimer' => get_field('site_disclaimer', 'option'),
    'tagline' => get_field('site_tagline', 'option'),
    'social' => get_field('social', 'option'),
    'email' => get_field('email', 'option'),
    'phone' => get_field('phone', 'option'),
    'facebookAppId' => get_field('appId', 'options') ?: '',
    'seo' => Formatter::format_seo(),
    'newsLetterSignUp' => array(
      'image' => get_field('sign_up_image', 'options') ?: '',
      'title' => get_field('sign_up_section_title', 'options') ?: '',
      'content' => get_field('sign_up_section_content', 'options') ?: ''
    )
  );

  return $settings;
}


function merw_save_signups() {

  $data = $_POST;

  // throws error if email is not set
  if(
      !isset($data['email']) ||
      !isset($data['name'])
    ){
    $statusCode = 449;
    status_header( $statusCode );
    $message = "email and name must be set";
    return new WP_Error( $statusCode, $message, array( 'status' => $statusCode . ' Retry With' ) );
  }

  // check for existing user
  $existing_user = get_posts(array(
    'numberposts' => -1,
    'post_type'   => 'signup',
    'meta_key'    => 'email',
    'meta_value'  => $data['email']
  ));

  if($existing_user){

    $page_id = $existing_user[0]->ID;

    $action = 'updated';

  }else{

    // add new post to sign up post type
    $page_id = wp_insert_post(array(
      'post_title'     => $data['email'],
      'post_name'      => $data['email'],
      'post_status'    => 'publish',
      'post_type'      => 'signup',
    ));

    $action = 'saved';

  }

  // print(json_encode($data)); die;

  // add fields to post object
  foreach($data as $key => $value){

    $value = sanitize_text_field( $value );

    switch($key){
      case 'name':
        $name = $value;
        update_field('name', $name, $page_id);
        break;
      case 'email':
        $email = sanitize_email( $value );
        update_field('email', $email, $page_id);
        break;
      case 'jobTitle':
        update_field('jobTitle', $value, $page_id);
        break;
      case 'business':
        update_field('business', $value, $page_id);
        break;
      case 'location':
        update_field('location', $value, $page_id);
        break;
    }

  }

  $response = [
    'message' => 'success',
    'action' => $action,
    'body' => $data
  ];

  return new WP_REST_Response( $response, 200 );

}

/* Get all menus */
function merw_get_all_menus( ) {

  $menus = get_field('navigations', 'options')[0];

  function debug($x) {
    echo json_encode($x);
    die;
  }

  if($menus){
    foreach($menus as $key => $menu){

      if($menu){
        foreach($menu as $menuItem){
          $subPages = [];
          if( isset($menuItem['subPages'][0]) ) {
            foreach( $menuItem['subPages'] as $subMenu ) {
                $post = get_page_by_title($subMenu['subPageTitle']);
                $subPages[] = array(
                  'title' => $subMenu['subPageTitle'],
                  'slug' => $subMenu['subPageTitle'],
                );
            }
          }

          $post = get_page_by_path($menuItem['title']);

          if($post){
            $menuItems[$key][] = array(
              'title' => $post->post_title,
              'slug' => generatePathName($post),
              'subPages' => $subPages
            );
          }
        }
      }
    }
  }

  if ( empty( $menuItems ) ) {
      $menuItems = [];
  }

  return $menuItems;
}



/*
* POST Queries
*/
