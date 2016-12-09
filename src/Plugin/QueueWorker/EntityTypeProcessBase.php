<?php
/**
 * @file
 * Contains Drupal\rest_import\Plugin\QueueWorker\EntityTypeProcessBase
 */

namespace Drupal\rest_import\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;

/**
 * Provides base functionality for the EntityTypeProcessBase Queue Workers.
 * Worker that relaunches an entity type import based on the entities left.
 */
abstract class EntityTypeProcessBase extends QueueWorkerBase {
  /**
   * {@inheritdoc}
   */
  public function processItem($item) {

  }
}