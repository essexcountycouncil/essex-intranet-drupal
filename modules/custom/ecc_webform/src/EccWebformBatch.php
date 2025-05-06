<?php

namespace Drupal\ecc_webform;

/**
 * @file
 * Requirements and update functions for the ecc_webform module.
 */

/**
 * Batch file to process webform submissions.
 */
class EccWebformBatch {

  /**
   * Clear all personal data from the feedback form.
   *
   * @param string $webform_id
   *   The webform ID.
   * @param int $offset
   *   The offset value.
   * @param int $limit
   *   The limit value.
   * @param array $context
   *   The context array.
   */
  public static function processSubmissions(string $webform_id, int $offset, int $limit, array &$context): void {
    // Query the submissions within the given range.
    $submission_ids = \Drupal::entityQuery('webform_submission')
      ->condition('webform_id', $webform_id)
      ->accessCheck(FALSE)
      ->range($offset, $limit)
      ->execute();

    $submissions = \Drupal::entityTypeManager()
      ->getStorage('webform_submission')
      ->loadMultiple($submission_ids);

    /** @var \Drupal\ecc_webform\EccWebformManager $ecc_webform */
    $ecc_webform_manager = \Drupal::service('ecc_webform.manager');
    // Load the submissions and process each one.
    foreach ($submissions as $submission) {
      $submission = $ecc_webform_manager->clearSubmissionPersonalData($submission);
      $submission->save();
    }

    $context['message'] = t('Processed @num submissions.', ['@num' => count($submissions)]);
  }

  /**
   * Finish the batch process.
   *
   * @param bool $success
   *   Whether the batch was successful.
   * @param array $results
   *   The results of the batch process.
   * @param array $operations
   *   The operations that were performed.
   */
  public static function finishBatch(bool $success, array $results, array $operations): void {
    if ($success) {
      \Drupal::logger('ecc_webform')->notice('All submissions processed successfully.');
    }
    else {
      \Drupal::logger('ecc_webform')->error('There were errors processing some submissions.');
    }
  }
}