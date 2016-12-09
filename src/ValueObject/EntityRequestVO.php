<?php

namespace Drupal\rest_import\ValueObject;


class EntityRequestVO {

  const REQUEST_TYPE_UNIQUE   = 'unique';
  const REQUEST_TYPE_MULTIPLE = 'multiple';

  public $requestType;
  public $requestLimit;
  public $entityType;
  public $entityId;
  public $entityLanguage;

}