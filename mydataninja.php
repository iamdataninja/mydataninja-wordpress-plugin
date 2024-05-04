<?php
/*
Plugin Name: MyDataNinja - Ad Performance Tracking, Order Reports, CRM, Analytics, and Optimization Tools
Description: One, Easy Solution to Create, Track and Optimize Digital Marketing Campaigns.
Version: 1.0.3
Author: <a href="https://mydataninja.com/">MyDataNinja</a>
Tags: woocommerce, e-commerce, google ads, facebook ads, meta ads, ppc, digital advertising, sales, sales management, business analytics, woocommerce, e-commerce, google ads, facebook ads, digital advertising
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if (!defined('ABSPATH')) exit;

require __DIR__ . '/vendor/autoload.php';
require_once(ABSPATH . 'wp-admin/includes/plugin.php');

require_once __DIR__ . '/lib/woocommerce-dependency-checker.php';
if (!mdnj_check_woocommerce_dependency()) {
  return;
}

require_once __DIR__ . '/lib/default-options-setter.php';
mdnj_set_default_options_on_activation(__FILE__);

require_once __DIR__ . '/functions.php';
include_once __DIR__ . '/src/CostOfGoods/AddCostOfGoodsField.php';
include_once __DIR__ . '/src/CostOfGoods/AddCogAndProfitToOrders.php';
include_once __DIR__ . '/src/Script/AddScript.php';
include_once __DIR__ . '/src/Script/SendLastEventId.php';
include_once __DIR__ . '/src/Interface/PluginInterface.php';
include_once __DIR__ . '/src/AccessToken/SaveAccessToken.php';
include_once __DIR__ . '/src/REST/endpoints.php';

register_activation_hook(__FILE__, 'mydataninja_activate');
function mydataninja_activate()
{
  set_transient('mydataninja_activation_redirect', true, 30);
}

add_action('admin_init', 'mydataninja_redirect_after_activation');
function mydataninja_redirect_after_activation()
{
  // Check if the transient is set
  if (get_transient('mydataninja_activation_redirect')) {
    // Delete the transient
    delete_transient('mydataninja_activation_redirect');

    // Redirect to the desired page (change 'my-plugin-settings' to your actual page slug)
    if (is_admin() && !isset($_GET['activate-multi'])) {
      wp_safe_redirect(admin_url(mydataninja_config('REDIRECT_AFTER_ACTIVATION')));
      exit;
    }
  }
}
