<?php
namespace Drupal\rest_import;

use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * Helpers that update entities.
 */
class EntityUpdater
{

  public function updateSource($rest_entity) {
    $result = null;
    // maps the source entity type to the target entity type and target entity id
    $entityMapper = new EntityMapper($rest_entity['type']);
    // creates entity based on the target entity type
    switch ($entityMapper->type) {
      case 'node':
        drupal_set_message(t('Update node of entity bundle @entity_bundle', ['@entity_bundle' => $entityMapper->bundle]));
        $result = $this->updateNode($rest_entity, $entityMapper);
        break;
      case 'taxonomy_term':
        drupal_set_message(t('Update term of entity bundle @entity_bundle', ['@entity_bundle' => $entityMapper->bundle]));
        $result = $this->updateTerm($rest_entity, $entityMapper);
        break;
    }
    return $result;
  }

  private function updateTranslation($rest_entity) {
    // @todo implement
    // the translation should be achieved by fetching the translation of an
    // entity then using the $entity_mapper->converter->translateProperties($rest_entity);
    // considering this, we can prepare the translation with a recall of
    // the rest_entity
  }

  private function updateNode($rest_entity, EntityMapper $entity_mapper) {
    // @todo implement
    $result = null;
    $node = Node::load($entity_mapper->getTargetEntityId($rest_entity['id']));
    // use EntityConverterInterface concrete class to update custom properties
    $entity_mapper->converter->updateProperties($node, $rest_entity);
    $result = $node->save(); // !! returns "2"

    $nodeResult = [
      'result' => $result,
      'entity' => $node,
    ];
    return $nodeResult;
  }

  private function updateTerm($rest_entity, EntityMapper $entityMapper) {
    // @todo implement
    $result = null;
    return $result;
  }

}