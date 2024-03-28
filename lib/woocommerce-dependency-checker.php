<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function mdnj_check_woocommerce_dependency() {
  if (!is_plugin_active('woocommerce/woocommerce.php')) {
    deactivate_plugins(plugin_basename('mydataninja/mydataninja.php'));
    add_action('admin_notices', 'mdnj_show_woocommerce_dependency_error');
    return false;
  }

  return true;
}

function mdnj_show_woocommerce_dependency_error() {
  ?>
    <div class="error">
        <p><?php esc_html_e('MyDataNinja Plugin has been deactivated because WooCommerce is not active.', 'mydataninja-ad-performance-tracking-order-reports-crm-analytics-and-optimization-tools'); ?></p>
    </div>
  <?php
}

mdnj_check_woocommerce_dependency();