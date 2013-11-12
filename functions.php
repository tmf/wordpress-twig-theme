<?php

foreach (array(
           __DIR__ . '/../../../vendor/',
           __DIR__ . '/../../vendor/',
           __DIR__ . '/vendor/'
         ) as $vendorPath) {
  if (is_dir(realpath($vendorPath))) {
    require_once $vendorPath . 'autoload.php';

    $container = new Tmf\Theme\ServiceContainer();

    $wordpressTheme = new Tmf\Theme\WordpressTheme($container);
    break;
  }
}



