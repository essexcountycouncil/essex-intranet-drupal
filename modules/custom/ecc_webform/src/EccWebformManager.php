<?php

declare(strict_types = 1);

namespace Drupal\ecc_webform;

use Drupal\webform\WebformSubmissionInterface;

/**
 * Provide additional functionality for webform.
 */
class EccWebformManager {

  /**
   * Clear all personal data from a submission.
   *
   * @param \Drupal\webform\WebformSubmissionInterface $submission
   *   The webform submission entity.
   *
   * @return \Drupal\webform\WebformSubmissionInterface
   *   The updated webform submission entity.
   */
  public function clearSubmissionPersonalData(WebformSubmissionInterface $submission): WebformSubmissionInterface {
    $submission->set('remote_addr', '');
    $submission->setOwnerId(0);
    return $submission;
  }

}
