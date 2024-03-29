<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function mdnj_create_save_form_info_endpoint() {
  register_rest_route('mydataninja/v1', '/save-crm-info', array(
    'methods' => 'POST',
    'callback' => 'mdnj_save_form_hash_callback',
  ));
}

add_action('rest_api_init', 'mdnj_create_save_form_info_endpoint');

function mdnj_save_form_hash_callback($request) {
  $parameters = $request->get_params();

  $forms = isset($parameters['forms']) ? $parameters['forms'] : [];

  if (!empty($forms)) {
    update_option('mdnj_forms', $forms);
    return rest_ensure_response(array('success' => true));
  } else {
    return new WP_Error('missing_forms', 'Forms are missing', array('status' => 400));
  }
}

add_action('rest_api_init', function () {
  register_rest_route('mydataninja/v1', '/select_forms', array(
    'methods' => 'POST',
    'callback' => 'mdnj_select_forms',
    'permission_callback' => function () {
      return current_user_can('manage_options');
    },
  ));
});

function mdnj_select_forms(WP_REST_Request $request) {
  $forms = $request->get_param('forms');

  if (empty($forms)) {
    delete_option('mdnj_selected_forms');
    return rest_ensure_response(array('success' => true));
  }

  if (count($forms) > 5) {
    return new WP_Error('too_many_forms', 'You can select a maximum of 5 forms', array('status' => 400));
  }

  update_option('mdnj_selected_forms', $forms);
  return rest_ensure_response($forms);
}

function add_custom_script() {
  $selected_forms = get_option('mdnj_selected_forms', []);
  ?>
  <script>
      window.onload = function() {
        <?php foreach ($selected_forms as $form): ?>
          var elements = document.querySelectorAll('<?php echo esc_js($form['class']); ?>');
          elements.forEach(function(element) {
              nj.push(["loadForm", "<?php echo esc_js($form['hash']); ?>", "." + element.className]);
          });
        <?php endforeach; ?>
      };
  </script>
  <?php
}

add_action('wp_footer', 'add_custom_script');