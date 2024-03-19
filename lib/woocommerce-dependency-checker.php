<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function check_woocommerce_dependency() {
  if (!is_plugin_active('woocommerce/woocommerce.php')) {
    deactivate_plugins(plugin_basename('mydataninja/mydataninja.php'));
    add_action('admin_notices', 'show_woocommerce_dependency_error');
    return false;
  }

  return true;
}

function show_woocommerce_dependency_error() {
  ?>
    <div class="error">
        <p><?php esc_html_e('MyDataNinja Plugin has been deactivated because WooCommerce is not active.', 'mydataninja'); ?></p>
    </div>
  <?php
}

check_woocommerce_dependency();