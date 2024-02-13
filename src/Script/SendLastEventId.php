<?php

function send_last_event_id_to_server() {
    if (is_wc_endpoint_url('order-received')) {
        ?>
        <script>
            jQuery(document).ready(function($) {
                function sendLastEventIdToServer() {
                    var lastEventId = nj.getLastEventId();
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'save_last_event_id',
                            last_event_id: lastEventId,
                            order_id: <?php echo json_encode(wc_get_order_id_by_order_key($_GET['key'])); ?>
                        },
                    });
                }

                nj.push(['on', 'onEventIdUpdated', function(e) {
                    sendLastEventIdToServer();
                }]);
            });
        </script>
        <?php
    }
}

add_action('wp_footer', 'send_last_event_id_to_server');

function save_last_event_id_callback() {
    $last_event_id = $_POST['last_event_id'];
    $order_id = $_POST['order_id'];

    update_post_meta($order_id, 'event_id', $last_event_id);

    wp_die();
}

add_action('wp_ajax_save_last_event_id', 'save_last_event_id_callback');

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
