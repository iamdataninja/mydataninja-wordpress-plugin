<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function mdnj_check_user($consumer_secret_substr) {
  global $wpdb;
  $prefix = 'MyDataNinja - API';

  $results = wp_cache_get('mydataninja_api_keys');

  if ($results === false) {
    $results = $wpdb->get_results($wpdb->prepare(
      "SELECT consumer_key, consumer_secret FROM {$wpdb->prefix}woocommerce_api_keys WHERE description LIKE %s",
      sanitize_text_field($prefix) . '%'
    ), ARRAY_A);

    wp_cache_set('mydataninja_api_keys', $results);
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
    global $myDataNinjaConfig;

    $current_url = home_url(add_query_arg([], $wp->request));

    $include_tracker = get_option('mdnj_include_tracker', 'yes');
    $website_id = get_option('dataninja_website_id');

      if ($include_tracker === 'yes') {
        wp_enqueue_script('mydataninja-tracker-script', plugins_url('assets/js/tracker.js', plugin_dir_path(__DIR__)), [], $myDataNinjaConfig['VERSION'], true);

        wp_enqueue_script('mydataninja-external-script', 'https://static.mydataninja.com/ninja.js', ['mydataninja-tracker-script'], $myDataNinjaConfig['VERSION'], true);
        add_filter('script_loader_tag', function($tag, $handle) use ($website_id) {
          if ($handle !== 'mydataninja-external-script') {
            return $tag;
          }
          return str_replace('<script', '<script data-website="' . esc_attr($website_id) . '"', $tag);
        }, 10, 2);
      }
}

add_action('wp_footer', 'mdnj_add_ninja_script');
