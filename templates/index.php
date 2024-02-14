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

                <div class="checkboxes">
                    <label for="_include_profits">
                        <input type="checkbox" id="_include_profits" name="_include_profits" <?php checked(get_option('_include_profits'), 'yes'); ?>>
                        Include MyDataNinja "Cost of Goods" Field
                    </label>

                    <label for="_include_tracker">
                        <input type="checkbox" id="_include_tracker" name="_include_tracker" <?php checked(get_option('_include_tracker'), 'yes'); ?>>
                        Include Tracker on Website
                    </label>

                    <label for="_use_existing_cog_field">
                        <input type="checkbox" id="_use_existing_cog_field" name="_use_existing_cog_field" <?php checked(get_option('_use_existing_cog_field'), 'yes'); ?>>
                        Use Existing Cost of Goods Field
                    </label>

                    <label for="_existing_cog_field_name" id="_existing_cog_field_name_label" style="display: none;">
                        <input placeholder="Field name" type="text" id="_existing_cog_field_name" name="_existing_cog_field_name" value="<?php echo esc_attr(get_option('_existing_cog_field_name')); ?>">
                    </label>
                </div>

                <input type="submit" class="btn save-btn" style="font-size: 12px;" value="Save Changes">

            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var checkbox = document.getElementById('_use_existing_cog_field');
        var label = document.getElementById('_existing_cog_field_name_label');

        label.style.display = checkbox.checked ? 'block' : 'none';

        checkbox.addEventListener('change', function() {
            label.style.display = checkbox.checked ? 'block' : 'none';
        });
    });
</script>