<?php

namespace Drupal\training_import;

use Drupal\Core\Datetime\DrupalDateTime;

class TrainingUtils {
  /**
   * @param string $date_in The time string you want to convert
   * @param array  $list_format_in List of format that you want to convert from (ordered)
   * @param string $format_out The output time format
   * @return string The result string, which is the time in format $format_out
   */
  public static function convertDateFormat($date_in, $list_format_in = [], $format_out = 'Y-m-d') {
    try {
      if (is_array($list_format_in)) {
        foreach ($list_format_in as $format_in) {
          try {
            $datetime = DrupalDateTime::createFromFormat($format_in, $date_in);
            return $datetime->format($format_out);
          } catch (\Exception $e) {
            // continue loop
          }
        }
      }
    }
    catch (\Exception $e) {
      \Drupal::logger("Training:Utils")
        ->error("Input date wrong: {$e}, return default");
    }
    return date($format_out);
  }

  /**
   * @param string $name The name of the taxonomy term to find
   * @param string $vocabulary The vocabulary to find the term within, default to find any vocabulary
   * @return int|NULL The ID of the first term found, or NULL if no term found
   */
  public static function getTermIDByName($name, $vocabulary = 'ANY') {
    $query = \Drupal::entityQuery('taxonomy_term');

    if ($vocabulary !== 'ANY') {
      $query = $query->condition('vid', $vocabulary);
    }
    $query = $query->condition('name', $name);
    $tids = $query->execute();

    return $tids ? reset($tids) : NULL;
  }
}