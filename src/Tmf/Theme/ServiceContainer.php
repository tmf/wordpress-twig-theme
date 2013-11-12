<?php

namespace Tmf\Theme;

use Pimple;
use Twig_Environment;
use Twig_Loader_Filesystem;

class ServiceContainer extends Pimple
{
  public function __construct()
  {
    $this['theme_path'] = __DIR__;
    $this['template_path'] = __DIR__ . '/resources/templates';
    $this['cache_path'] = __DIR__ . '/resources/cache';

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