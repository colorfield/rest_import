<?php

namespace Drupal\rest_import;

interface SequenceServiceInterface {
  public function getSequence();
  public function importSequence();
}
