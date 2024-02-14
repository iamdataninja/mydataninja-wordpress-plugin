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
        update_option('_include_profits', isset($_POST['_include_profits']) ? 'yes' : 'no');
        update_option('_include_tracker', isset($_POST['_include_tracker']) ? 'yes' : 'no');
        update_option('_use_existing_cog_field', isset($_POST['_use_existing_cog_field']) ? 'yes' : 'no');
        update_option('_existing_cog_field_name', isset($_POST['_existing_cog_field_name']) ? sanitize_text_field($_POST['_existing_cog_field_name']) : '_mydataninja_cost_of_goods');
    }

    include(plugin_dir_path(__DIR__) . '../templates/index.php');
}

function enqueue_custom_styles() {
    global $myDataNinjaConfig;

    wp_enqueue_style('mydataninja-custom-style', plugins_url('assets/css/style.css', plugin_dir_path(__DIR__)));
    wp_enqueue_script('mydataninja-custom-script', plugins_url('assets/js/authorize.js', plugin_dir_path(__DIR__)), [], null, true);

    wp_localize_script('mydataninja-custom-script', 'mydataninja_vars', [
        'base_url' => home_url(),
        'currency' => get_woocommerce_currency(),
        'name' => get_bloginfo('name'),
        'front_base_url' => $myDataNinjaConfig['FRONT_BASE_URL']
    ]);
}

add_action('admin_enqueue_scripts', 'enqueue_custom_styles');
