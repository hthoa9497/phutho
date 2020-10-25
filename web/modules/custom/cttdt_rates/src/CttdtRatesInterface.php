<?php

namespace Drupal\cttdt_rates;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a cttdt rates entity type.
 */
interface CttdtRatesInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  /**
   * Gets the cttdt rates title.
   *
   * @return string
   *   Title of the cttdt rates.
   */
  public function getTitle();

  /**
   * Sets the cttdt rates title.
   *
   * @param string $title
   *   The cttdt rates title.
   *
   * @return \Drupal\cttdt_rates\CttdtRatesInterface
   *   The called cttdt rates entity.
   */
  public function setTitle($title);

  /**
   * Gets the cttdt rates creation timestamp.
   *
   * @return int
   *   Creation timestamp of the cttdt rates.
   */
  public function getCreatedTime();

  /**
   * Sets the cttdt rates creation timestamp.
   *
   * @param int $timestamp
   *   The cttdt rates creation timestamp.
   *
   * @return \Drupal\cttdt_rates\CttdtRatesInterface
   *   The called cttdt rates entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the cttdt rates status.
   *
   * @return bool
   *   TRUE if the cttdt rates is enabled, FALSE otherwise.
   */
  public function isEnabled();

  /**
   * Sets the cttdt rates status.
   *
   * @param bool $status
   *   TRUE to enable this cttdt rates, FALSE to disable.
   *
   * @return \Drupal\cttdt_rates\CttdtRatesInterface
   *   The called cttdt rates entity.
   */
  public function setStatus($status);

}
