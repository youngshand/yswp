<?php
/**
 * Utils
 *
 * Add all utilities which will be used across this sites plugins to this file
 */

/* http://stackoverflow.com/questions/2791998/convert-dashes-to-camelcase-in-php */
function to_camel_case($str, $capitalizeFirstCharacter = false) {
		$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));
		$str = str_replace(' ', '', ucwords(str_replace('-', ' ', $str)));

		if (!$capitalizeFirstCharacter) {
				$str[0] = strtolower($str[0]);
		}

		return $str;
}

/* Generate path name */
function generatePathName($post) {
		if ($post->post_name === 'home') {
			$pathName = '/';
		} else {
			$pathName = '/' . $post->post_name;
		}

		$paths = array($pathName);
		$parentId = $post->post_parent;

		while ($parentId) {
				$parent = get_page($parentId);
				$pathSlug = '/' . $parent->post_name;
				array_push($paths, $pathSlug);
				$parentId = $parent->post_parent;
		}

		$paths = array_reverse($paths);

		$path = '';
		foreach ($paths as $slugs) {
				$path .= $slugs;
		}

		return $path;
}

function ys_update_front_end_cache( $post_id ) {

		//cURL options
		$cSession = curl_init();
		$host = false;
		$env = $_SERVER['ENV'];

		$frontend_url = get_field('siteUrl', 'option');

		// if $frontend_url ends with '/', remove it
		if(substr($frontend_url, -1) === '/'){
			$frontend_url = rtrim($frontend_url, "/");
		}

		if(!$frontend_url){
			if( $env === 'staging' ){
				$frontend_url = 'http://minter.beingbui.lt'; // add staging front end URl here
			}elseif($env === 'production'){
				$frontend_url = ''; // add production front end URl here
			}
		}


		if($frontend_url){
				$url = $frontend_url . '/api/update';

				curl_setopt( $cSession, CURLOPT_URL, $url);
				curl_setopt( $cSession, CURLOPT_CONNECTTIMEOUT ,1); // 0 = indefinitely, 1sec is min timeout
				curl_setopt( $cSession, CURLOPT_TIMEOUT, 1); // 0 = indefinitely, 1sec is min timeout
				curl_setopt( $cSession, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt( $cSession, CURLOPT_RETURNTRANSFER, true);
				curl_setopt( $cSession, CURLOPT_HEADER, false);

				//execute cURL
				$result = curl_exec($cSession);

				//close cURL
				curl_close($cSession);
		}
}

add_action( 'save_post', 'ys_update_front_end_cache' );
add_action('acf/save_post', 'ys_update_front_end_cache', 20);

function get_post_template($post){
		$template = str_replace( '.php', '', get_post_meta( $post->ID, '_wp_page_template', true ) );
		return $template;
}

// dynamically finds the slug of page by template and prefixes $page_name + $path
function find_slug_from_template(&$path, $template){

	$pages = get_pages(array(
    'meta_key' => '_wp_page_template',
    'meta_value' => $template
	));

	if(count($pages) > 0){
		$path = $path . $pages[0]->post_name . '/';
	}

	return $path;

}



function get_post_path($post){

  $path = '/';

  // News items, find page using news template and get it's slug
  if ($post->post_type === 'articles' || $post->post_type === 'announcements') {
  	find_slug_from_template($path, 'news.php'); // overrides $path variable
  }

  // People items, find page using people template and get it's slug
  if ($post->post_type === 'people') {
  	find_slug_from_template($path, 'people.php'); // overrides $path variable
  }

  // Offerrings items, find page using services-industries template and get it's slug
  if ($post->post_type === 'services' || $post->post_type === 'industries') {
  	find_slug_from_template($path, 'services.php'); // overrides $path variable
  }

  // Unique case for home page
  if (get_post_template($post) !== 'home'){
  	$path = $path . $post->post_name;
  }

  if ( $post->post_parent ) {
  	$parent = get_page($post->post_parent);
  	$path = '/' . $parent->post_name . '/' . $post->post_name;
  }

  return $path;
}

// Used to populate paths for menus
function get_custom_post_paths(){
	$paths = array();
  $pageNames = array();
  $pages = get_posts(array(
      'post_type' => 'page',
      'posts_per_page' => '-1'
  ));

  if($pages) {

      foreach($pages as $post) {
          $pageNames[$post->post_title] = $post->post_title;
      }

      ksort($pageNames);

      foreach($pages as $post) {
          $path = generatePathName($post);
          $paths[$path] = $post->post_title;
      }
  }

  return $paths;
}

/*
 * Wrap a string in p tags
 */
function wrap_p_tags($input){
  if(strpos($input, '<p>') === false) {
    return '<p>' . $input . '</p>';
  }
  // p tags are already present
  return $input;
}

/*
 *	Strip <p> tags from string
 */
function strip_p_tags($input){
	$input = str_replace('<p>', '', $input);
	$input = str_replace('</p>', '', $input);

	return $input;
}

/*
 * Dump and Die
 */
function dd($data){
	echo "<pre>";
	print_r($data);
	echo "</pre>";
	die;
}

function flatten($input, $key_match){

	$response = [];
	if($input){
		foreach($input as $key => $value){
			if(is_array($value)){
				foreach($value as $k => $v){
					if( $k === $key_match ){
						array_push($response, $v);
					}
				}
			}

		}
	}

	return $response;
}

/**
 *	Improved flatten function which passes input by reference
 */
function flatten_array(&$input, $key_match){

	$response = [];
	if($input){
		foreach($input as $key => $value){
			if(is_array($value)){
				foreach($value as $k => $v){
					if( $k === $key_match ){
						array_push($response, $v);
					}
				}
			}

		}
	}

	$input = $response;

	return $input;
}

/*
 *	gets all wordpress pages to populate cta select dropdown
 *	-- may need to add links for custom post types e.g. services, industries, articles
 */
function all_pages(){
	$response = [];
	$pages = get_pages() ?: [];

	foreach($pages as $page){
		$response[$page->post_name] = $page->post_title;
	}

	return $response;
}

function get_sections(){
	$response = [];

	$sections = get_field('section_ids', 'option') ?: [];

	if(is_array($sections)){

		foreach($sections as $section){
			$response[$section['id']] = $section['section_name'];
		}

	}

	return $response;

}

function get_page_by_template($template){
	
	$pages = get_pages(array(
    'meta_key' => '_wp_page_template',
    'meta_value' => $template,
    'posts_per_page' => 1
	));

	return $pages[0] ?: null;

}