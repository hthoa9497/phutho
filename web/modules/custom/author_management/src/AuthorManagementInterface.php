<?php

namespace Drupal\author_management;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining an author management entity type.
 */
interface AuthorManagementInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Gets the author management title.
   *
   * @return string
   *   Title of the author management.
   */
  public function getTitle();

  /**
   * Sets the author management title.
   *
   * @param string $title
   *   The author management title.
   *
   * @return \Drupal\author_management\AuthorManagementInterface
   *   The called author management entity.
   */
  public function setTitle($title);

  /**
   * Gets the author management creation timestamp.
   *
   * @return int
   *   Creation timestamp of the author management.
   */
  public function getCreatedTime();

  /**
   * Sets the author management creation timestamp.
   *
   * @param int $timestamp
   *   The author management creation timestamp.
   *
   * @return \Drupal\author_management\AuthorManagementInterface
   *   The called author management entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the author management status.
   *
   * @return bool
   *   TRUE if the author management is enabled, FALSE otherwise.
   */
  public function isEnabled();

  /**
   * Sets the author management status.
   *
   * @param bool $status
   *   TRUE to enable this author management, FALSE to disable.
   *
   * @return \Drupal\author_management\AuthorManagementInterface
   *   The called author management entity.
   */
  public function setStatus($status);

}
