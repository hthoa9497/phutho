<?php

namespace Drupal\question_survey;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a question survey entity type.
 */
interface QuestionSurveyInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  /**
   * Gets the question survey title.
   *
   * @return string
   *   Title of the question survey.
   */
  public function getTitle();

  /**
   * Sets the question survey title.
   *
   * @param string $title
   *   The question survey title.
   *
   * @return \Drupal\question_survey\QuestionSurveyInterface
   *   The called question survey entity.
   */
  public function setTitle($title);

  /**
   * Gets the question survey creation timestamp.
   *
   * @return int
   *   Creation timestamp of the question survey.
   */
  public function getCreatedTime();

  /**
   * Sets the question survey creation timestamp.
   *
   * @param int $timestamp
   *   The question survey creation timestamp.
   *
   * @return \Drupal\question_survey\QuestionSurveyInterface
   *   The called question survey entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the question survey status.
   *
   * @return bool
   *   TRUE if the question survey is enabled, FALSE otherwise.
   */
  public function isEnabled();

  /**
   * Sets the question survey status.
   *
   * @param bool $status
   *   TRUE to enable this question survey, FALSE to disable.
   *
   * @return \Drupal\question_survey\QuestionSurveyInterface
   *   The called question survey entity.
   */
  public function setStatus($status);

}
