<?php

function attach_website_route_callback($request) {
    $parameters = $request->get_params();

    $consumer_secret = $request->get_param('consumer_secret');
    if (!check_user($consumer_secret)) {
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

function create_attach_website_endpoint() {
    register_rest_route('mydataninja/v1', '/attach-website', array(
        'methods' => 'POST',
        'callback' => 'attach_website_route_callback',
    ));
}

add_action('rest_api_init', 'create_attach_website_endpoint');

function check_user($consumer_secret_substr) {
    global $wpdb;
    $prefix = 'MyDataNinja - API';

    $query = $wpdb->prepare(
        "SELECT consumer_key, consumer_secret FROM {$wpdb->prefix}woocommerce_api_keys WHERE description LIKE %s",
        $prefix . '%'
    );

    $results = $wpdb->get_results($query, ARRAY_A);

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


function add_ninja_script() {
    global $wp;
    $current_url = home_url(add_query_arg([], $wp->request));

    $include_tracker = get_option('_include_tracker', 'yes');
    $website_id = get_option('dataninja_website_id');

    if ($include_tracker === 'yes') {
        ?>
        <script type="text/javascript" data-website="<?php echo esc_attr($website_id); ?>" src="https://static.mydataninja.com/ninja.js" async defer></script>
        <script type="text/javascript">
            var nj = window.nj || [];            
            nj.push(["init", {}]);
        </script>
        <?php
    }
}

add_action('wp_footer', 'add_ninja_script');