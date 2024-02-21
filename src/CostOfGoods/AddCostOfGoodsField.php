<?php

global $cog_field_name;
$cog_field_name = get_option('_existing_cog_field_name', '_mydataninja_cost_of_goods');

function add_cost_of_goods_field()
{
  global $woocommerce, $post, $cog_field_name;

  $include_profits = get_option('_include_profits', 'yes');
  if ($include_profits === 'yes') {

    $label = __('Cost of Goods', 'mydataninja-woocommerce-plugin') . ' (' . get_woocommerce_currency_symbol() . ')';

    woocommerce_wp_text_input([
      'id' => $cog_field_name,
      'label' => $label,
      'placeholder' => __('Enter cost of goods', 'mydataninja-woocommerce-plugin'),
      'desc_tip' => 'true',
      'description' => __('Enter the cost of goods for calculating profit.', 'mydataninja-woocommerce-plugin'),
      'type' => 'number',
      'custom_attributes' => [
        'step' => 'any',
      ],
    ]);

    ?>
      <script>
          jQuery(document).ready(function($) {
              var costOfGoodsFieldWrapper = $('.' + <?php echo wp_json_encode($cog_field_name); ?> + '_field');
              costOfGoodsFieldWrapper.append('<span class="description"><?php display_profit_field(); ?></span>');
          });
      </script>
    <?php
  }
}

add_action('woocommerce_product_options_pricing', 'add_cost_of_goods_field');

function add_cost_of_goods_field_to_variations($loop, $variation_data, $variation)
{
  global $cog_field_name;

  woocommerce_wp_text_input([
    'id' => $cog_field_name . '[' . $variation->ID . ']',
    'label' => __('Cost of Goods', 'mydataninja-woocommerce-plugin') . ' (' . get_woocommerce_currency_symbol() . ')',
    'desc_tip' => 'true',
    'description' => __('Enter the cost of goods for calculating profit.', 'mydataninja-woocommerce-plugin'),
    'value' => get_post_meta($variation->ID, $cog_field_name, true),
    'wrapper_class' => 'form-row',
  ]);
}

add_action('woocommerce_variation_options_pricing', 'add_cost_of_goods_field_to_variations', 10, 3);

function save_cost_of_goods_field($post_id)
{
  global $cog_field_name;

  if (!isset($_POST['cost_of_goods_nonce_field'])) {
    return;
  }

  if (!wp_verify_nonce($_POST['cost_of_goods_nonce_field'], 'save_cost_of_goods_nonce')) {
    return;
  }

  $cost_of_goods = isset($_POST[$cog_field_name]) ? sanitize_text_field($_POST[$cog_field_name]) : '';

  update_post_meta($post_id, $cog_field_name, $cost_of_goods);
}

add_action('woocommerce_process_product_meta', 'save_cost_of_goods_field');

function save_cost_of_goods_field_for_variations($variation_id, $i): void
{
  global $cog_field_name;

  $cost_of_goods = $_POST[$cog_field_name][$variation_id];
  if (isset($cost_of_goods)) {
    update_post_meta($variation_id, $cog_field_name, sanitize_text_field($cost_of_goods));
  }
}

add_action('woocommerce_save_product_variation', 'save_cost_of_goods_field_for_variations', 10, 2);

function display_profit_field()
{
  global $post, $cog_field_name;

  $cost_of_goods = get_post_meta($post->ID, $cog_field_name, true);
  $product = wc_get_product($post);

  if (!empty($cost_of_goods) && $product) {
    $profit_fixed = $product->get_price() - $cost_of_goods;
    $profit_percentage = ($profit_fixed / $cost_of_goods) * 100;

    echo 'Profit is ' . wc_price($profit_fixed) . ' (' . number_format($profit_percentage, 2) . '%)';
  }
}

add_action('woocommerce_product_after_variable_attributes', 'display_profit_field');