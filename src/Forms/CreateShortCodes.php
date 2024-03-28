<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function mydataninja_iframe_shortcode($atts) {
    global $myDataNinjaConfig;
  
    $atts = shortcode_atts(
      array(
        'hash' => '',
      ),
      $atts,
      'mydataninja_iframe'
    );
  
    $iframe = '<iframe src="' . esc_url($myDataNinjaConfig["API_BASE_URL"] . "/ext/form/load/" . $atts['hash']) . '" frameborder="0" width="100%"></iframe>';
  
    return $iframe;
  }
  add_shortcode('mydataninja_iframe', 'mydataninja_iframe_shortcode');
