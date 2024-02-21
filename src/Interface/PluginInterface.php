<?php

function add_plugin_interface_menu() {
  add_menu_page(
      'MyDataNinja WooCommerce Plugin',
      'MyDataNinja',
      'manage_options',
      'mydataninja-plugin',
      'display_plugin_interface',
      plugins_url('../assets/icons/mydataninja.png', __DIR__),
      56
  );
  echo '<style>
        #toplevel_page_mydataninja-plugin img {
            max-width: 21px;
            max-height: 21px;
            filter: grayscale(100%) brightness(200%);
        }
    </style>';
}

add_action('admin_menu', 'add_plugin_interface_menu');

function is_api_key_authorized() {
    global $wpdb;
    $prefix = 'MyDataNinja - API';

    $result = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_api_keys WHERE description LIKE %s",
            $prefix . '%'
        )
    );

    return $result > 0;
}

function display_plugin_interface() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['mydataninja_nonce_field'])) {
          return;
        }

        if (!wp_verify_nonce($_POST['mydataninja_nonce_field'], 'mydataninja_nonce')) {
          return;
        }

        update_option('_include_profits', isset($_POST['_include_profits']) ? 'yes' : 'no');
        if (isset($_POST['_include_profits'])) {
          update_option('_existing_cog_field_name', '_mydataninja_cost_of_goods');
        }

        update_option('_include_tracker', isset($_POST['_include_tracker']) ? 'yes' : 'no');
        update_option('_use_existing_cog_field', isset($_POST['_use_existing_cog_field']) ? 'yes' : 'no');
        update_option('_existing_cog_field_name', isset($_POST['_existing_cog_field_name']) ? sanitize_text_field($_POST['_existing_cog_field_name']) : '_mydataninja_cost_of_goods');
    }

    include(plugin_dir_path(__DIR__) . '../templates/index.php');
}

function enqueue_custom_styles() {
  global $myDataNinjaConfig;

  $style_path = plugin_dir_path(__DIR__) . 'assets/css/style.css';
  $style_version = filemtime($style_path);

  $authorize_script_path = plugin_dir_path(__DIR__) . 'assets/js/authorize.js';
  $authorize_script_version = filemtime($authorize_script_path);

  $plugin_interface_script_path = plugin_dir_path(__DIR__) . 'assets/js/plugin-interface.js';
  $plugin_interface_script_version = filemtime($plugin_interface_script_path);

  wp_enqueue_style('mydataninja-custom-style', plugins_url('assets/css/style.css', plugin_dir_path(__DIR__)), [], $style_version);
  wp_enqueue_script('mydataninja-authorize-script', plugins_url('assets/js/authorize.js', plugin_dir_path(__DIR__)), [], $authorize_script_version, true);
  wp_enqueue_script('mydataninja-plugin-interface-script', plugins_url('assets/js/plugin-interface.js', plugin_dir_path(__DIR__)), [], $plugin_interface_script_version, true);

  wp_localize_script('mydataninja-authorize-script', 'mydataninja_vars', [
    'base_url' => home_url(),
    'currency' => get_woocommerce_currency(),
    'name' => get_bloginfo('name'),
    'front_base_url' => $myDataNinjaConfig['FRONT_BASE_URL']
  ]);

  $orderStatistics = get_order_statistics();
  wp_localize_script('mydataninja-plugin-interface-script', 'php_vars', [
    'accessToken' => get_option('mydataninja_access_token'),
    'currencySymbol' => get_woocommerce_currency_symbol(),
    'apiBaseUrl' => $myDataNinjaConfig['API_BASE_URL'],
    'todayOrders' => $orderStatistics['today']['count'],
    'todayAverage' => $orderStatistics['today']['average'],
    'monthOrders' => $orderStatistics['month']['count'],
    'monthAverage' => $orderStatistics['month']['average'],
    'allTimeOrders' => $orderStatistics['allTime']['count'],
    'allTimeAverage' => $orderStatistics['allTime']['average'],
  ]);
}

add_action('admin_enqueue_scripts', 'enqueue_custom_styles');

function get_order_statistics() {
  $today = gmdate('Y-m-d');
  $firstDayOfMonth = gmdate('Y-m-01');
  $dateRanges = [
    'today' => $today,
    'month' => $firstDayOfMonth . '...' . $today,
    'allTime' => ''
  ];
  $statistics = [];

  foreach ($dateRanges as $period => $dateRange) {
    $orders = wc_get_orders(['return' => 'ids', 'date_created' => $dateRange]);
    $total = array_reduce($orders, function ($carry, $orderId) {
      $order = wc_get_order($orderId);
      return $carry + $order->get_total();
    }, 0);
    $average = count($orders) ? $total / count($orders) : 0;
    $statistics[$period] = ['count' => count($orders), 'average' => $average];
  }

  return $statistics;
}