<?php
namespace Drupal\rest_import;


use Drupal\Core\Entity\Entity;

/**
 * Class EntityMapper
 * @todo this class should differentiate a bit for term and node or variable should be renamed.
 * Meanwhile type is used for node and term, bundle for node bundle or vocabulary.
 *
 * @package Drupal\rest_import
 */
class EntityMapper {

  /**
   * @var $type
   *  The target entity type.
   */
  public $type;

  /**
   * @var $bundle
   *  The target entity bundle: node type or vocabulary id (named "entity type id").
   */
  public $bundle;

  /**
   * @var EntityConverterInterface $converter
   *  The converter class.
   */
  public $converter;

  /**
   * Defines the mapping between third party and Drupal entities.
   * @todo add other entity types mapping
   *
   * @param $rest_entity_type
   *   The source (third party) entity type.
   */
  public function __construct($rest_entity_type) {
    $converter = null;
    // @todo map this from the YAML file that will be used for the SequenceController
    switch($rest_entity_type) {

      // Content types
      case 'licence':
        $this->type = 'node';
        $this->bundle = 'licence_file';
        $converter = __NAMESPACE__ . '\LicenceFileConverter';
        break;

      // Vocabularies
      case 'licence_status':
        $this->type = 'taxonomy_term';
        $this->bundle = 'licence_status';
        // use stub class, can be specialized if necessary
        $converter = __NAMESPACE__ . '\TermConverter';
        break;
      case 'certification_system':
        $this->type = 'taxonomy_term';
        $this->bundle = 'certification_system';
        // use stub class, can be specialized if necessary
        $converter = __NAMESPACE__ . '\TermConverter';
        break;
      case 'certification_body':
        $this->type = 'taxonomy_term';
        $this->bundle = 'certification_body';
        // use stub class, can be specialized if necessary
        $converter = __NAMESPACE__ . '\TermConverter';
        break;
    }
    $this->converter = new $converter();
  }

  /**
   * Writes a record into the rest_import_map table.
   *
   * @param $entity
   * @param $item
   */
  public static function writeMap($rest_entity, Entity $target_entity) {
    $fields = array(
      'source_id' => (string) $rest_entity['id'], // some systems can have other id than integer
      'target_id' => (int) $target_entity->id(),
      'source_entity_type' => (string) $rest_entity['type'],
      'target_entity_type' => (string) $target_entity->getEntityTypeId(),
      'target_entity_bundle' => (string) $target_entity->bundle(),
      // Language is probably not needed, it depends on the rest structure
      // it can still have several keys per translated entities (like D7 i18n).
      'language' => (string) $rest_entity['language'],
      'timestamp' => REQUEST_TIME,
    );
    try{
      $insert = \Drupal::database()->insert('rest_import_map');
      //$insert = Database::getConnection('default')->insert('rest_import_map');
      $insert->fields($fields);
      $insert->execute();
    }catch (\Exception $e) {
      drupal_set_message($e->getMessage(), 'error');
      // @todo watchdog or watchdog exception
    }
  }

  /**
   * Fetches the target entity map (id, type, bundle) from the rest
   * entity id and type.
   *
   * @param $rest_entity_id
   * @return int
   */
  public function getTargetEntityId($rest_entity_id) {
    $query = \Drupal::database()->select('rest_import_map', 'map');
    // Fetch the target entity id from the log table
    $query->fields('map', ['target_id']);
    $query->condition('map.source_id', $rest_entity_id);
    $query->condition('map.target_entity_type', $this->type);
    $query->condition('map.target_entity_bundle', $this->bundle);
    $result = $query->execute()->fetchField();
    return $result;
  }


}