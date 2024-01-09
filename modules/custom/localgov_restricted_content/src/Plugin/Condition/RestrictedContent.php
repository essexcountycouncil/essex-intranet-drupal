<?php

namespace Drupal\localgov_restricted_content\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\NodeInterface;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Restricted Content' condition.
 *
 * @Condition(
 *   id = "restricted_content",
 *   label = @Translation("Restricted Content"),
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:node", label = @Translation("Current node"), required = TRUE),
 *     "user" = @ContextDefinition("entity:user", label = @Translation("User"))
 *   }
 * )
 */
class RestrictedContent extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Restricted Content service.
   *
   * @var \Drupal\localgov_restricted_content\RestrictedContentInterface
   */
  protected $restrictedContent;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->restrictedContent = $container->get('localgov_restricted_content.restricted_content');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    return $this->t('@state true if user is not logged in and the current page is restricted content.', [
      '@state' => empty($this->configuration['negate']) ? $this->t('Return') : $this->t('Do not return'),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['enabled' => FALSE] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $this->configuration['enabled'],
      '#description' => $this->t('Check if user can view restricted content.'),
    ];
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['enabled'] = $form_state->getValue('enabled');
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function isEnabled() {
    return !empty($this->configuration['enabled']);
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    if (!$this->isEnabled()) {
      return TRUE;
    }
    /** @var \Drupal\user\UserInterface $user */
    $user = $this->getContextValue('user');
    if ($user instanceof UserInterface and $user->isAuthenticated()) {
      return TRUE;
    }

    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->getContextValue('node');
    return (
      $node instanceof NodeInterface and
      !$this->restrictedContent->isRestricted($node)
    );
  }

}
