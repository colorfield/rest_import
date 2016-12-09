<?php
namespace Drupal\rest_import;

use Drupal\Core\Entity\EntityInterface;

interface EntityConverterInterface {
  function createProperties($rest_entity);
  function updateProperties(EntityInterface &$entity, $rest_entity);
  function translateProperties(EntityInterface &$entity, $rest_entity);
}
