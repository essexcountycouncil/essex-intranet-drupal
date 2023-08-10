<?php

namespace Drupal\oidc_auto_login\Plugin\rest\resource;

use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Rest Resource for checking whether auto-login is allowed.
 *
 * @RestResource(
 *   id = "ecc_allow_auto_login",
 *   label = @Translation("Remove!"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/ecc_allow_auto_login",
 *     "https://www.drupal.org/link-relations/create" = "/api/v1/ecc_allow_auto_login"
 *   }
 * )
 */
class EccAllowAutoLogin extends ResourceBase {

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->currentUser = $container->get('current_user');
    $instance->configFactory = $container->get('config.factory');
    $instance->requestStack = $container->get('request_stack');
    return $instance;
  }

  /**
   * Perform GET request.
   *
   * @return \Drupal\rest\ResourceResponseInterface
   *   ResourceResponse.
   */
  public function get() {
    $auth = FALSE;
    $config = $this->configFactory->get('oidc_auto_login.settings');
    if ($config->get('enabled') && $this->currentUser->isAnonymous()) {
      if ($config->get('header_required')) {
        $header_key = $config->get('header_key');
        $expected_header_value = $config->get('header_value');
        if ($header_key && $expected_header_value) {
          $actual_header_value = $this->requestStack->getCurrentRequest()->headers->get($header_key);
        }
        $auth = !empty($actual_header_value) && $actual_header_value === $expected_header_value;
      }
      else {
        $auth = TRUE;
      }
    }
    return new ModifiedResourceResponse(['allow_auto_login' => $auth]);
  }

}
