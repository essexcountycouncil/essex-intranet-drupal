<?php

namespace Drupal\oidc_auto_login\Routing;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Override Auth0 routes.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * Config.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config = $config_factory->get('auth0_user.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    return;
    if ($route = $collection->get('openid_connect.redirect_controller_redirect')) {
      $route->setDefaults([
        '_controller' => '\Drupal\oidc_auto_login\Controller\OidcAutoLoginRedirectController::authenticate',
      ]);
    }
  }

}
