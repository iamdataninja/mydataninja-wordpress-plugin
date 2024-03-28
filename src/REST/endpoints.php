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

function mdnj_save_form_hash_callback($request) {
  $parameters = $request->get_params();

  $forms = isset($parameters['forms']) ? $parameters['forms'] : [];

  if (!empty($forms)) {
    update_option('mdnj_forms', $forms);
    return rest_ensure_response(array('success' => true));
  } else {
    return new WP_Error('missing_forms', 'Forms are missing', array('status' => 400));
  }
}

function mdnj_create_attach_website_endpoint() {
  register_rest_route('mydataninja/v1', '/attach-website', array(
    'methods' => 'POST',
    'callback' => 'mdnj_attach_website_route_callback',
  ));
}

add_action('rest_api_init', 'mdnj_create_attach_website_endpoint');