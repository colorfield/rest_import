<?php

namespace Drupal\rest_import\Plugin\QueueWorker;

/**
 * Imports entities via a manual action triggered by an admin.
 *
 * @QueueWorker(
 *   id = "manual_entity_process",
 *   title = @Translation("Manual Entity Process"),
 * )
 */
class ManualEntityProcess extends EntityProcessBase {}