<?php
/*
Plugin Name: MyDataNinja WooCommerce Plugin
Description: Effortlessly track and manage profits for your products.
Version: 1.0
Author: <a href="https://github.com/Afcyy">Vazha Aptsiauri</a> | <a href="https://mydataninja.com/">MyDataNinja</a>
Tags: woocommerce, e-commerce, profit, profit tracking, sales, sales management, business analytics
*/

require __DIR__ . '/vendor/autoload.php';

include_once __DIR__ . '/src/Cost of Goods/AddCostOfGoodsField.php';
include_once __DIR__ . '/src/Cost of Goods/AddCogAndProfitToOrders.php';
include_once __DIR__ . '/src/Script/AddScript.php';
include_once __DIR__ . '/src/Script/SendLastEventId.php';
include_once __DIR__ . '/src/Interface/PluginInterface.php';

$myDataNinjaConfig = include __DIR__ . '/config.php';

function set_default_options_on_activation() {
    if (get_option('_include_profits') === false) {
        update_option('_include_profits', 'yes');
    }

    if (get_option('_include_tracker') === false) {
        update_option('_include_tracker', 'yes');
    }
}

register_activation_hook(__FILE__, 'set_default_options_on_activation');
