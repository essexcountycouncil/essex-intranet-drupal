<?php

namespace Drupal\localgov_restricted_content;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Session\AccountProxyInterface;

class RestrictedContent implements RestrictedContentInterface {

  public const PARENT_FIELDS = [
    'field_newsroom',
    'localgov_guides_parent',
    'localgov_services_parent',
    'localgov_step_parent',
    'localgov_subsites_parent',
  ];

  public const RESTRICTED_CONTENT_FIELD = 'field_restricted_content';

  /**
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   Current user.
   */
  public function __construct(protected AccountProxyInterface $currentUser) {
  }

  /**
   * {@inheritdoc}
   */
  public function isRestricted(ContentEntityInterface $entity): bool {
    if ($this->currentUser->isAuthenticated()) {
      return FALSE;
    }

    if ($entity->hasField($this::RESTRICTED_CONTENT_FIELD)) {
      if ($entity->{$this::RESTRICTED_CONTENT_FIELD}->value) {
        return TRUE;
      }
      foreach ($this::PARENT_FIELDS as $parent_field_option) {
        if ($entity->hasField($parent_field_option)) {
          $parent_field = $parent_field_option;
          break;
        }
      }
      if (isset($parent_field)) {
        if ($parent = $entity->$parent_field->entity) {
          return $this->isRestricted($parent);
        }
      }
    }
    return FALSE;
  }

}