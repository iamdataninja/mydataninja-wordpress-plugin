<?php

if ( ! defined( 'ABSPATH' ) ) exit;

global $cog_field_name;
$cog_field_name = get_option('mdnj_existing_cog_field_name', 'mdnj_cost_of_goods');

function mdnj_add_cost_of_goods_field()
{
  global $woocommerce, $post, $cog_field_name;

  $include_profits = get_option('mdnj_include_profits', 'yes');
  if ($include_profits === 'yes') {

    $label = __('Cost of Goods', 'mydataninja-ad-performance-tracking-order-reports-crm-analytics-and-optimization-tools') . ' (' . get_woocommerce_currency_symbol() . ')';

    woocommerce_wp_text_input([
      'id' => $cog_field_name,
      'label' => $label,
      'placeholder' => __('Enter cost of goods', 'mydataninja-ad-performance-tracking-order-reports-crm-analytics-and-optimization-tools'),
      'desc_tip' => 'true',
      'description' => __('Enter the cost of goods for calculating profit.', 'mydataninja-ad-performance-tracking-order-reports-crm-analytics-and-optimization-tools'),
      'type' => 'number',
      'custom_attributes' => [
        'step' => 'any',
      ],
    ]);

    wp_nonce_field('save_cost_of_goods_nonce', 'cost_of_goods_nonce_field');

    ?>
      <script>
          jQuery(document).ready(function($) {
              var costOfGoodsFieldWrapper = $('.' + <?php echo wp_json_encode($cog_field_name); ?> + '_field');
              costOfGoodsFieldWrapper.append('<span class="description"><?php mdnj_display_profit_field(); ?></span>');
          });
      </script>
    <?php
  }
}

add_action('woocommerce_product_options_pricing', 'mdnj_add_cost_of_goods_field');

function mdnj_add_cost_of_goods_field_to_variations($loop, $variation_data, $variation)
{
  global $cog_field_name;

  woocommerce_wp_text_input([
    'id' => $cog_field_name . '[' . $variation->ID . ']',
    'label' => __('Cost of Goods', 'mydataninja-ad-performance-tracking-order-reports-crm-analytics-and-optimization-tools') . ' (' . get_woocommerce_currency_symbol() . ')',
    'desc_tip' => 'true',
    'description' => __('Enter the cost of goods for calculating profit.', 'mydataninja-ad-performance-tracking-order-reports-crm-analytics-and-optimization-tools'),
    'value' => get_post_meta($variation->ID, $cog_field_name, true),
    'wrapper_class' => 'form-row',
  ]);

  wp_nonce_field('save_cost_of_goods_nonce', 'cost_of_goods_nonce_field');
}

add_action('woocommerce_variation_options_pricing', 'mdnj_add_cost_of_goods_field_to_variations', 10, 3);

function mdnj_save_cost_of_goods_field($post_id)
{
  global $cog_field_name;

  if (!isset($_POST['cost_of_goods_nonce_field'])) {
    return;
  }

  $nonce_field = isset($_POST['cost_of_goods_nonce_field']) ? sanitize_text_field(wp_unslash($_POST['cost_of_goods_nonce_field'])) : '';

  if (!wp_verify_nonce($nonce_field, 'save_cost_of_goods_nonce')) {
    return;
  }

  $cost_of_goods = isset($_POST[$cog_field_name]) ? sanitize_text_field($_POST[$cog_field_name]) : '';

  update_post_meta($post_id, $cog_field_name, $cost_of_goods);
}

add_action('woocommerce_process_product_meta', 'mdnj_save_cost_of_goods_field');

function mdnj_save_cost_of_goods_field_for_variations($variation_id, $i): void
{
  global $cog_field_name;

  if (!isset($_POST['cost_of_goods_nonce_field'])) {
    return;
  }

  $nonce_field = isset($_POST['cost_of_goods_nonce_field']) ? sanitize_text_field(wp_unslash($_POST['cost_of_goods_nonce_field'])) : '';

  if (!wp_verify_nonce($nonce_field, 'save_cost_of_goods_nonce')) {
    return;
  }

  $cost_of_goods = isset($_POST[$cog_field_name][$variation_id]) ? sanitize_text_field(wp_unslash($_POST[$cog_field_name][$variation_id])) : '';
  if (!empty($cost_of_goods)) {
    update_post_meta($variation_id, $cog_field_name, $cost_of_goods);
  }
}

add_action('woocommerce_save_product_variation', 'mdnj_save_cost_of_goods_field_for_variations', 10, 2);

function mdnj_display_profit_field()
{
  global $post, $cog_field_name;

  $cost_of_goods = get_post_meta($post->ID, $cog_field_name, true);
  $product = wc_get_product($post);

  if (!empty($cost_of_goods) && $product) {
    $profit_fixed = $product->get_price() - $cost_of_goods;
    $profit_percentage = ($profit_fixed / $cost_of_goods) * 100;

    $allowed_html = array(
      'span' => array(
        'class' => array(),
      ),
    );

    echo wp_kses('Profit is ' . wc_price($profit_fixed) . ' (' . number_format($profit_percentage, 2) . '%)', $allowed_html);
  }
}

add_action('woocommerce_product_after_variable_attributes', 'mdnj_display_profit_field');