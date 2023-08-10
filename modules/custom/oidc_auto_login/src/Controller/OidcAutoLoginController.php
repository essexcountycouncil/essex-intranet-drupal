<?php

namespace Drupal\oidc_auto_login\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\RendererInterface;
use Drupal\openid_connect\OpenIDConnectClaims;
use Drupal\openid_connect\OpenIDConnectSessionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * OpenID Connect Auto-login Controller.
 *
 * @package Drupal\oidc_auto_login
 */
class OidcAutoLoginController extends ControllerBase {

  /**
   * Render context.
   *
   * @var \Drupal\Core\Render\RenderContext
   */
  protected $context;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('openid_connect.claims'),
      $container->get('openid_connect.session'),
      $container->get('renderer')
    );
  }

  /**
   * Constructor.
   *
   * @param \Drupal\openid_connect\OpenIDConnectClaims $claims
   *   The OpenID Connect claims service.
   * @param \Drupal\openid_connect\OpenIDConnectSessionInterface $session
   *   Session service of the OpenID Connect module.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   Renderer service.
   */
  public function __construct(
    protected OpenIDConnectClaims $claims,
    protected OpenIDConnectSessionInterface $session,
    protected RendererInterface $renderer
  ) {
    $this->context = new RenderContext();
  }

  /**
   * Auto login.
   *
   * @return array|\Symfony\Component\HttpFoundation\Response
   *   A render array or a Response object.
   */
  public function login() {
    if ($this->currentUser()->isAnonymous()) {
      // Avoid early rendering errors.
      /** @var \Drupal\Core\Cache\CacheableDependencyInterface $result */
      $response = $this->renderer->executeInRenderContext($this->context, function () {
        return $this->getAutoLoginResponse();
      });
      return $response;
    }
    return new RedirectResponse('/user/auto-login/already-logged-in');
  }

  /**
   * Route to close auto-login window after user has been logged in.
   *
   * @return array|\Symfony\Component\HttpFoundation\Response
   *   A render array or a Response object.
   */
  public function loggedIn() {
    return [
      '#markup' => 'You have been logged in using your OpenID Connect account. This window can be closed.',
      '#attached' => [
        'library' => [
          'oidc_auto_login/logged_in',
        ],
      ],
    ];
  }

  /**
   * Route to close auto-login window if user is already logged in.
   *
   * @return array|\Symfony\Component\HttpFoundation\Response
   *   A render array or a Response object.
   */
  public function alreadyLoggedIn() {
    return [
      '#markup' => 'You are already logged in. This window can be closed.',
      '#attached' => [
        'library' => [
          'oidc_auto_login/already_logged_in',
        ],
      ],
    ];
  }

  /**
   * Authorise and get response.
   *
   * @return \Symfony\Component\HttpFoundation\Response|array
   *   A response object.
   */
  protected function getAutoLoginResponse() {
    try {
      $client_id = $this->config('oidc_auto_login.settings')
        ->get('client');
      $clients = $this->entityTypeManager()
        ->getStorage('openid_connect_client')
        ->loadByProperties([
          'id' => $client_id,
        ]);
      if (!empty($clients)) {
        if ($client = $clients[$client_id]) {
          $plugin = $client->getPlugin();
          $scopes = $this->claims->getScopes($plugin);
          $this->session->saveDestination();
          $this->session->saveOp('login');
          return $plugin->authorize($scopes);
        }
      }
    }
    catch (\Exception $e) {
    }
    // Close the auto-login window.
    new RedirectResponse('/user/auto-login/already-logged-in');
  }

}
