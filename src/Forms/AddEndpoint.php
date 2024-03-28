<?php

if ( ! defined( 'ABSPATH' ) ) exit;

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