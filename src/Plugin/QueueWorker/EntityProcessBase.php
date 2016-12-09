<?php
/**
 * @file
 * Contains Drupal\rest_import\Plugin\QueueWorker\EntityImportBase
 */

namespace Drupal\rest_import\Plugin\QueueWorker;

use Drupal\Core\Entity\Entity;
use Drupal\rest_import\EntityCreator;
use Drupal\rest_import\EntityMapper;
use Drupal\rest_import\EntityUpdater;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides base functionality for the EntityImportBase Queue Workers.
 * Worker that processes a batch of items to be converted into entities.
 */
abstract class EntityProcessBase extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  const OPERATION_CREATE = 'create';
  const OPERATION_UPDATE = 'update';
  const OPERATION_DELETE = 'delete';

  /**
   * The node storage.
   * @todo to be moved in EntityCreator
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $entityStorage;

  /**
   * Creates a new EntityProcessBase object.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_storage
   *   The node storage.
   */
  public function __construct(EntityStorageInterface $entity_storage) {
    $this->entityStorage = $entity_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('entity.manager')->getStorage('node')
    );
  }

  /**
   * Processes create entity operation.
   *
   * @param $rest_entity
   */
  protected function createEntity($rest_entity) {

    $entityCreator = new EntityCreator();
    // should contain entity id (node id, term id, user id)
    // creates the source Drupal entity to be translated.
    // @todo create translation if necessary (logging also necessary)
    $resultEntity = $entityCreator->createSource($rest_entity);
    // kint($resultEntity);

    $targetEntity = $resultEntity['entity'];
    if($targetEntity instanceof Entity) {
      // Returns 1 if the entity was created
      drupal_set_message('RESULT = ' . $resultEntity['result']);
      drupal_set_message('ID = ' . $targetEntity->id());
      if((int) $resultEntity['result'] === 1) {
        // Creates the record in the map table if it succeed
        // only create operations have to be logged into the log table
        EntityMapper::writeMap($rest_entity, $targetEntity);
      }
    }

    // log the operation
    $this->logEntityProcess($rest_entity,
                            $targetEntity,
                            $resultEntity['result'],
                            EntityProcessBase::OPERATION_CREATE);

    // send the result of the operation to the web service
    $this->sendEntityProcessResult($rest_entity, $targetEntity, $resultEntity['result']);

  }

  /**
   * Processes update entity operation.
   *
   * @param $rest_entity
   */
  protected function updateEntity($rest_entity) {

    drupal_set_message('Updating entity');
    $entityUpdater = new EntityUpdater();
    // @todo create translation if necessary (logging also necessary)
    $resultEntity = $entityUpdater->updateSource($rest_entity);

    $targetEntity = $resultEntity['entity'];

    // log the operation
    $this->logEntityProcess($rest_entity,
                            $targetEntity,
                            $resultEntity['result'],
                            EntityProcessBase::OPERATION_UPDATE);

    // send the result of the operation to the web service
    $this->sendEntityProcessResult($rest_entity, $targetEntity, $resultEntity['result']);

  }

  /**
   * Processes delete entity operation.
   *
   * @param $rest_entity
   */
  protected function deleteEntity($rest_entity) {

    // @todo fetch entity id from the map table

    // @todo delete the Drupal entity
    // the entity deletion in the map table is delegated to the
    // hook_entity_delete() to cover the case of a manual delete
    // @todo delete translation

    // @todo log the operation
    /*
    $this->logEntityProcess($rest_entity,
                            $targetEntity,
                            $resultEntity['result'],
                            EntityProcessBase::OPERATION_DELETE);
    */

    // @todo send the result of the operation to the web service
  }

  /**
   * Logs the result of an operation on a single entity.
   *
   * @param $result
   * @param $item
   */
  private function logEntityProcess($rest_entity, Entity $target_entity, $result, $applied_operation) {
    //kint($rest_entity);
    //kint($target_entity);
    $fields = array(
      'source_id' => (string) $rest_entity['id'], // some systems can have other id than integer
      'target_id' => (int) $target_entity->id(),
      'source_entity_type' => (string) $rest_entity['type'],
      'target_entity_type' => (string) $target_entity->getEntityTypeId(),
      'target_entity_bundle' => (string) $target_entity->bundle(),
      'language' => (string) $rest_entity['language'],
      'requested_operation' => (string) $rest_entity['operation'],
      'applied_operation' => (string) $applied_operation,
      'status' => (int) $result,
      'timestamp' => REQUEST_TIME,
    );
    try{
      $insert = \Drupal::database()->insert('rest_import_log');
      //$insert = Database::getConnection('default')->insert('rest_import_log');
      $insert->fields($fields);
      $insert->execute();
    }catch (\Exception $e) {
      drupal_set_message($e->getMessage(), 'error');
      // @todo watchdog or watchdog exception
    }
  }

  /**
   * Sends the result of an operation on a single entity to the webservice.
   *
   * @param $result
   * @param $item
   */
  private function sendEntityProcessResult($rest_entity, Entity $target_entity, $result) {
    // @todo implement send result to the web service
    // result 1 (create) / 2 (update ?) !!
  }

  /**
   * Fetches target entity from the rest entity.
   * @todo refactor with EntityMapper
   *
   * @param $rest_entity
   * @return mixed
   */
  private function getTargetEntityId($rest_entity) {
    $sourceEntityID = $rest_entity['id'];
    $sourceEntityType = $rest_entity['type'];
    $query = \Drupal::database()->select('rest_import_map', 'map');
    // Fetch the target entity id mapping from the log table
    $query->fields('map', ['target_id']);
    $query->condition('map.source_id', $sourceEntityID);
    $query->condition('map.source_entity_type', $sourceEntityType);
    $targetId = $query->execute()->fetchField();
    return $targetId;
  }

  /**
   * Check here if the entity mapped with the external system exists.
   *
   * @param $data
   */
  private function entityExists($rest_entity) {
    $result = false;

    // An alternate path of what is commented below can be to store the
    // source id straight into a rest_import_map table.
    // It adds redundancy but the select is still simpler
    // and with many records in the log table it will keep the same response time,
    // even if the log table grows too much.
    // The map table is also maintained via hook_entity_delete
    // even if someone deletes accidentally an entity under Drupal.
    // @todo improve this test
    // @todo use EntityMapper instead
    $targetId = $this->getTargetEntityId($rest_entity);
    if(!empty($targetId)) {
      $result = true;
    }

//    // For an entity to exist, it should have been created at least once
//    $query = \Drupal::database()->select('rest_import_log', 'log');
//    // Fetch the target entity id mapping from the log table
//    $query->fields('log', ['target_id', 'operation']);
//    $query->condition('log.source_entity_id', $sourceEntityID);
//    $targetEntityID = $query->execute();
//
//    // and it's last action in the log table should not be a delete operation.
//    // (... code ...)
//    // If a correct mapping already exists, see if the entity still exists,
//    // it could have been deleted via Drupal and not via the web service (shouldn't but still a possibility).
//    if (!empty($targetEntityID)) {
//      /** @var @todo $query */
//      // @todo dependency injection
//      $entityQueryService = \Drupal::getContainer()->get('entity.query');
//      // @todo entity_type should be defined as a private method that maps the destination_entity bundle to the entity_type
//      $query = $entityQueryService->get('node');
//      $query->condition('nid', $nid);
//      $entity_ids = $query->execute();
//      if (!empty($entity_ids)) {
//        $result = true;
//      }
//    }

    // @todo return result
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($rest_entity) {
    drupal_set_message('Queue process item');
    //kint($rest_entity);

    // Several cases are possible here where things can go wrong
    // - create an entity that already exists
    // - update an entity that does not exists anymore
    // - ...

    // So let's start by a reliable check of Drupal entity existence
    // if entity exists, load it and update it

    switch ($rest_entity['operation']) {
      // create
      case EntityProcessBase::OPERATION_CREATE:
        if($this->entityExists($rest_entity)) {
          drupal_set_message(t('Entity exists, updating.'), 'warning');
          // @todo watchdog notice
          $this->updateEntity($rest_entity);
          // otherwise create it
        }else {
          drupal_set_message(t('Entity does not exist, creating.'));
          $this->createEntity($rest_entity);
        }
        break;

      // update
      case EntityProcessBase::OPERATION_UPDATE:
        if($this->entityExists($rest_entity)) {
          drupal_set_message('Entity exists, updating.');
          $this->updateEntity($rest_entity);
          // otherwise create it
        }else {
          drupal_set_message('Entity does not exist, creating.', 'warning');
          // @todo watchdog notice
          $this->createEntity($rest_entity);
        }
        break;

      // delete
      case EntityProcessBase::OPERATION_DELETE:
        if($this->entityExists($rest_entity)) {
          drupal_set_message('Entity exists, deleting.');
          $this->deleteEntity($rest_entity);
        }else {
          drupal_set_message('Entity does not exist, unable to delete.', 'warning');
          // @todo watchdog notice
          // @todo elaborate tests by trying to test if entity exists even if not mapped
          $this->createEntity($rest_entity);
        }
        break;

      // unsupported operation
      default:
        // @todo watchdog + exeception (watchdog_exception)
        break;
    }

  }
}