<?php

namespace Drupal\localgov_restricted_content\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides settings for eu_cookie_compliance module.
 */
class RestrictedContentConfigForm extends ConfigFormBase {

  use StringTranslationTrait;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'localgov_restricted_content_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'localgov_restricted_content.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('localgov_restricted_content.settings');

    $restricted_content_types = $config->get('restricted_content_types') ?? [];
    $contentTypes = $this->entityTypeManager->getStorage('node_type')->loadMultiple();

    $form['content_types'] = [
      '#tree' => TRUE,
      '#type' => 'fieldset',
      '#title' => $this->t('Content types'),
    ];

    foreach ($contentTypes as $contentType) {
      $form['content_types'][$contentType->id()] = [
        '#type' => 'checkbox',
        '#title' => $contentType->label(),
        '#default_value' => in_array($contentType->id(), $restricted_content_types),
      ];
    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('localgov_restricted_content.settings');
    $restricted_content_types = [];
    foreach ($form_state->getValue('content_types') as $id => $value) {
      if ($value) {
        $restricted_content_types[] = $id;
      }
    }
    $config->set('restricted_content_types', $restricted_content_types);
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
