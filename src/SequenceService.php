<?php

namespace Drupal\rest_import;

use Drupal\rest_import\SelectService;
use Drupal\rest_import\ValueObject\EntityRequestVO;

/**
 * Class SequenceService.
 *
 * @package Drupal\rest_import
 */
class SequenceService implements SequenceServiceInterface {

  /**
   * Drupal\rest_import\SelectService definition.
   *
   * @var \Drupal\rest_import\SelectService
   */
  protected $selectService;

  /**
   * Constructor.
   */
  public function __construct(SelectService $rest_import_select) {
    $this->selectService = $rest_import_select;
  }

  public function getSequence() {

    // -------------------------------------------------------------------------
    // 1) Taxonomy terms sequence is not important
    // within the vocabularies (V) because they do not have relation between
    // each other, but they have to be imported first, before the content types.
    // -------------------------------------------------------------------------
    // 1.1) Licence status
    // 1.2) Company activity
    // 1.3) Test method
    // 1.4) Product type
    // 1.5) Application (TBD)
    // 1.6) Document type
    // 1.7) Certification system
    // 1.8) Certification body

    // -------------------------------------------------------------------------
    // 2) Content types (CT), sequence must be respected.
    // -------------------------------------------------------------------------
    // 2.1) Company
    //  - references V : Company activity
    // 2.2) Licence file
    //  - references CT : Company
    //  - references V : Licence status, Certification system, Certification body
    // 2.3) Product
    //  - references CT : Licence file
    //  - references V : Product type, Application
    // 2.4) Document
    // - references V : Document type
    // 2.5) Test
    //  - references CT : Company, Product
    //  - references V: Test method
    // 2.6) Document / Product (TBD)
    // - references CT : Document / Product
    // - references V : Certification body, Certification system

    // @todo move the sequence in YAML file with attributes
    // @see https://learnxinyminutes.com/docs/yaml/
    // each entity that depends on other ones have to wait for their completion
    // a content model definition in YAML should look like


    // source_entity_type: licence
    //  target_entity_type: node
    //  bundle: licence_file
    //  converter_class: LicenceFileConverter
    //  source_dependencies:
    //    - licence_status

    // a first basic implementation looks like
    // a table can keep track of the import sequence completion
    // that will allow to relaunch operation in case of crash
    return [
      'licence_status',
      //'company_activity',
      //'test_method',
      //'product_type',
      //'application',
      //'document_type',
      //'certification_system',
      //'certification_body',
      //'company',
      'licence',
      //'product',
      //'document',
      //'test',
      //'document_product'
    ];

    /*
    return [
      'licence_status',
      'company_activity',
      'test_method',
      'product_type',
      'application',
      'document_type',
      'certification_system',
      'certification_body',
      'company',
      'licence',
      'product',
      'document',
      'test',
      'document_product'
    ];
    */

  }

  /**
   * Imports sequence of entity types.
   */
  public function importSequence() {

    // for each source entity type, call the web service until
    // all records have been imported
    // this should be regarded as another queue (EntityTypeProcessBase)
    $entityRequest = new EntityRequestVO();
    $entityRequest->requestType = EntityRequestVO::REQUEST_TYPE_MULTIPLE;
    $entityRequest->requestLimit = (int) \Drupal::config('rest_import.web_service')->get('items_limit');
    $defaultLanguage = \Drupal::config('rest_import.web_service')->get('default_language');

    foreach($this->getSequence() as $source_entity_type) {
      // @todo call each until we have no data left for this source
      drupal_set_message("Processing entity: " . $source_entity_type);

      $entityRequest->entityType = $source_entity_type;
      // @todo iterate through languages or set default language for a first release
      $entityRequest->entityLanguage = $defaultLanguage;

      // use directly the SelectService OR
      // use EntityTypeProcessBase and call it again after the queue
      // is being processed for each item
      // this structure assumes that the web service handles a log of
      // the entities left to be imported (so when it reaches 0, the loop stops.
      do {
        // @todo deliver items left to import
        // @todo consider using enqueueItems separately, with the result of the select
        $entitySelect = $this->selectService->selectSourceEntities($entityRequest);
        $itemsToImport = count($entitySelect->sourceEntities);
        drupal_set_message('Items to import = ' . $itemsToImport);
      } while($itemsToImport !== 0);

    }

  }


}
