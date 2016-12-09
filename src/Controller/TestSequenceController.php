<?php

namespace Drupal\rest_import\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\rest_import\SequenceService;

/**
 * Class TestSequenceController.
 *
 * @package Drupal\rest_import\Controller
 */
class TestSequenceController extends ControllerBase {

  /**
   * Drupal\rest_import\SequenceService definition.
   *
   * @var \Drupal\rest_import\SequenceService
   */
  protected $restImportSequence;

  /**
   * {@inheritdoc}
   */
  public function __construct(SequenceService $rest_import_sequence) {
    $this->restImportSequence = $rest_import_sequence;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('rest_import.sequence')
    );
  }

  /**
   * Import.
   *
   * @return string
   */
  public function import() {

    $this->restImportSequence->importSequence();

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Launches the several select in a sequence.')
    ];
  }

}
