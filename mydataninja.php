<?php
/*
Plugin Name: MyDataNinja WooCommerce
Description: Effortlessly track and manage profits for your products.
Version: 1.0
Author: <a href="https://github.com/Afcyy">Vazha Aptsiauri</a> | <a href="https://mydataninja.com/">MyDataNinja</a>
Tags: woocommerce, e-commerce, profit, profit tracking, sales, sales management, business analytics
*/

require __DIR__ . '/vendor/autoload.php';
require_once(ABSPATH . 'wp-admin/includes/plugin.php');

require_once __DIR__ . '/lib/woocommerce-dependency-checker.php';
if (!check_woocommerce_dependency()) {
  return;
}

require_once __DIR__ . '/lib/default-options-setter.php';
require_once __DIR__ . '/includes.php';

$myDataNinjaConfig = include __DIR__ . '/config.php';