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