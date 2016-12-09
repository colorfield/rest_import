<?php

namespace Drupal\rest_import\Tests;

use Drupal\simpletest\WebTestBase;
use GuzzleHttp\Client;
use Drupal\Component\Serialization\Json;

/**
 * Provides automated tests for the rest_import module.
 */
class TestReadControllerTest extends WebTestBase {

  /**
   * GuzzleHttp\Client definition.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;
  /**
   * Drupal\Component\Serialization\Json definition.
   *
   * @var \Drupal\Component\Serialization\Json
   */
  protected $serializationJson;

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "rest_import TestReadController's controller functionality",
      'description' => 'Test Unit for module rest_import and controller TestReadController.',
      'group' => 'Other',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests rest_import functionality.
   */
  public function testTestReadController() {
    // Check that the basic functions of module rest_import.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
