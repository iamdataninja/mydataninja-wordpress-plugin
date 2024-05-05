<?php

$mydnj_config = include __DIR__ . '/config.php';

/**
 * Get value of a variable
 *
 * @param mixed|callback $value
 * @param mixed ...$args
 * @return mixed
 */
function mydataninja_value($value, ...$args)
{
  if (is_callable($value)) {
    return call_user_func($value, ...$args);
  }

  return $value;
}

/**
 * Get mydataninja config value
 *
 * @param string $key
 * @param mixed $default
 * @return string|int|mixed
 */
function mydataninja_config($key, $default = null)
{
  global $mydnj_config;

  if (!isset($mydnj_config)) {
    return mydataninja_value($default);
  }

  if (!isset($mydnj_config[$key])) {
    return mydataninja_value($default);
  }

  return mydataninja_value($mydnj_config[$key]);
}

/**
 * Ensure that function exists and call it then
 *
 * @param string|callback $function
 * @param mixed ...$args
 * @return mixed
 */
function mydataninja_ensure_function($function, $default = null, ...$args)
{
  if (function_exists($function)) {
    return call_user_func($function, ...$args);
  }

  return mydataninja_value($default);
}


/**
 * Return mydataninja authorization url when clicked on authorize button
 *
 * @return string
 */
function mydataninja_get_authorization_url()
{
  return mydataninja_config('FRONT_BASE_URL') . '/' .
    'apps?' . http_build_query([
      'app' => 'woocommerce',
      'connect' => 'woocommerce',
      'name' => get_bloginfo('name'),
      'currency' => mydataninja_ensure_function('get_woocommerce_currency'),
      'base_url' => home_url(),
    ]);
}
