<?php

namespace Drupal\rest_import;
use Drupal\Component\Serialization\Json;

/**
 * Class SendResultService.
 *
 * @package Drupal\rest_import
 */
class SendResultService implements SendResultServiceInterface {

  /**
   * Drupal\Component\Serialization\Json definition.
   *
   * @var \Drupal\Component\Serialization\Json
   */
  protected $serializationJson;
  /**
   * Constructor.
   */
  public function __construct(Json $serialization_json) {
    $this->serializationJson = $serialization_json;
  }

  /**
   * Serializes the status, to be sent to the web service as a result
   * of the transaction.
   *
   * @param $entity
   * @param $format
   */
  public function serialize($entity, $format) {
    $serializer = \Drupal::service('serializer');
    return $serializer->serialize($entity, $format);
  }

  public function sendResult() {
    // @todo implement
  }

}
