<?php

if ( ! defined( 'ABSPATH' ) ) exit;

global $myDataNinjaConfig;
$permalink_structure = get_option('permalink_structure');
$is_plain_permalinks = empty($permalink_structure);

function pretty_field_name($field_name) {
  $pretty_name = str_replace('_', ' ', $field_name);
  $pretty_name = ucwords($pretty_name);
  return $pretty_name;
}

global $wpdb;
$meta_keys = $wpdb->get_col("
    SELECT DISTINCT($wpdb->postmeta.meta_key)
    FROM $wpdb->posts
    LEFT JOIN $wpdb->postmeta
    ON $wpdb->posts.ID = $wpdb->postmeta.post_id
    WHERE $wpdb->posts.post_type = 'product'
    AND $wpdb->postmeta.meta_value REGEXP '^-?[0-9]+$' OR $wpdb->postmeta.meta_value REGEXP '^-?[0-9]*\.[0-9]+$'
");
?>

<div class="mydataninja wrap">
    <div class="mydataninja-logo">
        <img src="<?php echo esc_url(plugins_url('/assets/icons/mydataninja.png', __DIR__)); ?>" alt="MyDataNinja Logo">
    </div>
    <div class="interface-container">
        <h1>Welcome to MyDataNinja WooCommerce Plugin</h1>
        <div class="custom-interface">
            <?php if (mdnj_is_api_key_authorized()): ?>
                <ul class="tabs">
                    <li class="tab-link <?php echo $is_settings_page ? 'current' : ''; ?>" data-tab="tab-1">Settings</li>
                      <?php if(is_plugin_active('woocommerce/woocommerce.php')): ?>
                          <li class="tab-link <?php echo $is_reports_page ? 'current' : ''; ?>" data-tab="tab-2">Reports</li>
                      <?php endif; ?>
                    <?php if(get_option('mdnj_form_hash')): ?>
                        <li class="tab-link <?php echo $is_form_page ? 'current' : ''; ?>" data-tab="tab-3">Forms</li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>


            <div id="tab-1" class="tab-content <?php echo $is_settings_page ? 'current' : ''; ?>">
                <form method="post" action="">
                  <?php wp_nonce_field('mydataninja_nonce', 'mydataninja_nonce_field'); ?>
                    <p>
                      <?php
                      if ($is_plain_permalinks && mdnj_is_api_key_authorized()) {
                        echo '<div class="warning-message">In order to ensure optimal functionality of the plugin, we kindly request you to refrain from using the "plain" option.</div>';
                      } elseif ($is_plain_permalinks) {
                        echo 'To proceed with the authorization, please ensure that your permalinks are not set to "plain". You can adjust this in your settings.';
                      } else {
                        echo 'Click "Authorize" to seamlessly link your store with <a href="https://mydataninja.com/" style="color: #FF4E00!important;">MyDataNinja.</a> Unlock valuable insights into your order profitability, effortlessly monitor user behavior, enhance their experience, and optimize your orders and ads with precision.';
                      }
                      ?>
                    </p>

                  <?php
                  if (mdnj_is_api_key_authorized() && !$is_plain_permalinks) {
                    echo '<button class="btn authorize-btn" disabled>Authorized Successfully</button>';
                    echo '<a href="' . esc_url($myDataNinjaConfig['FRONT_BASE_URL']) . '/dashboard" class="btn authorize-btn">Open MyDataNinja</a>';                  } else {
                    if ($is_plain_permalinks) {
                      echo '<a href="' . esc_url(admin_url('options-permalink.php')) . '" class="btn authorize-btn save-btn no-underline">Adjust Permalink Settings</a>';
                    } else {
                      echo '<a href="' . esc_url($myDataNinjaConfig['FRONT_BASE_URL']) . '/crm/woocommerce?name=' . esc_html(get_bloginfo('name')) . '&currency=' . esc_html(get_woocommerce_currency()) . '&base_url=' . esc_url(home_url()) . '" class="btn authorize-btn">Authorize</a>';
                    }
                  }
                  ?>

                  <?php if(mdnj_is_api_key_authorized()): ?>
                      <div class="checkbox-container">
                          <div class="checkboxes">
                              <div class="checkbox-row">
                                  <div class="checkbox-label">
                                      <label for="_include_tracker">Include Tracker on Website</label>
                                      <p>This setting will add the MyDataNinja JS Pixel to your website so that our system can track your ads and visitors, understanding which advertisements and traffic sources are bringing in orders, along with their associated profit and ROI. This setting is a must in order for MyDataNinja to work properly.</p>
                                  </div>
                                  <div class="checkbox-input">
                                      <input type="checkbox" id="_include_tracker" name="_include_tracker" <?php checked(get_option('mdnj_include_tracker'), 'yes'); ?>>
                                  </div>
                              </div>
                              <?php if(is_plugin_active('woocommerce/woocommerce.php')): ?>
                                  <div class="checkbox-row">
                                      <div class="checkbox-label wide-label">
                                          <label for="_include_profits">Include MyDataNinja "Cost of Goods" Field</label>
                                          <p>This setting will add a new field for every product, where you must enter the cost of each product. Our system can then calculate the profit per product and profit per order.</p>
                                      </div>
                                      <div class="checkbox-input narrow-input">
                                          <input type="checkbox" id="_include_profits" name="_include_profits" <?php checked(get_option('mdnj_include_profits'), 'yes'); ?>>
                                      </div>
                                  </div>

                                  <div class="checkbox-row">
                                      <div class="checkbox-label">
                                          <label for="_use_existing_cog_field">Use Existing Cost of Goods Field</label>
                                          <p>If you already have a "Cost of Goods" field and don't want to add a new one from our system, please choose this setting and indicate which existing field is handling that. This way, MyDataNinja can retrieve information from that field.</p>
                                      </div>
                                      <div class="checkbox-input">
                                          <input type="checkbox" id="_use_existing_cog_field" name="_use_existing_cog_field" <?php checked(get_option('mdnj_use_existing_cog_field'), 'yes'); ?>>
                                      </div>
                                  </div>

                                  <div class="checkbox-row">
                                      <select id="_existing_cog_field_name" name="_existing_cog_field_name" style="min-width: 100%">
                                        <?php
                                        if (!empty($meta_keys)) {
                                          foreach ($meta_keys as $key) {
                                            $pretty_name = pretty_field_name($key);
                                            $selected = selected($key, get_option('mdnj_existing_cog_field_name'), false);
                                            echo '<option value="' . esc_attr($key) . '" ' . esc_attr($selected) . '>' . esc_html($pretty_name) . '</option>';                                      }
                                        } else {
                                          echo "<option>Currently, there are no available custom fields as there are no products in the database.</option>";
                                        }
                                        ?>
                                      </select>
                                  </div>

                                  <div class="checkbox-row" style="flex-direction: column; text-align: left">
                                    <label for="_default_profit_margin" style="width: 100%;">Default Profit Margin (%)</label>
                                    <input type="number" id="_default_profit_margin" name="_default_profit_margin" value="<?php echo esc_attr(get_option('mdnj_default_profit_margin')); ?>" min="0" max="100" placeholder="0%" style="width: 100%">
                                    <p>This will be used if a product doesn't have a cost of goods (COG) defined or if the user opts out of that option.</p>
                                  </div>
                              <?php endif; ?>
                          </div>

                          <input type="submit" class="btn save-btn" style="font-size: 14px; width: 80%" value="Save Changes">
                      </div>
                  <?php endif; ?>

                </form>
            </div>

            <div id="tab-2" class="tab-content <?php echo $is_reports_page ? 'current' : ''; ?>">
                <h2>Order Reports</h2>

                <canvas id="ordersChart"></canvas>

                <div class="widget-container">
                    <h2>MyDataNinja Widgets</h2>
                    <div id="sales">
                        <div id="totals" class="widget"></div>
                        <div id="totalSales" class="widget"></div>
                    </div>
                    <div id="groupedNetworks"></div>
                </div>
            </div>

            <div id="tab-3" class="tab-content ninja-forms <?php echo $is_form_page ? 'current' : ''; ?>">
                <div class="form-container">
                  <?php
                  $forms = get_option('mdnj_forms', []);
                  foreach ($forms as $form) {
                    echo '<div class="form-header">';
                        echo '<h3 class="form-name">' . esc_html($form['name']) . '</h3>';
                        echo '<div class="form-actions">';
                            echo '<a href="' . esc_url($myDataNinjaConfig["FRONT_BASE_URL"] . "/crm/dataninja/" . $form["id"] . "/integration") . '" target="_blank" class="form-action-icon"><img width="20" src="' . esc_url(plugins_url('/assets/icons/edit.svg', __DIR__)) . '" alt="Edit"></a>';
                            echo '<a href="' . esc_url($myDataNinjaConfig["API_BASE_URL"] . "/ext/form/load/" . $form["hash"]) . '" target="_blank" class="form-action-icon"><img width="20" src="' . esc_url(plugins_url('/assets/icons/external-link.svg', __DIR__)) . '" alt="Open"></a>';
                            echo '<a href="#" class="form-action-icon arrow-icon"><img width="20" src="' . esc_url(plugins_url('/assets/icons/arrow.svg', __DIR__)) . '" alt="Open Accordion"></a>';
                        echo '</div>';
                    echo '</div>';
                    echo '<div id="mdnj-forms-accordion">';
                        echo '<div class="form-fields">';
                        echo '<input type="text" id="iframe-input-' . esc_attr($form["hash"]) . '" value=\'<iframe src="' . esc_url($myDataNinjaConfig["API_BASE_URL"] . "/ext/form/load/" . $form["hash"]) . '" frameborder="0" width="100%"></iframe>\' readonly >';
                        echo '<a href="#" data-input-id="iframe-input-' . esc_attr($form["hash"]) . '" class="form-action-icon copy-icon"><img width="20" src="' . esc_url(plugins_url('/assets/icons/copy.svg', __DIR__)) . '" alt="Copy"></a>';
                        echo '</div>';
                        echo '<div class="form-fields">';
                        echo '<input type="text" id="shortcode-input-' . esc_attr($form["hash"]) . '" value=\'[mydataninja_iframe hash="' . esc_attr($form["hash"]) . '"]\' readonly >';
                        echo '<a href="#" data-input-id="shortcode-input-' . esc_attr($form["hash"]) . '" class="form-action-icon copy-icon"><img width="20" src="' . esc_url(plugins_url('/assets/icons/copy.svg', __DIR__)) . '" alt="Copy"></a>';
                        echo '</div>';
                        echo '<span>For a native form integration, use this JavaScript code. Ensure the "Include Tracker" option is enabled.</span>';
                        echo '<div class="form-fields">';
                        echo '<input type="text" id="loadform-input-' . esc_attr($form["hash"]) . '" value=\'nj.push(["loadForm", "' . esc_attr($form["hash"]) . '", "body"])\'' . ' readonly >';
                        echo '<a href="#" data-input-id="loadform-input-' . esc_attr($form["hash"]) . '" class="form-action-icon copy-icon"><img width="20" src="' . esc_url(plugins_url('/assets/icons/copy.svg', __DIR__)) . '" alt="Copy"></a>';
                        echo '</div>';
                    echo '</div>';
                  }
                  ?>
                </div>
            </div>
        </div>
    </div>
</div>