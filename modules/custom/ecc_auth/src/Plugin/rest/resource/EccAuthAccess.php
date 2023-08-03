<?php

namespace Drupal\ecc_auth\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Rest Resource for user details.
 *
 * @RestResource(
 *   id = "ecc_auth_access",
 *   label = @Translation("ECC Auth access"),
 *   uri_paths = {
 *     "canonical" = "/api/internal/v1/ecc_auth",
 *     "https://www.drupal.org/link-relations/create" = "/api/internal/v1/ecc_auth"
 *   }
 * )
 */
class EccAuthAccess extends ResourceBase {

  const CACHE_CONTEXTS = [
    'user',
    'supporter',
  ];

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->currentUser = $container->get('current_user');
    return $instance;
  }

  /**
   * Perform GET request.
   *
   * @return \Drupal\rest\ResourceResponseInterface
   *   ResourceResponse.
   */
  public function get() {
    return new ResourceResponse(['true']);
  }

}
