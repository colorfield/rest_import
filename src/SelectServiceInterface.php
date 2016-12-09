<?php

namespace Drupal\rest_import;

use Drupal\rest_import\ValueObject\EntityRequestVO;
use Drupal\rest_import\ValueObject\EntitySelectVO;

interface SelectServiceInterface {
  /**
   * Selects source entities from the endpoint.
   *
   * @param EntityRequestVO $requestVO
   * @return EntitySelectVO
   */
  public function selectSourceEntities(EntityRequestVO $requestVO);

  /**
   * Prepares the queue with source entities to be processed for import.
   *
   * @param EntitySelectVO $items
   */
  public function enqueueSourceEntities(EntitySelectVO $entitySelectVO);
}
