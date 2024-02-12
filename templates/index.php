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
                        Include Profits Field
                    </label>

                    <label for="_include_tracker">
                        <input type="checkbox" id="_include_tracker" name="_include_tracker" <?php checked(get_option('_include_tracker'), 'yes'); ?>>
                        Include Tracker on Website
                    </label>
                </div>

                <input type="submit" class="btn save-btn" style="font-size: 12px;" value="Save Changes">
            </form>
        </div>
    </div>
</div>