<?php

/*
 * Setup which pages flexible content layout shouldn't be displayed on certain pages
 *
 * @param array $field
 */
add_filter('acf/load_field', function($field) {

  if (! is_admin() || $field['type'] !== 'flexible_content') {
    return $field;
  }

  global $post;

  // Setup which flexible layouts to remove
  $remove = [
    'masthead' => ['services', 'industries', 'home.php', 'contact.php', 'news.php'],
    'title' => ['home.php', 'people.php', 'services.php', 'news.php'],
    'image' => ['home.php', 'contact.php', 'people.php', 'services.php', 'news.php'],
    'text' =>['home.php', 'people.php', 'services.php', 'news.php'],
    'video' => ['services', 'industries', 'home.php', 'contact.php', 'people.php', 'services.php', 'news.php'],
    'textVideo' => ['services', 'industries', 'home.php', 'contact.php', 'people.php', 'services.php', 'news.php'],
    'textImage' => ['services', 'industries', 'contact.php', 'people.php', 'services.php', 'news.php'],
    'blockQuote' => ['home.php', 'contact.php', 'people.php', 'services.php', 'news.php'],
    'list' => ['home.php', 'contact.php', 'people.php', 'services.php', 'news.php'],
    'gallery' => ['services', 'industries', 'home.php', 'contact.php', 'people.php', 'services.php', 'news.php'],
    'ctaBanner' => ['services', 'industries', 'home.php', 'contact.php', 'people.php', 'services.php', 'news.php'],
    'officeMap' => ['services', 'industries', 'home.php', 'contact.php', 'people.php', 'services.php', 'news.php'],
    'newsletterSignup' => ['services', 'industries'],
    'practiceLeaders' => ['home.php', 'contact.php', 'people.php', 'services.php', 'news.php'],
    'dataList' => ['home.php', 'contact.php', 'people.php', 'services.php', 'news.php'],
  ];

  // Remove layouts if required
  foreach ($field['layouts'] as $key => $value) {
    if ( array_key_exists($value['name'], $remove) && ( in_array($post->post_type, $remove[$value['name']]) || in_array(basename(get_page_template($post)), $remove[$value['name']]) ) ) {
      unset($field['layouts'][$key]);
    }
  }

  return $field;
});