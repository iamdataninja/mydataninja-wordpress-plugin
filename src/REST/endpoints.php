<?php

if ( ! defined( 'ABSPATH' ) ) exit;

$mdnj_api_prefix = 'mydataninja/v1';

add_action('rest_api_init', 'mdnj_register_endpoints');

function mdnj_check_handler() {
    return [
        'success' => true
    ];
}

// Register the REST API endpoint
function mdnj_register_endpoints() {
    global $mdnj_api_prefix;

    register_rest_route( $mdnj_api_prefix, '/check', [
        'methods'   => 'GET',
        'callback'  => 'mdnj_check_handler',
        'permission_callback' => '__return_true',
    ]);
}