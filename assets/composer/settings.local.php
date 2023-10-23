<?php

$databases = [];
$databases['default']['default'] = array (
  'database' => $_ENV['MYSQL_DATABASE'] ?? getenv('MYSQL_DATABASE'),
  'username' => $_ENV['MYSQL_USER'] ?? getenv('MYSQL_USER'),
  'password' => $_ENV['MYSQL_PASSWORD'] ?? getenv('MYSQL_PASSWORD'),
  'prefix' => '',
  'host' => $_ENV['MYSQL_HOST'] ?? getenv('MYSQL_HOST'),
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

if (getenv('OPENID_CONNECT_PARAMS') == true) {
  $openid_config_params = json_decode(getenv('OPENID_CONNECT_PARAMS'), true);
  $config['openid_connect.settings']['always_save_userinfo'] = $openid_config_params['always_save_userinfo'];
  $config['openid_connect.settings']['connect_existing_users'] = $openid_config_params['connect_existing_users'];
  $config['openid_connect.settings']['override_registration_settings'] = $openid_config_params['override_registration_settings'];
  $config['openid_connect.settings']['end_session_enabled'] = $openid_config_params['end_session_enabled'];
  $config['openid_connect.settings']['user_login_display'] = $openid_config_params['user_login_display'];
  foreach($openid_config_params['clients'] as $client => $value) {
    $config['openid_connect.client.' . $client]['status'] = $value['status'];
    $config['openid_connect.client.' . $client]['settings']['tenant'] = $value['settings']['tenant'];
    $config['openid_connect.client.' . $client]['settings']['client_id'] = $value['settings']['client_id'];
    $config['openid_connect.client.' . $client]['settings']['client_secret'] = $value['settings']['client_secret'];
  }
}

$settings['container_yamls'][] = $app_root . '/' . $site_path . '/../development.services.yml';

$config['config_split.config_split.ddev']['status'] = TRUE;
