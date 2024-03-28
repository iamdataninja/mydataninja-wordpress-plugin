<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function mdnj_get_and_save_access_token(WP_REST_Request $request) {
  $access_token = $request->get_param('access_token');

  if (!$access_token) {
    return new WP_Error('missing_access_token', 'Access token is missing', array('status' => 400));
  }

  update_option('mdnj_access_token', $access_token);
  return rest_ensure_response(array('success' => true));
}

function mdnj_create_get_token_endpoint() {
  register_rest_route('mydataninja/v1', '/get-token', array(
    'methods' => 'POST',
    'callback' => 'mdnj_get_and_save_access_token',
  ));
}

add_action('rest_api_init', 'mdnj_create_get_token_endpoint');