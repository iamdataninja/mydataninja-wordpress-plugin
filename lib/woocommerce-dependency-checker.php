<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function check_woocommerce_dependency() {
  if (is_plugin_active('woocommerce/woocommerce.php')) {
    return true;
  } else {
    add_action('admin_notices', 'mydataninja_woocommerce_dependency_notice');
    return false;
  }
}

function mydataninja_woocommerce_dependency_notice() {
  ?>
    <div class="notice notice-warning is-dismissible">
        <p><?php _e('The MyDataNinja Plugin is active but has limited functionality as WooCommerce is inactive.





', 'mydataninja'); ?></p>
    </div>
  <?php
}

check_woocommerce_dependency();