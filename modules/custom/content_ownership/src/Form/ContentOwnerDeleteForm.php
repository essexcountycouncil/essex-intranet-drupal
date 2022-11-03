<?php

namespace Drupal\content_ownership\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a form for deleting a content_owner_sme entity.
 *
 * @ingroup content_owner_sme
 */
class ContentOwnerDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * Returns the question to ask the user.
   *
   * @return string
   *   The form question. The page title will be set to this value.
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete Content Owner: %name?', array('%name' => $this->entity->label()));
  }

  /**ContentOWn
   * Returns the route to go to if the user cancels the action.
   *
   * @return \Drupal\Core\Url
   *   A URL object.
   */
  public function getCancelUrl() {
    return new Url('entity.content_owner_sme.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   *
   * Delete the entity and log the event. logger() replaces the watchdog.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity = $this->getEntity();
    $entity->delete();

    $this->logger('content_owner_sme')->notice('deleted %title.',
      array(
        '%title' => $this->entity->label(),
      ));
    // Redirect to term list after delete.
    $form_state->setRedirect('entity.content_owner_sme.collection');
  }
}
?>
