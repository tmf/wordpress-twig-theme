<?php

namespace Tmf\Theme;

use Pimple;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_SimpleFilter;
use Tmf\Twig\WordpressProxy;

/**
 * Class ServiceContainer
 *
 * A pimple service container
 *
 * @see https://github.com/fabpot/Pimple#packaging-a-container-for-reusability
 * @author Tom Forrer <tom.forrer@gmail.com>
 * @package Tmf\Theme
 */
class ServiceContainer extends Pimple
{
  /**
   * Service container constructor: define the needed services as a package.
   */
  public function __construct()
  {
    // we know where we are in our wordpress-theme (3 levels further down of the theme root)
    $this['theme_path'] = dirname(dirname(dirname(__DIR__)));
    $this['view_path'] = $this['theme_path'] . '/views';
    $this['cache_path'] = $this['theme_path'] . '/cache';
    $this['asset_path'] = $this['theme_path'] . '/resources';

    // provide a twig loader
    $this['twig_loader'] = function ($c) {
      return new Twig_Loader_Filesystem($c['view_path']);
    };

    // provide a twig wordpress proxy object
    $this['wordpress_proxy'] = function ($c) {
      return new WordpressProxy();
    };

    // provide a twig environment with a cache folder and the wordpress proxy
    $this['twig'] = function ($c) {
      $twig = new Twig_Environment($c['twig_loader'], array(
        'cache' => $c['cache_path'],
        'debug' => false
      ));

      $twig->addGlobal('wp', $c['wordpress_proxy']);

      return $twig;
    };
  }
} 