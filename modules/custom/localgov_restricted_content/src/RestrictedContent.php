<?php

namespace Drupal\localgov_restricted_content;

use Drupal\Core\Config\ConfigFactoryInterface;
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
   * Constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config factory.
   */
  public function __construct(protected ConfigFactoryInterface $configFactory) {
  }

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
      /** @var \Drupal\Core\Entity\ContentEntityInterface $parent */
      if ($parent = $entity->{$parent_field}->entity) {
        if ($this->isEntityRestrictable($parent)) {
          if ($parent->{$this::RESTRICTED_CONTENT_FIELD}->value) {
            return TRUE;
          }
          if ($entity->id() === $parent->id()) {
            // There's no guarantee that the entity isn't its own grandparent,
            // but abort in the simple case that it's its own parent.
            // @todo A more complex check is needed for the non-trivial case.
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
    if ($this->isEntityRestrictable($entity)) {
      if ($entity->{$this::RESTRICTED_CONTENT_FIELD}->value) {
        return TRUE;
      }
      return $this->isAncestorRestricted($entity);
    }
    return FALSE;
  }

  /**
   * Check if entity is restrictable.
   *
   * 1. Entity is a node.
   * 2. Content type is in the list of restricted content types.
   * 3. Entity has the restricted content field.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   Entity.
   *
   * @return bool
   *   TRUE if entity is restrictable.
   */
  public function isEntityRestrictable(ContentEntityInterface $entity): bool {
    if ($entity->getEntityTypeId() !== 'node') {
      return FALSE;
    }

    $restricted_content_types = &drupal_static(__FUNCTION__);
    if (!isset($restricted_content_types)) {
      $config = $this->configFactory->get('localgov_restricted_content.settings');
      $restricted_content_types = $config->get('restricted_content_types') ?? [];
    }

    if (!in_array($entity->bundle(), $restricted_content_types)) {
      return FALSE;
    }

    return $entity->hasField($this::RESTRICTED_CONTENT_FIELD);
  }

}
