<?php

if ( ! defined( 'ABSPATH' ) ) exit;

global $mdnj_cog_field_name;
$mdnj_cog_field_name = get_option('mdnj_existing_cog_field_name', 'mdnj_cost_of_goods');

function mdnj_calculate_order_cost_of_goods($order_id)
{
    global $mdnj_cog_field_name;

    $order = wc_get_order($order_id);
    $items = $order->get_items();

    $total_cost_of_goods = 0;

    foreach ($items as $item) {
        $product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();

        $cost_of_goods = get_post_meta($product_id, $mdnj_cog_field_name, true);

        $total_cost_of_goods += !empty($cost_of_goods) ? floatval($cost_of_goods) : 0;
    }

    return $total_cost_of_goods;
}

function mdnj_calculate_order_profit($order_id)
{
    global $mdnj_cog_field_name;

    $order = wc_get_order($order_id);
    $items = $order->get_items();

    $total_profit = 0;
    $default_profit_margin = (float)get_option('mdnj_default_profit_margin', 0);

    foreach ($items as $item) {
        $product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();

        $cost_of_goods = (float)get_post_meta($product_id, $mdnj_cog_field_name, true);
        $product = wc_get_product($product_id);
        $price = $product ? (float)$product->get_price() : 0;

        $include_profits = get_option('mdnj_include_profits', 'no');
        if($include_profits == 'yes'){
            if (!empty($cost_of_goods) && $cost_of_goods > 0) {
                $profit_fixed = $price - $cost_of_goods;
            } else {
                $profit_fixed = ($price * $default_profit_margin / 100);
            }
        }

        $total_profit += $profit_fixed ?? 0;
    }

    return $total_profit;
}

function mdnj_add_profit_and_cost_of_goods_to_order_response($response, $object) {
    $order_id = $object->get_id();
    $response->data['mydataninja_profit'] = mdnj_calculate_order_profit($order_id);
    $response->data['mydataninja_cost_of_goods'] = mdnj_calculate_order_cost_of_goods($order_id);

    return $response;
}

add_filter('woocommerce_rest_prepare_shop_order_object', 'mdnj_add_profit_and_cost_of_goods_to_order_response', 11092002, 2);