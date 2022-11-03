<?php

namespace Drupal\content_ownership\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the contact entity.
 *
 * @see \Drupal\content_ownership\Entity\ContentOwner.
 */
class ContentOwnerAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   *
   * Link the activities to the permissions. checkAccess is called with the
   * $operation as defined in the routing.yml file.
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view content owner/sme entity entity');

      case 'edit':
        return AccessResult::allowedIfHasPermission($account, 'edit content owner/sme entity entity');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete content owner/sme entity entity');
    }
    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   *
   * Separate from the checkAccess because the entity does not yet exist, it
   * will be created during the 'add' process.
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add content owner/sme entity entity');
  }

}
?>
