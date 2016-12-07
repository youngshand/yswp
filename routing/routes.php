<?php

require_once(__DIR__.'/router.php');

/* Register routes */
add_action( 'rest_api_init', function () {

    /**
     * Instantiate new route with site name
     */
    $router = new Router('merw');

    /**
     * Create endpoints 
     * 
     * $router->method($uri, $callback)
     */
    $router->get('/pages','merw_get_all_pages');
    $router->get('/settings','merw_get_all_settings');

    $router->post('/signups','merw_save_signups');
} );
