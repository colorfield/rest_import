<?php
namespace Drupal\rest_import\Controller;

use Drupal\rest_import\ValueObject\EntityRequestVO;
use Drupal\rest_import\SelectService;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TestSelectController
 * Used for development purpose.
 * @todo port unit test.
 */
class TestSelectController extends ControllerBase {

  /**
   * Drupal\rest_import\SelectService definition.
   *
   * @var \Drupal\rest_import\SelectService
   */
  protected $selectService;

  /**
   * {@inheritdoc}
   */
  public function __construct(SelectService $select_service) {
    $this->selectService = $select_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('rest_import.select')
    );
  }

  /**
   * Selects a unique distant entity, for testing purpose only.
   *
   * @param $type
   * @param $id
   * @param null $language
   * @return array
   */
  public function selectUnique($entity_type, $entity_id, $language = null) {

    $entityRequest = new EntityRequestVO();
    $entityRequest->requestType = EntityRequestVO::REQUEST_TYPE_UNIQUE;
    $entityRequest->entityType = $entity_type;
    $entityRequest->entityId = $entity_id;
    $entityRequest->entityLanguage = $language;

    $entitySelectVO = $this->selectService->selectSourceEntities($entityRequest);
    $this->selectService->enqueueSourceEntities($entitySelectVO);

    $output = '';
    $output .= $this->t('Select unique entity with parameters: $entity_type = @etype, $entity_id = @eid, $language (optional) = @lang',
      ['@etype' => $entity_type, '@eid' => $entity_id, '@lang' => $language]);
    $output .= '. ' . $this->selectService->getManualQueueLink();

    return [
      '#type' => 'markup',
      '#markup' => $output,
    ];
  }

  /**
   * Selects multiple distant entities, for testing purpose only.
   *
   * @param $type
   * @param $id
   * @param null $language
   * @return array
   */
  public function selectMultiple($entity_type, $limit, $language = null) {

    $entityRequest = new EntityRequestVO();
    $entityRequest->requestType = EntityRequestVO::REQUEST_TYPE_MULTIPLE;
    $entityRequest->entityType = $entity_type;
    $entityRequest->requestLimit = $limit;
    $entityRequest->entityLanguage = $language;

    $entitySelectVO = $this->selectService->selectSourceEntities($entityRequest);
    $this->selectService->enqueueSourceEntities($entitySelectVO);

    $output = '';
    $output .= $this->t('Select multiple entities with parameters: $entity_type = @etype, $limit = @limit, $language (optional) = @lang',
      ['@etype' => $entity_type, '@limit' => $limit, '@lang' => $language]);
    $output .= '. ' . $this->selectService->getManualQueueLink();

    return [
      '#type' => 'markup',
      '#markup' => $output,
    ];
  }
}