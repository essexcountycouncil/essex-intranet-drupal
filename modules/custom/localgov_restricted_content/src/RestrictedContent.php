<?php

namespace Drupal\localgov_restricted_content;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * The localgov_restricted_content.restricted_content service.
 */
class RestrictedContent implements RestrictedContentInterface {

  public const PARENT_FIELDS = [
    'localgov_newsroom',
    'localgov_guides_parent',
    'localgov_services_parent',
    'localgov_step_parent',
    'localgov_subsites_parent',
  ];

  public const RESTRICTED_CONTENT_FIELD = 'localgov_restricted_content';

  /**
   * {@inheritdoc}
   */
  public function isAncestorRestricted(ContentEntityInterface $entity): bool {
    foreach ($this::PARENT_FIELDS as $parent_field_option) {
      if ($entity->hasField($parent_field_option)) {
        $parent_field = $parent_field_option;
        break;
      }
    }
    if (isset($parent_field)) {
      if ($parent = $entity->{$parent_field}->entity) {
        if ($parent->hasField($this::RESTRICTED_CONTENT_FIELD)) {
          if ($parent->{$this::RESTRICTED_CONTENT_FIELD}->value) {
            return TRUE;
          }
          if ($entity->id() === $parent->id()) {
            // There's no guarantee that the entity isn't its own grandparent,
            // but abort in the simple case that it's its own parent.
            // TODO: A more complex check is needed for the non-trivial case.
            return TRUE;
          }
          return $this->isAncestorRestricted($parent);
        }
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isRestricted(ContentEntityInterface $entity): bool {
    if ($entity->hasField($this::RESTRICTED_CONTENT_FIELD)) {
      if ($entity->{$this::RESTRICTED_CONTENT_FIELD}->value) {
        return TRUE;
      }
      return $this->isAncestorRestricted($entity);
    }
    return FALSE;
  }

}
