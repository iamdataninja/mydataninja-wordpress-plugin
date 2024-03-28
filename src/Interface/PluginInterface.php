<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function mdnj_add_plugin_interface_menu() {
  add_menu_page(
    'MyDataNinja WooCommerce Plugin',
    'MyDataNinja',
    'manage_options',
    'mydataninja-integration',
    'mdnj_display_plugin_interface',
    plugins_url('../assets/icons/mydataninja.png', __DIR__),
    56
  );

  if (mdnj_is_api_key_authorized()){
    add_submenu_page(
      'mydataninja-integration',
      'Reports',
      'Reports',
      'manage_options',
      'mydataninja-reports',
      'mdnj_display_reports_interface'
    );
  }

  add_submenu_page(
    'mydataninja-integration',
    'Settings',
    'Settings',
    'manage_options',
    'mydataninja-settings',
    'mdnj_display_settings_interface'
  );

  remove_submenu_page('mydataninja-integration', 'mydataninja-integration');

  echo '<style>
        #toplevel_page_mydataninja-integration img {
            max-width: 21px;
            max-height: 21px;
            filter: grayscale(100%) brightness(200%);
        }
    </style>';
}

function mdnj_display_reports_interface() {
  mdnj_save_options();

  $is_reports_page = true;
  $is_settings_page = false;

  call_user_func(function() use ($is_reports_page, $is_settings_page) {
    include(plugin_dir_path(__DIR__) . '../templates/index.php');
  });
}

function mdnj_display_settings_interface() {
  mdnj_save_options();

  $is_reports_page = false;
  $is_settings_page = true;

  call_user_func(function() use ($is_reports_page, $is_settings_page) {
    include(plugin_dir_path(__DIR__) . '../templates/index.php');
  });
}

add_action('admin_menu', 'mdnj_add_plugin_interface_menu');

function mdnj_is_api_key_authorized() {
  global $wpdb;
  $prefix = 'MyDataNinja - API';

  $cache_key = 'mydataninja_api_key_count';
  $result = wp_cache_get($cache_key);

  if ($result === false) {
    $result = $wpdb->get_var(
      $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_api_keys WHERE description LIKE %s",
        $prefix . '%'
      )
    );
    wp_cache_set($cache_key, $result);
  }

  return $result > 0;
}

function mdnj_display_plugin_interface() {
    mdnj_save_options();

    $current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
    $is_reports_page = $current_page === 'mydataninja-reports' || $current_page === 'mydataninja-integration';
    $is_settings_page = $current_page === 'mydataninja-settings';

    call_user_func(function() use ($is_reports_page, $is_settings_page) {
      include(plugin_dir_path(__DIR__) . '../templates/index.php');
    });
}

function mdnj_save_options()
{
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nonce_field = isset($_POST['mydataninja_nonce_field']) ? sanitize_text_field(wp_unslash($_POST['mydataninja_nonce_field'])) : '';

    if (!wp_verify_nonce($nonce_field, 'mydataninja_nonce')) {
      return;
    }

    $include_profits = isset($_POST['_include_profits']) ? sanitize_text_field($_POST['_include_profits']) : 'no';
    update_option('mdnj_include_profits', $include_profits === 'on' ? 'yes' : 'no');

    if ($include_profits === 'yes') {
      update_option('mdnj_existing_cog_field_name', '_mydataninja_cost_of_goods');
    }

    if (isset($_POST['_default_profit_margin'])) {
      $default_profit_margin = sanitize_text_field($_POST['_default_profit_margin']);
      update_option('mdnj_default_profit_margin', $default_profit_margin);
    }

    $include_tracker = isset($_POST['_include_tracker']) ? sanitize_text_field($_POST['_include_tracker']) : 'no';
    update_option('mdnj_include_tracker', $include_tracker === 'on' ? 'yes' : 'no');

    $use_existing_cog_field = isset($_POST['_use_existing_cog_field']) ? sanitize_text_field($_POST['_use_existing_cog_field']) : 'no';
    update_option('mdnj_use_existing_cog_field', $use_existing_cog_field === 'on' ? 'yes' : 'no');

    $existing_cog_field_name = isset($_POST['_existing_cog_field_name']) ? sanitize_text_field($_POST['_existing_cog_field_name']) : '_mydataninja_cost_of_goods';
    update_option('mdnj_existing_cog_field_name', $existing_cog_field_name);
  }
}

function mdnj_enqueue_custom_styles() {
  global $myDataNinjaConfig;

  wp_enqueue_style('mydataninja-custom-style', plugins_url('assets/css/style.css', plugin_dir_path(__DIR__)), [], $myDataNinjaConfig['VERSION']);
  wp_enqueue_script('mydataninja-chart-script', plugins_url('assets/js/chart.js', plugin_dir_path(__DIR__)), [], $myDataNinjaConfig['VERSION'], true);
  wp_enqueue_script('mydataninja-integration-interface-script', plugins_url('assets/js/plugin-interface.js', plugin_dir_path(__DIR__)), ['mydataninja-chart-script'], $myDataNinjaConfig['VERSION'], true);

  $orderStatistics = mdnj_get_order_statistics();
  wp_localize_script('mydataninja-integration-interface-script', 'php_vars', [
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

add_action('admin_enqueue_scripts', 'mdnj_enqueue_custom_styles');

add_action('admin_enqueue_scripts', 'mdnj_enqueue_custom_styles');

function mdnj_get_order_statistics() {
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