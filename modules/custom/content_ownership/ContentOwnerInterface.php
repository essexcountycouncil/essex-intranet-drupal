<?php

namespace Drupal\content_ownership;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a Contact entity.
 * @ingroup content_ownership
 */
interface ContentOwnerInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}

?>
