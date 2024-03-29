<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function mdnj_create_save_form_info_endpoint() {
  register_rest_route('mydataninja/v1', '/save-crm-info', array(
    'methods' => 'POST',
    'callback' => 'mdnj_save_form_hash_callback',
  ));
}

add_action('rest_api_init', 'mdnj_create_save_form_info_endpoint');

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