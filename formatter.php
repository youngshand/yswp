<?php
/**
 * Formatter
 */

require_once __DIR__ . '/utils.php';

/* Format Custom Post Type Responses */
class Formatter {

	/**
	 * Formats keys and returns a subset of values which are required for api output
	 */
	public static function format_pages($posts = [], $schema = []) {

		if($posts){
			foreach($posts as $post){
				$schema[] = [
					'id' => $post->ID,
					'title' => $post->post_title,
					'slug' => $post->post_name,
					'path' => get_post_path($post),
					'template' => get_post_template($post),
					'published' => $post->post_date,
					'image' => wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' )[0],
					'seo' => Formatter::format_seo($post),
					'sections' => Formatter::format_sections(get_field('modules', $post->ID)) ?: [],
					'data' => Formatter::format_page_uniq($post),
				];
			}

		}

		return $schema;
	}

	/**
	 * 	Format an image object and return a subset of values which are required for api output
	 */
	public static function format_image($image) {

		$serialized_image = null;

		if(! is_array($image)){
			return [];
		}

		if( !empty($image) && isset($image['ID'])) {

			$serialized_image = [
				'type' => 'image',
				'id' => $image['ID'],
				'title' => $image['title'],
				'url' => $image['url'],
				'alt' => $image['alt'],
				'description' => $image['description'],
				'mimeType' => $image['mime_type'],
				'width' => $image['width'],
				'height' => $image['height'],
				'sizes' => [],
			];

			if (!$image) {
				return [];
			}

			// Splits array into groups of 3 and removes key
			$sizes = array_chunk($image['sizes'], 3, true);

			$sizes_new = [];

			// foreach group, replace key with value from $keys array
			foreach ($sizes as $key => $value) {

				// gets key name of first item in array;
				reset($value);
				$first_key = key($value);

				$keys = [
					'url',
					'width',
					'height',
				];

				// creates new array with keys from $keys array and $values from $value array
				$sizes_new[$first_key] = array_combine($keys, $value);

			}

			$serialized_image['sizes'] = $sizes_new;

		}

		return $serialized_image;
	}

	/**
	 * 	Formats SEO response for posts
	 */
	public static function format_seo($post = null, $schema = []) {
		if($post){
      $schema = [
        'og' => [
          'title' => get_field('ogTitle', $post->ID),
          'description' => get_field('ogDescription', $post->ID),
          'image' => get_field('ogImage', $post->ID),
        ],
      ];
		} else {
      $options = get_fields('option');

      if (isset($options['ogAndMeta']) && isset($options['ogAndMeta'][0])) {

        $schema = [
          'title' => $options['ogAndMeta'][0]['title'],
          'description' => $options['ogAndMeta'][0]['description'],
          'image' => $options['ogAndMeta'][0]['image'],
          'og' => [
            'title' => $options['ogAndMeta'][0]['title'],
            'description' => $options['ogAndMeta'][0]['description'],
            'image' => $options['ogAndMeta'][0]['image'],
          ],
          'type' => $options['ogAndMeta'][0]['type'],
          'fbAdmins' => $options['ogAndMeta'][0]['fbAdmins'],
          'gtm' => $options['ogAndMeta'][0]['googleTagManager'],
          // strip any p tags and new line characters
          'schema' => str_replace(PHP_EOL, '', strip_tags($options['ogAndMeta'][0]['schema'])),
        ];
      }
    }

		return $schema;
	}

	/*	=============================================================
	 * 	Recursive loop to find custom modules and format appropriately.
	 *	=============================================================
	 */
	public static function format_sections($input){

		$return = array();

		if($input){

			foreach($input as $key => $value){

				switch(true){

					case $key === 'image':
						$value = Formatter::format_image($value);
						break;

					case $key === 'acf_fc_layout':
						$key = 'type';
						break;

					case $key === 'cta':

						// Return the standard array if length > 1. Required for masthead CTA functionality
						if(count($value) < 2){
							$value = $value[0] ?: [];
						}

						break;

					case $key === 'listContent':
						$value = flatten($value, 'listItem'); // Flattens nested objects to 1 dimensional array
						break;

					case $key === 'contact_numbers':
						$value = $value[0] ?: [];
						break;
				}


				// Strips HTML tags
				$strip_blacklist = ['content', 'address']; // keys to exclude
 				if(is_string($value) && !in_array($key, $strip_blacklist) ){
					$value = wp_strip_all_tags($value);
				}

				// Wrap HTML p tags
				$wrap_whitelist = ['excerpt', 'address']; // keys to include
				if(is_string($value) && in_array($key, $wrap_whitelist, true) ){
					$value = wrap_p_tags($value);
				}

				// Throws value back into function and starts loop again
				if( is_array($value) || is_object($value)){
					$value = formatter::format_sections($value);
				}

				$return[$key] = $value;
			}
		}

		return $return;
	}


}
