<?php
/*
Plugin Name: MyDataNinja WooCommerce Plugin
Description: Effortlessly track and manage profits for your products.
Version: 1.0
Author: <a href="https://github.com/Afcyy">Vazha Aptsiauri</a> | <a href="https://mydataninja.com/">MyDataNinja</a>
Tags: woocommerce, e-commerce, profit, profit tracking, sales, sales management, business analytics
*/

require __DIR__ . '/vendor/autoload.php';
require_once(ABSPATH . 'wp-admin/includes/plugin.php');

function show_woocommerce_dependency_error() {
  if (!is_plugin_active('woocommerce/woocommerce.php')) {
    ?>
      <div class="error">
          <p><?php _e('MyDataNinja WooCommerce Plugin has been deactivated because WooCommerce is not active.', 'mydataninja'); ?></p>
      </div>
    <?php
  }
}

if (!is_plugin_active('woocommerce/woocommerce.php')) {
  deactivate_plugins(plugin_basename('mydataninja/mydataninja.php'));
  add_action('admin_notices', 'show_woocommerce_dependency_error');
  return;
}

include_once __DIR__ . '/src/CostOfGoods/AddCostOfGoodsField.php';
include_once __DIR__ . '/src/CostOfGoods/AddCogAndProfitToOrders.php';
include_once __DIR__ . '/src/Script/AddScript.php';
include_once __DIR__ . '/src/Script/SendLastEventId.php';
include_once __DIR__ . '/src/Interface/PluginInterface.php';
include_once __DIR__ . '/src/AccessToken/SaveAccessToken.php';

$myDataNinjaConfig = include __DIR__ . '/config.php';

function set_default_options_on_activation() {
  if (get_option('_include_profits') === false) {
    update_option('_include_profits', 'yes');
  }

  if (get_option('_include_tracker') === false) {
    update_option('_include_tracker', 'yes');
  }

  if (get_option('_use_existing_cog_field') === false) {
    update_option('_use_existing_cog_field', 'no');
  }

  if (get_option('_existing_cog_field_name') === false) {
    update_option('_existing_cog_field_name', '_mydataninja_cost_of_goods');
  }
}

register_activation_hook(__FILE__, 'set_default_options_on_activation');