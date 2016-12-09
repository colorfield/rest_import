<?php

namespace Drupal\rest_import\Plugin\QueueWorker;

/**
 * Imports entities on CRON run.
 *
 * @QueueWorker(
 *   id = "cron_entity_process",
 *   title = @Translation("Cron Entity Process"),
 *   cron = {"time" = 10}
 * )
 */
class CronEntityProcess extends EntityProcessBase {}