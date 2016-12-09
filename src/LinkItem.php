<?php

namespace Drupal\rest_import;


class LinkItem
{
  private $path;
  private $label;
  private $type;
  private $attributes;

  public function __construct($path, $label, $type, $attributes = [])
  {
    $this->path = $path;
    $this->label = $label;
    $this->type = $type;
    $this->attributes = $attributes;
  }

  /**
   * @return mixed
   */
  public function getAttributes()
  {
    return $this->attributes;
  }

  /**
   * @param mixed $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }

  /**
   * @return mixed
   */
  public function getPath()
  {
    return $this->path;
  }

  /**
   * @param mixed $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }

  /**
   * @return mixed
   */
  public function getLabel()
  {
    return $this->label;
  }

  /**
   * @param mixed $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }

  /**
   * @return mixed
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * @param mixed $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }

}