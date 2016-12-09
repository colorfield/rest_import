<?php
namespace Drupal\rest_import;

use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\file\Entity\File;

/**
 * Helper that create entities.
 */
class EntityCreator {

  /**
   * Creates a Drupal source entity that can be translated.
   *
   * @param $source_entity
   * @return array|null
   */
  public function createSource($rest_entity) {
    $result = null;
    // maps the source entity type to the target entity type and target entity id
    $entityMapper = new EntityMapper($rest_entity['type']);
    // creates entity based on the target entity type
    switch ($entityMapper->type) {
      case 'node':
        drupal_set_message(t('Create node of entity bundle @entity_bundle', ['@entity_bundle' => $entityMapper->bundle]));
        $result = $this->createNode($rest_entity, $entityMapper);
        break;
      case 'taxonomy_term':
        drupal_set_message(t('Create term of entity bundle @entity_bundle', ['@entity_bundle' => $entityMapper->bundle]));
        $result = $this->createTerm($rest_entity, $entityMapper);
        break;
    }
    return $result;
  }

  /**
   * Creates a translation for a source entity.
   * @param $item
   * @return null
   */
  public function createTranslation(EntityInterface $entity) {
    // @todo implement
    $result = null;
    // the translation should be achieved by fetching the translation of an
    // entity then using the $entity_mapper->converter->translateProperties($rest_entity);
    // considering this, we can prepare the translation with a recall of
    // the rest_entity
    return $result;
  }

  /**
   * Get user id to identify content created from the REST resource.
   *
   * @return int
   */
  private function getCreatorUserId() {
    return \Drupal::config('rest_import.web_service')->get('import_uid');
  }

  /**
   * Creates a node.
   */
  private function createNode($rest_entity, EntityMapper $entity_mapper) {

    /** @var NodeInterface $node */
    //$node = $this->nodeStorage->load($data->nid);
    /*
    if (!$node->isPublished() && $node instanceof NodeInterface) {
      return $this->publishNode($node);
    }
    */

    $properties = [
      'type' => $entity_mapper->bundle,
      'langcode' => $rest_entity['language'],
      'created' => REQUEST_TIME,
      'changed' => REQUEST_TIME,
      'uid' => $this->getCreatorUserId(),
    ];
    // use EntityConverterInterface concrete class to append custom properties
    $properties += $entity_mapper->converter->createProperties($rest_entity);

    //kint($properties);

    $node = Node::create($properties);
    $result = $node->save();

    $nodeResult = [
      'result' => $result,
      'entity' => $node,
    ];

    return $nodeResult;
  }


  /**
   * Creates a taxonomy term.
   */
  private function createTerm($rest_entity, EntityMapper $entity_mapper) {

    $properties = [
      'vid' => $entity_mapper->bundle,
      'langcode' => $rest_entity['language'],
      'name' => $rest_entity['attributes']['name'],
      //'description' => [
      //  'value' => '<p>' . $description . '</p>',
      //  'format' => 'full_html',
      //],
      'weight' => 0,
      //'parent' => array (0),
    ];

    // use EntityConverterInterface concrete class to append custom properties
    $properties += $entity_mapper->converter->createProperties($rest_entity);

    $term = Term::create($properties);
    $result = $term->save();

    $termResult = [
      'result' => $result,
      'entity' => $term,
    ];

    return $termResult;
  }

  /**
   * Creates a term and its translations.
   * @param $vocabulary
   * @param $translations
   * @param string $source
   * @return array
   */
  /*
  private function createTranslatedTerm($vocabulary, $translations, $source = 'en') {
    $terms = array();
    foreach ($translations as $language => $translation) {
      // @todo implement
    }
    return $terms;
  }
  */

  /**
   * Creates a file from a URI
   * @param $uri
   * @return \Drupal\Core\Entity\EntityInterface|static
   */
  /*
  private function createFileFromURI($uri) {
    $file = File::create([
      'uid' => 1,
      'uri' => $uri,
      'status' => 1,
    ]);
    $file->save();
    return $file;
  }
  */

  /**
   * Creates a node and attach images.
   * @param $properties
   * @param $files
   * @return \Drupal\Core\Entity\EntityInterface|static
   */
  /*
  private function createNodeWithImages($properties, $files) {

    $nodeProperties = [
      'type' => 'article',
      'langcode' => 'en',
      'created' => REQUEST_TIME,
      'changed' => REQUEST_TIME,
      'uid' => 1,
      'title' => 'My node with images',
      'field_tags' => [1, 2, 3],
      'body' => [
        'summary' => '',
        'value' => 'My node',
        'format' => 'full_html',
      ],
    ];

    foreach ($files as $file) {
      $nodeProperties['field_image'][] = [
        'target_id' => $file->id(),
        'alt' => "alt",
      ];
    }

    $node = Node::create($nodeProperties);
    $node->save();
    // \Drupal::service('path.alias_storage')->save('/node/' . $node->id(), '/my-path', 'en');
    return $node;
  }
  */

  /**
   * Creates a node with image field.
   */
  /*
  private function createNodeWithImage($properties, File $file) {
    $node = Node::create([
      'type' => 'article',
      'langcode' => 'en',
      'created' => REQUEST_TIME,
      'changed' => REQUEST_TIME,
      'uid' => 1,
      'title' => 'My node with images',
      //'field_tags' =>[2],
      'body' => [
        'summary' => '',
        'value' => 'My node',
        'format' => 'full_html',
      ],
      'field_image' => [
        [
          'target_id' => $file->id(),
          'alt' => "alt",
        ],
      ],
    ]);
    $node->save();
    // \Drupal::service('path.alias_storage')->save('/node/' . $node->id(), '/my-path', 'en');
    return $node;
  }
  */
}