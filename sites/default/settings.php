<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

$settings['trusted_host_patterns'][] = '^.+.gsas.columbia.edu$';
$settings['trusted_host_patterns'][] = '^gsas.columbia.edu$';

if (isset($_ENV['PANTHEON_ENVIRONMENT'])) {

  // Require HTTPS.
  if (isset($_SERVER['PANTHEON_ENVIRONMENT']) &&
    ($_SERVER['HTTPS'] === 'OFF') &&
    // Check if Drupal or WordPress is running via command line
    (php_sapi_name() != "cli")) {
    if (!isset($_SERVER['HTTP_X_SSL']) ||
    (isset($_SERVER['HTTP_X_SSL']) && $_SERVER['HTTP_X_SSL'] != 'ON')) {
      header('HTTP/1.0 301 Moved Permanently');
      header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
      exit();
    }
  }
  
  $live_domain_protocol = 'https://';
  $primary_domain_name = 'gsas.columbia.edu';
  $allowed_domain_names = array(
    'gsas.columbia.edu',
    'blog.gsas.columbia.edu',
  );
  $cli = (php_sapi_name() == 'cli');

  if ($_ENV['PANTHEON_ENVIRONMENT'] == 'live') {
      // Redirects
    if (!$cli) {
      // Redirect to reference domain name.
      if (!in_array($_SERVER['HTTP_HOST'], $allowed_domain_names)) {
        header('HTTP/1.0 301 Moved Permanently');
        header('Location: '. $live_domain_protocol. $primary_domain_name. $_SERVER['REQUEST_URI']);
        exit();
      } 
      // Blogs redirect 
      if ($_SERVER['HTTP_HOST'] == 'blog.gsas.columbia.edu') {
        header('HTTP/1.0 301 Moved Permanently');
        header('Location: '. $live_domain_protocol. $primary_domain_name. '/blog');
        exit();
      }
    }
  }

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
}

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}
