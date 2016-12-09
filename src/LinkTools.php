<?php

namespace Drupal\rest_import;

use Drupal\Core\Link;
use Drupal\Core\Url;

class LinkTools
{

  const LINK_TYPE_NID      = 0;
  const LINK_TYPE_ROUTE    = 1;
  const LINK_TYPE_INTERNAL = 2;
  const LINK_TYPE_EXTERNAL = 3;

  public static function createLink(LinkItem $link)
  {
    $result = null;
    // @todo to complete + refactoring on whole module
    // @todo convert in factory then remove static
    switch ($link->getType()) {
      case self::LINK_TYPE_NID:
        // @todo
        break;
      case self::LINK_TYPE_ROUTE:
        // @todo
        break;
      case self::LINK_TYPE_INTERNAL:
        // @todo handle optional attributes
        $result = self::getLinkFromInternal($link->getLabel(), $link->getPath(), $link->getAttributes());
        break;
      case self::LINK_TYPE_EXTERNAL:
        // @todo handle optional attributes
        $result = self::getLinkFromExternal($link->getLabel(), $link->getPath(), $link->getAttributes());
        break;
    }
    return $result;
  }

  // @todo refactoring
  // @todo handle attributes
  // @todo review https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Utility!LinkGenerator.php/function/LinkGenerator%3A%3Agenerate/8.2.x

  public static function getLinkFromNodeId($label, $nid, $attributes = [])
  {
    $path = '/node/' . $nid; // prefixed with /
    $url = Url::fromUri('internal:'.$path);
    // @todo replace with $url = Url::fromRoute('entity.node.canonical', ['node' => $fallbackNodeId]);
    $link = Link::fromTextAndUrl($label, $url);
    $link = $link->toRenderable();
    //$link['#attributes'] = array('class' => array('internal'));
    $link['#attributes'] = $attributes;
    $output = render($link);
    return $output;
  }

  public static function getLinkFromInternal($label, $path, $attributes = [])
  {
    $url = Url::fromUri('internal:'.$path);
    $link = Link::fromTextAndUrl($label, $url);
    $link = $link->toRenderable();
    //$link['#attributes'] = array('class' => array('internal'));
    $link['#attributes'] = $attributes;
    $output = render($link);
    return $output;
  }

  public static function getLinkFromRoute($label, $routeName, $routeParameters = [], $attributes = [])
  {
    $url = Url::fromRoute($routeName, $routeParameters); // a route provided in .routing.yml
    $link = Link::fromTextAndUrl($label, $url);
    $link = $link->toRenderable();
    //$link['#attributes'] = array('class' => array('internal'));
    $link['#attributes'] = $attributes;
    $output = render($link);
    return $output;
  }

  public static function getLinkFromExternal($label, $url, $attributes = [])
  {
    $url = Url::fromUri($url);
    $link = Link::fromTextAndUrl($label, $url);
    $link = $link->toRenderable();
    //$link['#attributes'] = array('class' => array('external'));
    $link['#attributes'] = $attributes;
    $output = render($link);
    return $output;
  }

}