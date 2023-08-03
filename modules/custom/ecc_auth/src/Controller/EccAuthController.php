<?php

namespace Drupal\ecc_auth\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\RendererInterface;
use Drupal\openid_connect\OpenIDConnectClaims;
use Drupal\openid_connect\OpenIDConnectSessionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class EccAuthController.
 *
 * @package Drupal\ecc_auth
 */
class EccAuthController extends ControllerBase {

  public const CLIENT = 'nomensa';

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
   * Constructs a BlockContent object.
   *
   * @param \Drupal\geolocation\GeocoderManager $geocoder_manager
   *   Geocoder manager.
   */
  public function __construct(
    protected OpenIDConnectClaims $claims,
    protected OpenIDConnectSessionInterface $session,
    protected RendererInterface $renderer
  ) {
    $this->context = new RenderContext();
  }

  /**
   * ECC Auth in popunder.
   *
   * @return array|\Symfony\Component\HttpFoundation\Response
   *    A render array or a Response object.
   */
  public function popunder() {
    $header = \Drupal::request()->headers->get('essex');
    \Drupal::logger('mine')->debug('header: ' . $header);
//    if ($header === 'true' && $this->currentUser()->isAnonymous()) {
      if ($this->currentUser()->isAnonymous()) {
      // Avoid early rendering errors.
      /** @var \Drupal\Core\Cache\CacheableDependencyInterface $result */
      $response = $this->renderer->executeInRenderContext($this->context, function() {
        return $this->getResponse();
      });
      return $response;
    }
    return new RedirectResponse('/ecc-auth/already-logged-in');
  }

  /**
   * Route to close popunder after user has been logged in.
   *
   * @return array|\Symfony\Component\HttpFoundation\Response
   *    A render array or a Response object.
   */
  public function popunder_loggedin() {
    return [
      '#markup' => 'You have been logged in using your Essex account. This window can be closed.',
      '#attached' => [
        'library' => [
          'ecc_auth/popunder-loggedin',
        ],
      ],
    ];
  }

  /**
   * Route to close popunder if user is already logged in.
   *
   * @return array|\Symfony\Component\HttpFoundation\Response
   *    A render array or a Response object.
   */
  public function popunder_already_loggedin() {
    return [
      '#markup' => 'You are already logged in. This window can be closed.',
      '#attached' => [
        'library' => [
          'ecc_auth/popunder-already-loggedin',
        ],
      ],
    ];
  }

  protected function getResponse() {
    $client = $this->entityTypeManager()
      ->getStorage('openid_connect_client')
      ->loadByProperties([
        'id' => self::CLIENT,
      ])[self::CLIENT];
    $plugin = $client->getPlugin();
    $scopes = $this->claims->getScopes($plugin);
    $this->session->saveDestination();
    $this->session->saveOp('login');
    return $plugin->authorize($scopes);
  }

}
