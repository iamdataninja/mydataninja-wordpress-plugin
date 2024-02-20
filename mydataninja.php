<?php
/*
Plugin Name: MyDataNinja - Ad Performance Tracking, Order Reports, CRM, Analytics, and Optimization Tools
Description: One, Easy Solution to Create, Track and Optimize Digital Marketing Campaigns.
Version: 1.0
Author: <a href="https://github.com/Afcyy">Vazha Aptsiauri</a> | <a href="https://mydataninja.com/">MyDataNinja</a>
Tags: woocommerce, e-commerce, google ads, facebook ads, meta ads, ppc, digital advertising, sales, sales management, business analytics
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