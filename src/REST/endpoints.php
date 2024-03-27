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

  $form_hash = isset($parameters['hash']) ? $parameters['hash'] : '';
  $form_id = isset($parameters['form_id']) ? $parameters['form_id'] : '';

  if (!empty($form_hash)) {
    update_option('mdnj_form_hash', $form_hash);
  }

  if (!empty($form_id)) {
    update_option('mdnj_form_id', $form_id);
  }

  if (!empty($form_hash) || !empty($form_id)) {
    return rest_ensure_response(array('success' => true));
  } else {
    return new WP_Error('missing_form_hash_or_form_id', 'Form Hash or Form ID is missing', array('status' => 400));
  }
}

function mdnj_create_save_form_info_endpoint() {
  register_rest_route('mydataninja/v1', '/save-crm-info', array(
    'methods' => 'POST',
    'callback' => 'mdnj_save_form_hash_callback',
  ));
}

add_action('rest_api_init', 'mdnj_create_save_form_info_endpoint');

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

function mdnj_create_attach_website_endpoint() {
  register_rest_route('mydataninja/v1', '/attach-website', array(
    'methods' => 'POST',
    'callback' => 'mdnj_attach_website_route_callback',
  ));
}

add_action('rest_api_init', 'mdnj_create_attach_website_endpoint');