<?php

namespace Drupal\localgov_restricted_content\EventSubscriber;

use Drupal\Core\Session\AccountInterface;
use Drupal\localgov_core\Event\PageHeaderDisplayEvent;
use Drupal\localgov_restricted_content\RestrictedContentInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Page header event to hide 'lede' on restricted content.
 *
 * @package Drupal\localgov_restricted_content\EventSubscriber
 */
class PageHeaderSubscriber implements EventSubscriberInterface {

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Account.
   * @param \Drupal\localgov_restricted_content\RestrictedContentInterface $restrictedContent
   *   The restricted content service.
   */
  public function __construct(
    protected AccountInterface $account,
    protected RestrictedContentInterface $restrictedContent) {
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      PageHeaderDisplayEvent::EVENT_NAME => ['setPageHeader', 0],
    ];
  }

  /**
   * Set page title and lede.
   */
  public function setPageHeader(PageHeaderDisplayEvent $event) {

    $node = $event->getEntity();

    if (!$node instanceof NodeInterface) {
      return;
    }

    if (
      $this->restrictedContent->isRestricted($node) and
      $this->account->isAnonymous()
    ) {
      $event->setLede('');
    }
  }

}
