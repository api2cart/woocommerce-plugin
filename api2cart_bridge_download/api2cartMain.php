<?php
/**
 * @package API2Cart
 * @version 1.0.0
 */
/*
Plugin Name: API2Cart Bridge Download
Plugin URI: https://www.api2cart.com/
Description: API2Cart Integration Plugin
Author: MagneticOne
Version: 1.0.0
Author URI: http://www.magneticone.com/
*/
defined('ABSPATH') or die("Cannot access pages directly.");

include 'worker.php';
$worker = new API2CartWorker();

if ($worker->isBridgeExist()) {
  include $worker->a2cBridgePath . $worker->configFilePath;
  $storeKey = M1_TOKEN;
}

if (isset($_REQUEST['a2caction'])) {
  $action = $_REQUEST['a2caction'];
  $storeKey = md5('api2cart_' . time());
  switch ($action) {
    case 'installBridge':
      $worker->installBridge();
      $worker->updateToken($storeKey);
      break;
    case 'removeBridge':
      $worker->unInstallBridge();
      break;
    case 'updateToken':
      $worker->updateToken($storeKey);
  }
  die($storeKey);
}

function api2cart_plugin_action_links($links, $file)
{
  if ($file == plugin_basename(dirname(__FILE__) . '/api2cartMain.php')) {
    $links[] = '<a href="' . admin_url('admin.php?page=api2cart-config') . '">' . __('Settings') . '</a>';
  }

  return $links;
}

add_filter('plugin_action_links', 'api2cart_plugin_action_links', 10, 2);

function api2cart_config()
{
  global $worker;
  global $storeKey;
  wp_enqueue_style('api2cart-css', plugins_url('css/style.css', __FILE__));
  wp_enqueue_script('api2cart-js', plugins_url('js/api2cart.js', __FILE__), array('jquery'));

  $showButton = 'install';
  if ($worker->isBridgeExist()) {
    $showButton = 'uninstall';
  }

  $cartName = 'WooCommerce';
  $sourceCartName = 'WooCommerce';
  $sourceCartName = strtolower(str_replace(' ', '-', trim($sourceCartName)));
  $referertext = 'API2Cart: ' . $sourceCartName . ' to ' . $cartName . ' module';

  include 'settings.phtml';
  return true;
}

function api2cart_load_menu()
{
  add_submenu_page('plugins.php', __('API2Cart'), __('API2Cart'), 'manage_options', 'api2cart-config', 'api2cart_config');
}

add_action('admin_menu', 'api2cart_load_menu');
