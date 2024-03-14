<?php

function modify_order_payload_before_webhook($payload, $resource, $resource_id, $webhook_id) {
    if ($resource === 'order') {
        $order = wc_get_order($resource_id);
        $event_id = get_post_meta($resource_id, 'event_id', true);

        if ($event_id) {
            $payload['event_id'] = $event_id;
        }
    }

    return $payload;
}

add_filter('woocommerce_webhook_payload', 'modify_order_payload_before_webhook', 10, 4);

function add_event_id_to_order_object($response, $object, $request) {
    $event_id = get_post_meta($object->get_id(), 'event_id', true);

    if (empty($event_id)) {
        $event_id = null;
    }

    $data = $response->get_data();
    $data['event_id'] = $event_id;
    $response->set_data($data);

    return $response;
}

add_filter('woocommerce_rest_prepare_shop_order_object', 'add_event_id_to_order_object', 10, 3);

function add_njeventid_to_order( $order_id ) {
  $njeventid = isset($_COOKIE['njeventid']) ? $_COOKIE['njeventid'] : null;
  
  if(!$njeventid) return;
  
  update_post_meta($order_id, 'event_id', $njeventid);
}

add_action('woocommerce_checkout_order_processed', 'add_njeventid_to_order', 10, 1);
