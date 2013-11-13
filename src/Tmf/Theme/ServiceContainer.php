<?php

namespace Tmf\Theme;

use Pimple;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_SimpleFilter;
use Tmf\Twig\WordpressProxy;

class ServiceContainer extends Pimple
{
  public function __construct()
  {
    $this['theme_path'] = dirname(dirname(dirname(__DIR__)));
    $this['view_path'] = $this['theme_path'] . '/views';
    $this['cache_path'] = $this['theme_path'] . '/cache';
    $this['asset_path'] = $this['theme_path'] . '/resources';

    $this['twig_loader'] = function ($c) {
      return new Twig_Loader_Filesystem($c['view_path']);
    };

    $this['wordpress_proxy'] = function ($c) {
      return new WordpressProxy();
    };

    $this['twig'] = function ($c) {
      $twig = new Twig_Environment($c['twig_loader'], array(
        'cache' => $c['cache_path'],
        'debug' => true
      ));

      $twig->addGlobal('wp', $c['wordpress_proxy']);

      $emptyStringFilter = new Twig_SimpleFilter('es', function ($something) {
        return '';
      });
      $twig->addFilter($emptyStringFilter);
      return $twig;
    };
  }
} 