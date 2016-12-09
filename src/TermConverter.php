<?php
namespace Drupal\rest_import;
use Drupal\Core\Entity\EntityInterface;

/**
 * Class TermConverter
 * Stub class that can be used for generic term import.
 * @package Drupal\rest_import
 */
class TermConverter implements EntityConverterInterface {
  public function createProperties($rest_entity) {
    // @todo implement if any
    $properties = [];
    return $properties;
  }
  public function updateProperties(EntityInterface &$term, $rest_entity) {
    // @todo implement if any
  }
  public function translateProperties(EntityInterface &$term, $rest_entity) {
    // @todo implement if any
  }
}