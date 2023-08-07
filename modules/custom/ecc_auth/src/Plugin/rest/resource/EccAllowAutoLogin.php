<?php

namespace Drupal\ecc_auth\Plugin\rest\resource;

use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Rest Resource for checking whether auto-login is allowed.
 *
 * @RestResource(
 *   id = "ecc_allow_auto_login",
 *   label = @Translation("ECC Allow Auth-login"),
 *   uri_paths = {
 *     "canonical" = "/api/internal/v1/allow_auto_login",
 *     "https://www.drupal.org/link-relations/create" = "/api/internal/v1/allow_auto_login"
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
    $config = $this->configFactory->get('ecc_auth.settings');
    $enabled = $config->get('enabled') ?: FALSE;
    $auth = $enabled && $this->currentUser->isAnonymous();
    return new ModifiedResourceResponse(['allow_auto_login' => $auth]);
  }

}
