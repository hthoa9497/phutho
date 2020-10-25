<?php

namespace Drupal\training_import;


/**
 * Custom exception class
 */
class TrainingImportException extends Exception
{
  /**
   * @inheritdoc
   */
  public function __construct($message, $code = 0, Exception $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}
