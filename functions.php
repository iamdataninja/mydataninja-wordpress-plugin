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
