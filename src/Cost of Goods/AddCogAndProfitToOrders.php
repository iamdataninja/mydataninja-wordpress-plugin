<?php

function calculate_order_cost_of_goods($order_id) {
    $order = wc_get_order($order_id);
    $items = $order->get_items();

    $total_cost_of_goods = 0;

    foreach ($items as $item) {
        $product_id = $item->get_product_id();

        $cost_of_goods = get_post_meta($product_id, '_cost_of_goods', true);

        $total_cost_of_goods += !empty($cost_of_goods) ? floatval($cost_of_goods) : 0;
    }

    return $total_cost_of_goods;
}

function calculate_order_profit($order_id) {
    $order = wc_get_order($order_id);
    $items = $order->get_items();

    $total_profit = 0;

    foreach ($items as $item) {
        $product_id = $item->get_product_id();

        $cost_of_goods = get_post_meta($product_id, '_cost_of_goods', true);
        $product = wc_get_product($product_id);
        $price = $product ? $product->get_price() : 0;

        if (!empty($cost_of_goods) && $product) {
            $profit_fixed = $price - $cost_of_goods;
            $profit_percentage = ($profit_fixed / $cost_of_goods) * 100;

            $total_profit += $profit_fixed;
        }
    }

    return $total_profit;
}

function add_profit_and_cost_of_goods_to_order_response($response, $object, $request) {
    $order_id = $object->get_id();
    $response->data['profit'] = calculate_order_profit($order_id);
    $response->data['cost_of_goods'] = calculate_order_cost_of_goods($order_id);

    return $response;
}

add_filter('woocommerce_rest_prepare_shop_order_object', 'add_profit_and_cost_of_goods_to_order_response', 11092002, 3);