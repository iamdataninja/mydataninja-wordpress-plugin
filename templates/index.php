<?php
    function pretty_field_name($field_name) {
      $pretty_name = str_replace('_', ' ', $field_name);
      $pretty_name = ucwords($pretty_name);
      return $pretty_name;
    }

    global $wpdb;
    $query = "
        SELECT DISTINCT($wpdb->postmeta.meta_key)
        FROM $wpdb->posts
        LEFT JOIN $wpdb->postmeta
        ON $wpdb->posts.ID = $wpdb->postmeta.post_id
        WHERE $wpdb->posts.post_type = 'product'
        AND $wpdb->postmeta.meta_value REGEXP '^-?[0-9]+$' OR $wpdb->postmeta.meta_value REGEXP '^-?[0-9]*\.[0-9]+$'
    ";
    $meta_keys = $wpdb->get_col($query);
?>

<div class="wrap">
    <div class="mydataninja-logo">
        <img src="https://mydataninja.com/wp-content/uploads/2023/07/logo.png" alt="MyDataNinja Logo">
    </div>
    <div class="interface-container">
        <h1>Welcome to MyDataNinja WooCommerce Plugin</h1>
        <div class="custom-interface">
            <form method="post" action="">
                <p>Click 'Authorize' to seamlessly link your store with <a href="https://mydataninja.com/">MyDataNinja.</a> Unlock valuable insights into your order profitability, effortlessly monitor user behavior, enhance their experience, and optimize your orders and ads with precision.</p>
                <button class="btn authorize-btn" <?php echo is_api_key_authorized() ? 'disabled' : 'onclick="authorize()"'; ?>>
                  <?php echo is_api_key_authorized() ? 'Authorized Successfully' : 'Authorize'; ?>
                </button>

                <div class="checkbox-container">
                    <div class="checkboxes">
                        <div class="checkbox-row">
                            <div class="checkbox-label wide-label">
                                <label for="_include_profits">Include MyDataNinja "Cost of Goods" Field</label>
                            </div>
                            <div class="checkbox-input narrow-input">
                                <input type="checkbox" id="_include_profits" name="_include_profits" <?php checked(get_option('_include_profits'), 'yes'); ?>>
                            </div>
                        </div>

                        <div class="checkbox-row">
                            <div class="checkbox-label">
                                <label for="_include_tracker">Include Tracker on Website</label>
                            </div>
                            <div class="checkbox-input">
                                <input type="checkbox" id="_include_tracker" name="_include_tracker" <?php checked(get_option('_include_tracker'), 'yes'); ?>>
                            </div>
                        </div>

                        <div class="checkbox-row">
                            <div class="checkbox-label">
                                <label for="_use_existing_cog_field">Use Existing Cost of Goods Field</label>
                            </div>
                            <div class="checkbox-input">
                                <input type="checkbox" id="_use_existing_cog_field" name="_use_existing_cog_field" <?php checked(get_option('_use_existing_cog_field'), 'yes'); ?>>
                            </div>
                        </div>

                        <div class="checkbox-row">
                            <select id="_existing_cog_field_name" name="_existing_cog_field_name" style="width: 100%">
                              <?php
                              if (!empty($meta_keys)) {
                                foreach ($meta_keys as $key) {
                                  $pretty_name = pretty_field_name($key);
                                  $selected = selected($key, get_option('_existing_cog_field_name'), false);
                                  echo "<option value='$key' $selected>$pretty_name</option>";
                                }
                              } else {
                                echo "<option>Currently, there are no available custom fields as there are no products in the database.</option>";
                              }
                              ?>
                            </select>
                        </div>
                    </div>

                    <input type="submit" class="btn save-btn" style="font-size: 14px; width: 80%" value="Save Changes">
                </div>

            </form>
        </div>
    </div>
</div>