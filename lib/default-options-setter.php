<?php

function mdnj_set_default_options_on_activation($file) {
  if (get_option('mdnj_include_profits') === false) {
    update_option('mdnj_include_profits', 'yes');
  }

  if (get_option('mdnj_include_tracker') === false) {
    update_option('mdnj_include_tracker', 'yes');
  }

  if (get_option('mdnj_use_existing_cog_field') === false) {
    update_option('mdnj_use_existing_cog_field', 'no');
  }

  if (get_option('mdnj_existing_cog_field_name') === false) {
    update_option('mdnj_existing_cog_field_name', 'mdnj_cost_of_goods');
  }

  register_activation_hook($file, 'mdnj_set_default_options_on_activation');
}