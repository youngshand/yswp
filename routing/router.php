<?php

require_once(__DIR__.'/../wp_queries.php');

/* Set up the router */
class Router {
	function __construct($site) {
		$this->site = $site;
	}

	public function get($uri, $callback) {
		register_rest_route( $this->site, $uri, array(
        'methods' => 'GET',
        'callback' => $callback,
    ) );
	}

	public function post($uri, $callback) {
		register_rest_route( $this->site, $uri, array(
        'methods' => 'POST',
        'callback' => $callback,
    ) );
	}

}
