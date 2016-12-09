<?php

namespace Drupal\rest_import\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\rest_import\LinkTools;

/**
 * Class TestController.
 *
 * @package Drupal\rest_import\Controller
 */
class TestController extends ControllerBase {

  /**
   * Main.
   *
   * @return string
   *   Return Hello string.
   */
  public function main() {

    $items = array();

    // @todo define dependency sequence to launch

    $items[] = LinkTools::getLinkFromRoute($this->t('Import all sequence'),
      'rest_import.test_sequence_controller_import');

    // Unit tests, per entity
    $selectUniqueParams = ['entity_type' => 'licence', 'entity_id' => '1', 'language' => 'en'];
    $selectMultipleParams = ['entity_type' => 'licence', 'limit' => '10', 'language' => 'en'];
    $items[] = LinkTools::getLinkFromRoute($this->t('Select unique licence entity'),
                                           'rest_import.test_select_unique_controller',
                                           $selectUniqueParams);
    $items[] = LinkTools::getLinkFromRoute($this->t('Select multiple licence entities'),
                                           'rest_import.test_select_multiple_controller',
                                           $selectMultipleParams);

    $selectMultipleParams = ['entity_type' => 'licence_status', 'limit' => '10', 'language' => 'en'];
    $items[] = LinkTools::getLinkFromRoute($this->t('Select multiple licence status entities'),
      'rest_import.test_select_multiple_controller',
      $selectMultipleParams);

    // @todo
    //$items[] = LinkTools::getLinkFromRoute($this->t('Send result'), '');
    $list['links'] = array(
      '#theme' => 'item_list',
      '#items' => $items,
      '#type' => 'ul',
    );

    return [
      '#type' => 'markup',
      '#markup' => render($list),
    ];
  }

}
