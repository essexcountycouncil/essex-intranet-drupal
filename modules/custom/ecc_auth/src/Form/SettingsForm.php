<?php

namespace Drupal\ecc_auth\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings form.
 *
 * @package Drupal\ecc_auth\Form
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ecc_auth_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ecc_auth.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ecc_auth.settings');

    $form['auto_login_settings'] = [
      '#tree' => FALSE,
      '#type' => 'fieldset',
      '#title' => $this->t('Auto-login settings'),
    ];

    $form['auto_login_settings']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable auto-login'),
      '#default_value' => $config->get('enabled'),
    ];

    $form['auto_login_settings']['client'] = [
      '#type' => 'textfield',
      '#title' => $this->t('OpenID Connect client id'),
      '#default_value' => $config->get('client'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('ecc_auth.settings');
    $config->set('enabled', $form_state->getValue('enabled'));
    $config->set('client', $form_state->getValue('client'));
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
