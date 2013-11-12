<?php

namespace Tmf\Theme;

use Pimple;
use Twig_Environment;
use Twig_Loader_Filesystem;

class ServiceContainer extends Pimple
{
  public function __construct()
  {
    $this['theme_path'] = dirname(dirname(dirname(__DIR__)));
    $this['template_path'] = $this['theme_path'] . '/resources/templates';
    $this['cache_path'] = $this['theme_path'] . '/resources/cache';

    $this['twig_loader'] = function ($c) {
      return new Twig_Loader_Filesystem($c['template_path']);
    };

    $this['twig'] = function ($c) {
      return new Twig_Environment($c['twig_loader'], array(
        'cache' => $c['cache_path'],
      ));
    };
  }
} 