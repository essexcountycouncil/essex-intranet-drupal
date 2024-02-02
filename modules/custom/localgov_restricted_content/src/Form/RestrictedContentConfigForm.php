<?php

namespace Drupal\localgov_restricted_content\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

 * Provides settings for localgov_restricted_content module.
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
    $content_types = $this->entityTypeManager->getStorage('node_type')->loadMultiple();
    asort($content_types);

    $form['content_types'] = [
      '#tree' => TRUE,
      '#type' => 'fieldset',
      '#title' => $this->t('Content types'),
    ];

    foreach ($content_types as $content_type) {
      $form['content_types'][$content_type->id()] = [
        '#type' => 'checkbox',
        '#title' => $content_type->label(),
        '#default_value' => in_array($content_type->id(), $restricted_content_types),
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
