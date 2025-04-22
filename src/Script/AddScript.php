<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function mdnj_attach_website_route_callback($request) {
    $parameters = $request->get_params();

    $consumer_secret = $request->get_param('consumer_secret');
    if (!mdnj_check_user($consumer_secret)) {
        return new WP_Error('invalid_consumer_key_or_secret', 'Invalid consumer key or secret', array('status' => 403));
    }

    $website_id = isset($parameters['website_id']) ? $parameters['website_id'] : '';

    if (!empty($website_id)) {
        update_option('mdnj_dataninja_website_id', $website_id);
        return rest_ensure_response(array('success' => true));
    } else {
        return new WP_Error('missing_website_id', 'Website ID is missing', array('status' => 400));
    }
}

function mdnj_create_attach_website_endpoint() {
    register_rest_route('mydataninja/v1', '/attach-website', array(
        'methods' => 'POST',
        'callback' => 'mdnj_attach_website_route_callback',
        'permission_callback' => '__return_true',
    ));
}

add_action('rest_api_init', 'mdnj_create_attach_website_endpoint');

function mdnj_check_user($consumer_secret_substr) {
  global $wpdb;
  $prefix = 'MyDataNinja - API';

  $results = wp_cache_get('mdnj_api_keys');

  if ($results === false) {
    $results = $wpdb->get_results($wpdb->prepare(
      "SELECT consumer_key, consumer_secret FROM {$wpdb->prefix}woocommerce_api_keys WHERE description LIKE %s",
      sanitize_text_field($prefix) . '%'
    ), ARRAY_A);

    wp_cache_set('mdnj_api_keys', $results);
  }

  if ($results) {
    foreach ($results as $row) {
      $consumer_secret = $row['consumer_secret'];

      if(substr($consumer_secret, -7) == $consumer_secret_substr){
        return True;
      }
    }
  }

  return False;
}

function mdnj_add_ninja_script() {
    global $wp;
    global $mdnj_myDataNinjaConfig;

    $current_url = home_url(add_query_arg([], $wp->request));

    $include_tracker = get_option('mdnj_include_tracker', 'yes');
    $website_id = get_option('mdnj_dataninja_website_id');

      if ($include_tracker === 'yes') {
        wp_enqueue_script('mydataninja-tracker-script', plugins_url('assets/js/tracker.js', plugin_dir_path(__DIR__)), [], $mdnj_myDataNinjaConfig['VERSION'], true);

        wp_enqueue_script('mydataninja-external-script', 'https://static.mydataninja.com/ninja.js', ['mydataninja-tracker-script'], $mdnj_myDataNinjaConfig['VERSION'], true);
        add_filter('script_loader_tag', function($tag, $handle) use ($website_id) {
          if ($handle !== 'mydataninja-external-script') {
            return $tag;
          }
          return str_replace('<script', '<script data-website="' . esc_attr($website_id) . '"', $tag);
        }, 10, 2);
      }
}

add_action('wp_footer', 'mdnj_add_ninja_script');