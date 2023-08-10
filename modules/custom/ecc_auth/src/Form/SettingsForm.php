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

    $form['auto_login_settings']['header_required'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Header is required'),
      '#default_value' => $config->get('header_required'),
    ];


    $form['auto_login_settings']['header_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Header key'),
      '#default_value' => $config->get('header_key'),
    ];


    $form['auto_login_settings']['header_value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Header value'),
      '#default_value' => $config->get('header_value'),
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
    $config->set('header_required', $form_state->getValue('header_required'));
    $config->set('header_key', $form_state->getValue('header_key'));
    $config->set('header_value', $form_state->getValue('header_value'));
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
