<?php

namespace Drupal\rest_import\ValueObject;


class EntitySelectVO {

  const STATUS_READY_TO_IMPORT        = 0;
  const STATUS_ENDPOINT_NOT_AVAILABLE = 1;
  const STATUS_ENDPOINT_NOT_REACHABLE = 2;

  public $status = self::STATUS_READY_TO_IMPORT;
  public $sourceEntities = [];

  /**
   * {@inheritdoc}
   */
  public function __construct() {
    $this->status = self::STATUS_READY_TO_IMPORT;
    $this->sourceEntities = [];
  }

}