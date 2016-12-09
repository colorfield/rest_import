<?php

namespace Drupal\rest_import;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Queue\QueueInterface;
use Drupal\rest_import\LinkTools;
use Drupal\rest_import\ValueObject\EntityRequestVO;
use Drupal\rest_import\ValueObject\EntitySelectVO;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Client;

/**
 * Class SelectService.
 *
 * @package Drupal\rest_import
 */
class SelectService implements SelectServiceInterface {

  /**
   * GuzzleHttp\Client definition.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * Constructor.
   */
  public function __construct(Client $http_client) {
    $this->httpClient = $http_client;
  }

  /**
   *
   * @return mixed|null
   */
  public function getManualQueueLink() {
    return LinkTools::getLinkFromRoute(t('Process queue'), 'rest_import.entity_import_queue_form');
  }

  /**
   * {@inheritdoc}
   */
  public function selectSourceEntities(EntityRequestVO $requestVO) {

    $entitySelect = new EntitySelectVO();

    $debugMode = \Drupal::config('rest_import.web_service')->get('debug_mode');
    $endpoint = \Drupal::config('rest_import.web_service')->get('endpoint');

    $resource = 'select/' . $requestVO->requestType .
      '/' . $requestVO->entityType .
      '/' . $requestVO->requestLimit .
      '/' . $requestVO->entityLanguage;

    drupal_set_message($resource);

    // maps the request type and entity type to static JSON files
    if($debugMode) {
      drupal_set_message(t('Debug mode active'), 'warning');
      $endpoint = \Drupal::config('rest_import.web_service')->get('debug_endpoint');
      switch($requestVO->requestType . '/' . $requestVO->entityType) {
        case 'unique/licence':
          $resource = 'Licence.json';
          break;
        case 'unique/licence_status':
          $resource = 'LicenceStatus.json';
          break;
        case 'multiple/licence':
          $resource = 'Licence.json';
          break;
        case 'multiple/licence_status':
          $resource = 'LicenceStatus.json';
          break;
      }
    }

    drupal_set_message('Requesting: ' . $endpoint . '/' . $resource);
    // @todo handle multiple formats : json, xml, hal
    $format = \Drupal::config('rest_import.web_service')->get('format');
    drupal_set_message('Format: ' . $format);

    if (!empty($endpoint)) {
      try {
        $request = $this->httpClient->get(
          $endpoint . '/'. $resource,
          ['Accept' => 'application/' . $format]
        );

        if (!empty($request)) {
          // @todo test headers definition
          $headers = $request->getHeaders();
          $body = $request->getBody();
          //kint($headers);
          //kint($body->getMetadata());
          //kint($body->getContents());

          // @todo review + add headers handler
          if ($body->isReadable() && $body->isSeekable()) {
            $response = Json::decode($body);
            // Wrap entities into a entity select value object that also
            // returns the status of the web service call.
            // Enqueue is not prepared here but delegated to the client class
            // it makes sense to decouple these operations for testing.
            //$this->enqueueItems($response['data']);
            $entitySelect->sourceEntities = $response['data'];

            // this could be wrong http://drupal.stackexchange.com/questions/128274/consuming-restful-web-services
            //$this->serializerJson->deserialize($body, 'Drupal\node\Entity\Node', $format);
            //$deserializedJson = $this->serializerJson->deserialize($body->getContents());
            //kint($deserializedJson);
          }
        }
        // @todo implement specificities (e.g. send mail with description of the issue to the person
        // that is responsible of the web service maintenance.
      } catch (ConnectException $e) {
        // web service unavailable
        drupal_set_message(t('Endpoint not available.'), 'error');
        watchdog_exception('rest_import', $e);
      } catch (ClientException $e) {
        // 404, web service endpoint changed
        drupal_set_message(t('Endpoint not reachable (404).'), 'error');
        watchdog_exception('rest_import', $e);
      } catch (\Exception $e) {
        drupal_set_message(t('Endpoint error @error', array('@error' => $e->getMessage())), 'error');
        watchdog_exception('rest_import', $e);
      }
    }else {
      // @todo provide link to config form
      drupal_set_message(t('The endpoint is not defined in admin/config/rest_import/web_service'), 'error');
    }

    return $entitySelect;
  }

  /**
   * {@inheritdoc}
   */
  public function enqueueSourceEntities(EntitySelectVO $entitySelect) {
    if($entitySelect->status == EntitySelectVO::STATUS_READY_TO_IMPORT) {
      if (!empty($entitySelect->sourceEntities)) {
        $queue_factory = \Drupal::service('queue');
        // @todo choose implementation (manual or cron based) later on the real application
        /** @var QueueInterface $queue */
        $queue = $queue_factory->get('manual_entity_process');

        foreach ($entitySelect->sourceEntities as $entity) {
          // @todo pass legacy source id instead of nodes once we work with the real output
          $queue_id = $queue->createItem($entity);
          //drupal_set_message('Queue create item @queue_id', array('@queue_id' => $queue_id));
        }
      }else {
        drupal_set_message(t('No entities found to import.'), 'warning');
      }
    }else{
      drupal_set_message(t('Not ready for import, review logs for more details.'), 'warning');
    }
  }




}
