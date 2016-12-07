<?php

add_action('init', 'ys_setup_post_types');
add_action('init', 'ys_setup_taxonomies', 0 );

/* Register the custom post types we need */
function ys_setup_post_types() {

    // Articles
    register_post_type(
        'articles',
        array(
            'label' => 'Articles',
            'labels' => array(
                'name' => 'Articles',
                'singular_name' => 'Article'
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => true,
            'menu_icon' => 'dashicons-format-aside',
            'query_var' => true,
            'rewrite' => array('slug' => '/articles'),
            'supports' => array('title'),
        )
    );

     // Sign up
    register_post_type(
        'signup',
        array(
            'label' => 'Sign Ups',
            'labels' => array(
                'name' => 'Sign Ups',
                'singular_name' => 'Sign Up',
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => true,
            'menu_icon' => 'dashicons-clipboard',
            'query_var' => true,
            'rewrite' => array('slug' => '/sign-ups'),
            'supports' => array('title'),
        )
    );

}

/* Register any custom taxonomies */
function ys_setup_taxonomies() {
    // News Tags
    register_taxonomy(
  		'tag',
  		'news',
  		array(
  			'label' => 'Tags',
  			'rewrite' => array( 'slug' => 'tag' ),
  			'hierarchical' => false,
  		)
  	);
    // News Categories
    register_taxonomy(
  		'category',
  		'news',
  		array(
  			'label' => 'Categories',
  			'rewrite' => array( 'slug' => 'category' ),
  			'hierarchical' => true,
  		)
  	);
}
