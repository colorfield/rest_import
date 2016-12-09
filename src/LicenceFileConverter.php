<?php
namespace Drupal\rest_import;

use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Entity\Node;

class LicenceFileConverter implements EntityConverterInterface {
  public function createProperties($rest_entity) {
    $properties = [];

    // plain values
    // @todo this should be moved in the EntityCreator class by default
    // this should only be used here for override only
    $properties['title'] = $rest_entity['attributes']['name'];

    // @todo debug dates
    $properties['field_lic_first_release_date'][0]['value'] =
      date('Y-m-d', strtotime($rest_entity['attributes']['date_first_release']));
    $properties['field_lic_current_release_date'][0]['value'] =
      date('Y-m-d', strtotime($rest_entity['attributes']['date_current_release']));

    $properties['field_lic_number'][0]['value'] = $rest_entity['attributes']['number'];
    $properties['field_lic_file_name'][0]['value'] = $rest_entity['attributes']['file_name'];


    // @todo for all references, add helpers that allows to check if they exists
    // in source and in target => handle unmet dependencies errors

    // term references
    $sourceStatusId = $rest_entity['relationships']['licence_status']['data']['id'];
    $sourceStatusType = $rest_entity['relationships']['licence_status']['data']['type'];
    $entityMapper = new EntityMapper($sourceStatusType);
    $targetStatusId = $entityMapper->getTargetEntityId($sourceStatusId);
    $properties['field_lic_status'] = [$targetStatusId];

    $sourceBodyId = $rest_entity['relationships']['certification_body']['data']['id'];
    $sourceBodyType = $rest_entity['relationships']['certification_body']['data']['type'];
    $entityMapper = new EntityMapper($sourceBodyType);
    $targetBodyId = $entityMapper->getTargetEntityId($sourceBodyId);
    $properties['field_certification_body'] = [$sourceBodyId]; // @todo wait for other deps
    //$properties['field_certification_body'] = [$targetBodyId];

    $sourceSystemId = $rest_entity['relationships']['certification_system']['data']['id'];
    $sourceSystemType = $rest_entity['relationships']['certification_system']['data']['type'];
    $entityMapper = new EntityMapper($sourceSystemType);
    $targetSystemId = $entityMapper->getTargetEntityId($sourceSystemId);
    $properties['field_certification_system'] = [$sourceSystemId]; // @todo wait for other deps
    //$properties['field_certification_system'] = [$targetSystemId];

    // products should reference licences if we want solr to make a faceted search
    // on product types that displays product and licence
    // node references
    //$sourceProduct = $rest_entity['relationships']['product']['data']['id'];
    //$targetProduct = 13;  // @todo fetch from map table
    //$properties['field_product'] = [(int) $targetProduct];

    return $properties;
  }

  public function updateProperties(EntityInterface &$node, $rest_entity) {
    // @todo implement
    // define if some of all the properties should be passed
    drupal_set_message('updating properties');
    if($node instanceof Node) {
      drupal_set_message('is a node');
      //$node->setTitle("TEST UPDATE");
    }
    //kint($node);
  }

  public function translateProperties(EntityInterface &$node, $rest_entity) {
    // @todo implement
  }
}