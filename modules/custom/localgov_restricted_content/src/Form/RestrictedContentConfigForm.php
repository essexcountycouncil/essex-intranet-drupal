<?php

namespace Drupal\localgov_restricted_content\Form;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\Url;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Routing\RequestContext;
use Drupal\eu_cookie_compliance\Plugin\ConsentStorageManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\user\RoleStorageInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides settings for eu_cookie_compliance module.
 */
class RestrictedContentConfigForm extends ConfigFormBase {

  use StringTranslationTrait;

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
    $entityTypeManager = \Drupal::service('entity_type.manager');
    $contentTypes = $entityTypeManager->getStorage('node_type')->loadMultiple();

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
