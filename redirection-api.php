<?php
/*
Plugin Name: Redirection API
Version: 0.1-alpha
Description: PLUGIN DESCRIPTION HERE
Author: YOUR NAME HERE
Author URI: YOUR SITE HERE
Plugin URI: PLUGIN SITE HERE
Text Domain: Redirection API
Domain Path: /languages
*/

function redirection_api_init() {
  $redirection_api = new Redirection_API();
  add_filter( 'json_endpoints', array( $redirection_api, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'redirection_api_init' );

/**
 * WP JSON Redirects
 *
 * @package WP API Redirection
 *
 * @since 0.1-alpha
 */
class Redirection_API {
  /**
   * Register redirects routes for WP API
   *
   * @since 0.1-alpha
   */
  public function register_routes( $routes ) {
    $routes['/redirects'] = array(
      array( array( $this, 'get_redirects' ), WP_JSON_Server::READABLE ),
    );

    $routes['/redirects/(?P<id>\d+)'] = array(
      array( array( $this, 'get_redirect' ), WP_JSON_Server::READABLE ),
    );

    return $routes;
  }

  /**
   * Get redirects
   *
   * @since 0.1-alpha
   */
  public function get_redirects() {
    return Red_Item::get_all_for_module( 1 );
  }

  /**
   * Get redirect
   *
   * @since 0.1.0
   */
  public function get_redirect( $id ) {
    return Red_Item::get_by_id( $id );
  }
}
