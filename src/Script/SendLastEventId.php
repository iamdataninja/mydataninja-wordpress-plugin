<?php

if (! defined('ABSPATH')) exit;

function mdnj_modify_order_payload_before_webhook($payload, $resource, $resource_id, $webhook_id)
{
  if ($resource === 'order') {
    $order = wc_get_order($resource_id);
    $session_id = get_post_meta($resource_id, 'nj_session_id', true);

    if ($session_id) {
      $payload['nj_session_id'] = $session_id;
    }
  }

  return $payload;
}

add_filter('woocommerce_webhook_payload', 'mdnj_modify_order_payload_before_webhook', 10, 4);

function mdnj_add_session_id_to_order_object($response, $object, $request)
{
  $session_id = get_post_meta($object->get_id(), 'nj_session_id', true);

  if (empty($session_id)) {
    $session_id = null;
  }

  $data = $response->get_data();
  $data['nj_session_id'] = $session_id;
  $response->set_data($data);

  return $response;
}

add_filter('woocommerce_rest_prepare_shop_order_object', 'mdnj_add_session_id_to_order_object', 10, 3);

function mdnj_add_session_id_in_order($order_id)
{
  $sessionid = isset($_COOKIE['njsession']) ? (int)$_COOKIE['njsession'] : null;

  if ($sessionid) {
    update_post_meta($order_id, 'nj_session_id', $sessionid);
  }
}
add_action('woocommerce_new_order', 'mdnj_add_session_id_in_order', 10, 1);
