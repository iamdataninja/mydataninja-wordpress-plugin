<?php

function calculate_order_profits($order_id) {
    $order = wc_get_order($order_id);
    $items = $order->get_items();

    $total_profit = 0;

    foreach ($items as $item) {
        $product_id = $item->get_product_id();

        $profit_number = get_post_meta($product_id, '_profit_number', true);
        $profit_percent = get_post_meta($product_id, '_profit_percent', true);

        $total_profit += !empty($profit_number) 
        ? floatval($profit_number) 
        : ($item->get_total() * floatval($profit_percent)) / 100;
    }

    return $total_profit;
}

function add_profit_info_to_order_response($response, $object, $request) {
    $order_id = $object->get_id();
    $response->data['profit'] = calculate_order_profits($order_id);

    return $response;
}

add_filter('woocommerce_rest_prepare_shop_order_object', 'add_profit_info_to_order_response', 11092002, 3);