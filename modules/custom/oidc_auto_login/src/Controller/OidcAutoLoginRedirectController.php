<?php

namespace Drupal\oidc_auto_login\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\externalauth\AuthmapInterface;
use Drupal\openid_connect\Controller\OpenIDConnectRedirectController;
use Drupal\openid_connect\OpenIDConnectClaims;
use Drupal\openid_connect\OpenIDConnect;
use Drupal\openid_connect\OpenIDConnectClientEntityInterface;
use Drupal\openid_connect\OpenIDConnectSessionInterface;
use Drupal\openid_connect\OpenIDConnectStateTokenInterface;
use Drupal\openid_connect\Plugin\OpenIDConnectClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Redirect controller.
 *
 * @package Drupal\openid_connect\Controller
 */
class OidcAutoLoginRedirectController extends OpenIDConnectRedirectController {

  /**
   * Redirect.
   *
   * @param \Drupal\openid_connect\OpenIDConnectClientEntityInterface $openid_connect_client
   *   The client.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   The redirect response starting the authentication request.
   *
   * @throws \Exception
   */
  public function authenticate(OpenIDConnectClientEntityInterface $openid_connect_client): RedirectResponse {
    $response = parent::authenticate($openid_connect_client);
    $messages = $this->messenger()->messagesByType('warning');
    if ($messages) {
 //     $this->messenger()->deleteByType('warning');
      return new RedirectResponse('/');
    }
//    $this->messenger()->addStatus('hi');//  ->addWarning('');
    return $response;
  }

}
