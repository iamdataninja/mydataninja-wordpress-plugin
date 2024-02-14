<?php

global $cog_field_name;
$cog_field_name = get_option('_existing_cog_field_name', '_mydataninja_cost_of_goods');

function calculate_order_cost_of_goods($order_id)
{
  global $cog_field_name;

  $order = wc_get_order($order_id);
  $items = $order->get_items();

  $total_cost_of_goods = 0;

  foreach ($items as $item) {
    $product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();

    $cost_of_goods = get_post_meta($product_id, $cog_field_name, true);

    $total_cost_of_goods += !empty($cost_of_goods) ? floatval($cost_of_goods) : 0;
  }

  return $total_cost_of_goods;
}

function calculate_order_profit($order_id)
{
  global $cog_field_name;

  $order = wc_get_order($order_id);
  $items = $order->get_items();

  $total_profit = 0;

  foreach ($items as $item) {
    $product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();

    $cost_of_goods = get_post_meta($product_id, $cog_field_name, true);
    $product = wc_get_product($product_id);
    $price = $product ? $product->get_price() : 0;

    if (!empty($cost_of_goods) && $product) {
      $profit_fixed = $price - $cost_of_goods;
      $total_profit += $profit_fixed;
    }
  }

  return $total_profit;
}

function add_profit_and_cost_of_goods_to_order_response($response, $object) {
  $order_id = $object->get_id();
  $response->data['profit'] = calculate_order_profit($order_id);
  $response->data['cost_of_goods'] = calculate_order_cost_of_goods($order_id);

  return $response;
}

add_filter('woocommerce_rest_prepare_shop_order_object', 'add_profit_and_cost_of_goods_to_order_response', 11092002, 2);