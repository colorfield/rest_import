<?php

namespace Drupal\rest_import\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the rest_import module.
 */
class SequenceControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "rest_import SequenceController's controller functionality",
      'description' => 'Test Unit for module rest_import and controller SequenceController.',
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
  public function testSequenceController() {
    // Check that the basic functions of module rest_import.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
