<?php

function add_profit_field() {
    global $woocommerce, $post;

    $include_profits = get_option('_include_profits', 'yes');

    if ($include_profits === 'yes') {
        echo '<div class="options_group">';

        woocommerce_wp_text_input([
            'id' => '_profit_number',
            'label' => __('Profit (Number)', 'mydataninja-woocommerce-plugin'),
            'placeholder' => __('Enter profit amount', 'mydataninja-woocommerce-plugin'),
            'desc_tip' => 'true',
            'description' => __('Enter the profit amount as a fixed number.', 'mydataninja-woocommerce-plugin'),
        ]);

        woocommerce_wp_text_input([
            'id' => '_profit_percent',
            'label' => __('Profit (Percent)', 'mydataninja-woocommerce-plugin'),
            'placeholder' => __('Enter profit percentage', 'mydataninja-woocommerce-plugin'),
            'desc_tip' => 'true',
            'description' => __('Enter the profit percentage as a percentage value.', 'mydataninja-woocommerce-plugin'),
            'type' => 'number',
            'custom_attributes' => [
                'step' => 'any',
            ],
        ]);

        echo '</div>';
    }
}

add_action('woocommerce_product_options_general_product_data', 'add_profit_field');

function save_profit_field($post_id) {
    $profit_number = isset($_POST['_profit_number']) ? sanitize_text_field($_POST['_profit_number']) : '';
    $profit_percent = isset($_POST['_profit_percent']) ? sanitize_text_field($_POST['_profit_percent']) : '';

    update_post_meta($post_id, '_profit_number', $profit_number);
    update_post_meta($post_id, '_profit_percent', $profit_percent);
}

add_action('woocommerce_process_product_meta', 'save_profit_field');

function display_profit_field() {
    global $post;

    $profit_number = get_post_meta($post->ID, '_profit_number', true);
    $profit_percent = get_post_meta($post->ID, '_profit_percent', true);

    if (!empty($profit_number)) {
        echo '<p><strong>' . __('Profit:', 'mydataninja-woocommerce-plugin') . '</strong> ' . $profit_number . '</p>';
    } elseif (!empty($profit_percent)) {
        echo '<p><strong>' . __('Profit:', 'mydataninja-woocommerce-plugin') . '</strong> ' . $profit_percent . '%</p>';
    }
}

add_action('woocommerce_product_meta_end', 'display_profit_field');