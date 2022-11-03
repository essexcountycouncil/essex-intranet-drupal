<?php

namespace Drupal\content_ownership;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;

/**
 * Provides a list controller for content_owner_sme entity.
 *
 * @ingroup content_owner_sme
 */
class ContentOwnerListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    $build['description'] = [
      '#markup' => $this->t('Content Entity Example implements a Contacts model. These contacts are fieldable entities.'),
    ];

    $build += parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the contact list.
   *
   * Calling the parent::buildHeader() adds a column for the possible actions
   * and inserts the 'edit' and 'delete' links as defined for the entity type.
   */
  public function buildHeader() {
    $header['id'] = $this->t('Content Owner ID');
    $header['name'] = $this->t('Display Name');
    $header['email'] = $this->t('E-mail');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\content_ownership\Entity\ContentOwner */
    $row['id'] = $entity->id();
    $row['name'] = $entity->label();
    $row['email'] = $entity->email->value;
    return $row + parent::buildRow($entity);
  }

}
?>
