<?php

namespace Drupal\ecc_webform\Plugin\WebformHandler;

use Drupal\ecc_webform\EccWebformManager;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Webform submission clear personal data handler.
 *
 * @WebformHandler(
 *   id = "ecc_webform_clear_personal_data",
 *   label = @Translation("Clear personal data"),
 *   category = @Translation("ECC Webform"),
 *   description = @Translation("Clear personal data from webform submission."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 *   tokens = FALSE,
 * )
 */
class EccWebformClearPersonalDataHandler extends WebformHandlerBase {

  /**
   * The webform token manager.
   *
   * @var \Drupal\ecc_webform\EccWebformManager
   */
  protected $eccWebformManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->eccWebformManager = $container->get('ecc_webform.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(WebformSubmissionInterface $webform_submission) {
    if ($webform_submission->isNew()) {
      $this->eccWebformManager->clearSubmissionPersonalData($webform_submission);
    }
  }

}
