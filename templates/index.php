<?php

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
<!-- bootstrap link -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
<!-- chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class='mydataninja mx-2'>
    <div class="text-center my-4">
        <img src="https://mydataninja.com/wp-content/uploads/2023/07/logo.png" alt="MyDataNinja Logo" class='logo'>
    </div>
    <div class="d-flex falign-items-center text-center flex-column ">
        <h1 class='title fs-4'>Welcome to MyDataNinja WooCommerce Plugin</h1>
        <div class="p-4 my-2 mx-auto shadow bg-body-tertiary custom-interface">

            <?php if (is_api_key_authorized()): ?>
                <ul class="nav nav-pills mb-3 m-auto gap-2 d-flex justify-content-center align-items-center" id="pills-tab" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active"  id="setting-tab" data-bs-toggle="pill" data-bs-target="#setting" type="button" role="tab" aria-controls="setting" aria-selected="true">Settings</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link"  id="report-tab" data-bs-toggle="pill" data-bs-target="#report" type="button" role="tab" aria-controls="report" aria-selected="false">Reports</button>
                  </li>
                </ul>
            <?php endif; ?>

            <div class="tab-content" id="pills-tabContent">  
              <div id="setting" role="tabpanel" aria-labelledby="setting-tab" tabindex="0" class="tab-content tab-pane fade <?php echo $is_settings_page ? 'active show' : ''; ?>">
                  <form method="post" action="">
                    <?php wp_nonce_field('mydataninja_nonce', 'mydataninja_nonce_field'); ?>
                      <div class='d-flex justify-content-center align-items-center'>
                        <p class="fs-6"  style="max-width: 700px; width: 100%">
                          <?php
                          if ($is_plain_permalinks && is_api_key_authorized()) {
                            echo '<div class="alert alert-warning fs-6">In order to ensure optimal functionality of the plugin, we kindly request you to refrain from using the "plain" option.</div>';
                          } elseif ($is_plain_permalinks) {
                            echo 'To proceed with the authorization, please ensure that your permalinks are not set to "plain". You can adjust this in your settings.';
                          } else {
                            echo 'Click "Authorize" to seamlessly link your store with <a href="https://mydataninja.com/" class="text-decoration-underline fw-bold" style="color: #FF4E00!important;">MyDataNinja.</a> Unlock valuable insights into your order profitability, effortlessly monitor user behavior, enhance their experience, and optimize your orders and ads with precision.';
                          }
                          ?>
                        </p>
                      </div>

                    <?php
                    if (is_api_key_authorized() && !$is_plain_permalinks) {
                      echo '<button class="btn btn-secondary disabled mt-3 mx-2" disabled>Authorized Successfully</button>';
                      echo '<a target="_blank" href="' . esc_url($myDataNinjaConfig['FRONT_BASE_URL']) . '/dashboard" class="btn btn-secondary mt-3 mx-2 link">Open MyDataNinja</a>';               
                      } else {
                      if ($is_plain_permalinks) {
                        echo '<a href="' . esc_url(admin_url('options-permalink.php')) . '" class="btn btn-primary mt-3 mx-2 link">Adjust Permalink Settings</a>';
                      } else {
                        echo '<a href="' . esc_url($myDataNinjaConfig['FRONT_BASE_URL']) . '/crm/woocommerce?name=' . get_bloginfo('name') . '&currency=' . get_woocommerce_currency() . '&base_url=' . home_url() . '" class="btn btn-secondary mt-3 mx-2 link">Authorize</a>';
                      }
                    }
                    ?>

                    <?php if(is_api_key_authorized()): ?>
                      <div class="checkbox-container">
                        <div class="d-flex flex-col justify-content-center align-items-center flex-wrap my-4 gap-3">
                            <div class="d-flex justify-content-between align-items-center gap-4">
                                <div class="text-start">
                                    <label for="_include_profits" class='fw-bolder mb-1 fs-6'>Include MyDataNinja "Cost of Goods" Field</label>
                                    <p class='fs-7 text-secondary'>This setting will add a new field for every product, where you must enter the cost of each product. Our system can then calculate the profit per product and profit per order.</p>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="_include_profits" name="_include_profits" <?php checked(get_option('_include_profits'), 'yes'); ?>>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center gap-4">
                                <div class="text-start">
                                    <label for="_include_tracker" class='fw-bolder mb-1 fs-6'>Include Tracker on Website</label>
                                    <p class='fs-7 text-secondary'>This setting will add the MyDataNinja JS Pixel to your website so that our system can track your ads and visitors, understanding which advertisements and traffic sources are bringing in orders, along with their associated profit and ROI. This setting is a must in order for MyDataNinja to work properly.</p>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="_include_tracker" name="_include_tracker" <?php checked(get_option('_include_tracker'), 'yes'); ?>>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center gap-4 w-100">
                                <div class="text-start">
                                    <label for="_use_existing_cog_field" class='fw-bolder mb-1 fs-6'>Use Existing Cost of Goods Field</label>
                                    <p class='fs-7 text-secondary'>If you already have a "Cost of Goods" field and don't want to add a new one from our system, please choose this setting and indicate which existing field is handling that. This way, MyDataNinja can retrieve information from that field.</p>
                                </div>
                                <div class="checkbox-input"> 
                                  <input class="form-check-input" type="checkbox" id="_use_existing_cog_field" name="_use_existing_cog_field" <?php checked(get_option('_use_existing_cog_field'), 'yes'); ?>>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center gap-4 w-100">
                                <select id="_existing_cog_field_name" name="_existing_cog_field_name" style="min-width: 100%; height: 40px">
                                  <?php
                                  if (!empty($meta_keys)) {
                                    foreach ($meta_keys as $key) {
                                      $pretty_name = pretty_field_name($key);
                                      $selected = selected($key, get_option('_existing_cog_field_name'), false);
                                      echo '<option value="' . esc_attr($key) . '" ' . esc_attr($selected) . '>' . esc_html($pretty_name) . '</option>';                                      }
                                  } else {
                                    echo "<option>Currently, there are no available custom fields as there are no products in the database.</option>";
                                  }
                                  ?>
                                </select>
                            </div>

                            <div class="default">
                                <label for="_default_profit_margin"  class='fw-bolder mb-1 w-100 fs-6 d-flex'>Default Profit Margin (%)</label>
                                <div class="input-group">
                                  <input  type="number" id="_default_profit_margin" name="_default_profit_margin"  min="100%" placeholder="0" value="<?php echo esc_attr(get_option('_default_profit_margin')); ?>">
                                  <span class="input-group-text">%</span>
                                </div>
                                <p class='fs-8 text-secondary'>This will be used if a product doesn't have a cost of goods (COG) defined or if the user opts out of that option.</p>
                            </div>
                        </div>
                        <button class="btn btn-secondary">Save Changes</button>
                      </div>
                    <?php endif; ?>
                  </form>
              </div>
              <div id="report" role="tabpanel" aria-labelledby="report-tab" tabindex="0" class="tab-content tab-pane fade <?php echo $is_reports_page ? 'active show' : ''; ?>">
                  <h2 class='fs-5 fw-bold'>Order Reports</h2>

                  <canvas id="ordersChart"></canvas>

                  <div class='d-grid gap-2 widget-container'>
                      <h2 class='fs-5 fw-bold mt-4'>MyDataNinja Widgets</h2>
                      <div id="sales" class='d-sm-flex'>
                          <div id="totals" class="widget"></div>
                          <div id="totalSales" class="widget"></div>
                      </div>
                      <div id="groupedNetworks" class='d-sm-flex'></div>
                  </div>
              </div>
            </div>
        </div>
    </div>
</div>