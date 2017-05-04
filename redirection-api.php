<?php
/*
Plugin Name: Redirection API
Version: 0.2-alpha
Description: PLUGIN DESCRIPTION HERE
Author: YOUR NAME HERE
Author URI: YOUR SITE HERE
Plugin URI: PLUGIN SITE HERE
Text Domain: Redirection API
Domain Path: /languages
*/


// Register old v1 routes
function redirection_api_init_v1() {
  $redirection_api = new Redirection_API();
  add_filter( 'json_endpoints', array( $redirection_api, 'register_routes_v1' ) );
}
add_action( 'wp_json_server_before_serve', 'redirection_api_init_v1' );

// Register new v2 routes
function redirection_api_init_v2() {
  $redirection_api = new Redirection_API();
  register_rest_route( '/redirects/v2', '/all', array(
    'methods' => WP_REST_Server::READABLE,
    'callback' => array( $redirection_api, 'get_redirects' ),
  ) );
  register_rest_route( '/redirects', '/(?P<id>\d+)', array(
    'methods' => WP_REST_Server::READABLE,
    'callback' => array( $redirection_api, 'get_redirect' ),
  ) );
  register_rest_route( '/redirects/v2', '/create', array(
    'methods' => WP_REST_Server::CREATABLE,
    'callback' => array( $redirection_api, 'save_redirect' ),
    'args' => array(
      'from' => array(
        'type' => 'string',
      ),
      'to' => array(
        'type' => 'string',
      ),
    ),
  ) );
}
add_action( 'rest_api_init', 'redirection_api_init_v2' );


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
   * @deprecated
   */
  public function register_routes_v1( $routes ) {
    $routes['/redirects'] = array(
      array( array( $this, 'get_redirects_v1' ), WP_JSON_Server::READABLE ),
    );
    $routes['/redirects/(?P<id>\d+)'] = array(
      array( array( $this, 'get_redirect_v1' ), WP_JSON_Server::READABLE ),
    );
    return $routes;
  }

  /**
   * Get redirects
   *
   * @since 0.1-alpha
   * @deprecated
   */
  public function get_redirects_v1() {
    $items = Red_Item::get_all_for_module( 1 );
    return $items;
  }

  /**
   * Get redirects
   *
   * @since 0.2-alpha
   */
  public function get_redirects( $request ) {
    $items = Red_Item::get_all_for_module( 1 );
    return new WP_REST_Response( $items );
  }

  /**
   * Get redirect
   *
   * @since 0.1.0
   * @deprecated
   */
  public function get_redirect_v1( $id ) {
    $item = Red_Item::get_by_id( $id );

    if ( empty( $item ) ) {
      return new WP_Error( 'rest_redirect_invalid_id', 'Redirect not found', array( 'status' => 404 ) );
    }

    return $this->prepare_redirect_for_response( $item );
  }

  /**
   * Get redirect
   *
   * @since 0.2.0
   */
  public function get_redirect( $request ) {
    $params = $request->get_params();
    $item = Red_Item::get_by_id( $params['id'] );

    if ( empty( $item ) ) {
      return new WP_Error( 'rest_redirect_invalid_id', 'Redirect not found', array( 'status' => 404 ) );
    }

    $object = $this->prepare_redirect_for_response( $item );

    return new WP_REST_Response( $object, 200 );
  }

  public function prepare_redirect_for_response( Red_Item $item ) {
    $object = new StdClass;
    $object->id = $item->get_id();
    $object->from = $item->get_url();
    $object->to = $item->get_action_data();

    return $object;
  }
}
