<?php

namespace Drupal\localgov_restricted_content;

use Drupal\Core\Entity\ContentEntityInterface;

interface RestrictedContentInterface {

  /**
   * Check if the entity is restricted.
   *
   * Is user is authenticated then return FALSE;
   * If localgov_restricted_content is TRUE then return TRUE.
   * if localgov_restricted_content is FALSE then recursively check the entity's
   * ancestors by following the parent field.
   * If no ancestors have localgov_restricted_content set to TRUE then return FALSE.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *
   * @return bool
   *   TRUE if the entity is restricted.
   */
  public function isRestricted(ContentEntityInterface $entity): bool;

}