<?php

$api_prefix = 'mydataninja/v1';

add_action('rest_api_init', 'mydataninja_register_endpoints');

function mydataninja_check_handler() {
    return [
        'success' => true
    ];
}

// Register the REST API endpoint
function mydataninja_register_endpoints() {
    global $api_prefix;

    register_rest_route( $api_prefix, '/check', [
        'methods'   => 'GET',
        'callback'  => 'mydataninja_check_handler',
    ]);
}

function mdnj_create_attach_website_endpoint() {
  register_rest_route('mydataninja/v1', '/attach-website', array(
    'methods' => 'POST',
    'callback' => 'mdnj_attach_website_route_callback',
  ));
}

add_action('rest_api_init', 'mdnj_create_attach_website_endpoint');

function mdnj_attach_website_route_callback($request) {
  $parameters = $request->get_params();

  $consumer_secret = $request->get_param('consumer_secret');
  if (!mdnj_check_user($consumer_secret)) {
    return new WP_Error('invalid_consumer_key_or_secret', 'Invalid consumer key or secret', array('status' => 403));
  }

  $website_id = isset($parameters['website_id']) ? $parameters['website_id'] : '';

  if (!empty($website_id)) {
    update_option('dataninja_website_id', $website_id);
    return rest_ensure_response(array('success' => true));
  } else {
    return new WP_Error('missing_website_id', 'Website ID is missing', array('status' => 400));
  }
}
