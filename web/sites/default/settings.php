<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all environments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to ensure that
 *      the site settings remain consistent.
 */
/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all envrionments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to insure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

// Override pantheon temporary directory
if (isset($_ENV['PANTHEON_ENVIRONMENT'])) {
  $settings["file_temp_path"] = "sites/default/files/private/tmp";
  $settings['simplesamlphp_dir'] = $_ENV['HOME'] .'/code/private/simplesamlphp';
}
        
if (isset($_ENV['PANTHEON_ENVIRONMENT']) && php_sapi_name() != 'cli') {
  $primary_domain = $_SERVER['HTTP_HOST'];
  switch ($_ENV['PANTHEON_ENVIRONMENT']) {
    case 'lando': // Localdev or Lando environments
      $config['environment_indicator.indicator']['name'] = 'Local Dev';
      $config['environment_indicator.indicator']['bg_color'] = '#808080';
      $config['environment_indicator.indicator']['fg_color'] = '#ffffff';
      break;
    case 'stage':
      //$primary_domain = 'online-stage.cu.edu';
      $config['environment_indicator.indicator']['name'] = 'Stage';
      $config['environment_indicator.indicator']['bg_color'] = '#4C742C';
      $config['environment_indicator.indicator']['fg_color'] = '#ffffff';
      break;
    case 'dev':
      //$primary_domain = 'online-dev.cu.edu';
      $config['environment_indicator.indicator']['name'] = 'Dev';
      $config['environment_indicator.indicator']['bg_color'] = '#d25e0f';
      $config['environment_indicator.indicator']['fg_color'] = '#ffffff';
      break;
    case 'test':
      $primary_domain = 'online-test.cu.edu';
      $config['environment_indicator.indicator']['name'] = 'Test';
      $config['environment_indicator.indicator']['bg_color'] = '#c50707';
      $config['environment_indicator.indicator']['fg_color'] = '#ffffff';
      break;
    case 'live':
      //$primary_domain = 'online.cu.edu';
      $config['environment_indicator.indicator']['name'] = 'Live';
      $config['environment_indicator.indicator']['bg_color'] = '#000000';
      $config['environment_indicator.indicator']['fg_color'] = '#ffffff';
      break;
    default:
      // Multidev catchall
      $config['environment_indicator.indicator']['name'] = 'Multidev';
      $config['environment_indicator.indicator']['bg_color'] = '#efd01b';
      $config['environment_indicator.indicator']['fg_color'] = '#000000';
      break;
  }

  $requires_redirect = FALSE;

  // Ensure the site is being served from the primary domain.
  if ($_SERVER['HTTP_HOST'] != $primary_domain) {
    $requires_redirect = TRUE;
  }

  // If you're not using HSTS in the pantheon.yml file, uncomment this next block.
  // if (!isset($_SERVER['HTTP_USER_AGENT_HTTPS'])
  //     || $_SERVER['HTTP_USER_AGENT_HTTPS'] != 'ON') {
  //   $requires_redirect = TRUE;
  // }

  if ($requires_redirect === TRUE) {

    // Name transaction "redirect" in New Relic for improved reporting (optional).
    if (extension_loaded('newrelic')) {
      newrelic_name_transaction("redirect");
    }

    header('HTTP/1.0 301 Moved Permanently');
    header('Location: https://'. $primary_domain . $_SERVER['REQUEST_URI']);
    exit();
  }
  // Drupal 8 Trusted Host Settings
  if (is_array($settings)) {
    $settings['trusted_host_patterns'] = array('^'. preg_quote($primary_domain) .'$');
  }
}

/**
 * Skipping permissions hardening will make scaffolding
 * work better, but will also raise a warning when you
 * install Drupal.
 *
 * https://www.drupal.org/project/drupal/issues/3091285
 */
// $settings['skip_permissions_hardening'] = TRUE;

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}
