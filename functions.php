<?php

$vendors = ABSPATH . '/vendor/autoload.php';

require_once $vendors;

$container = new Tmf\Theme\ServiceContainer();

$wordpressTheme = new Tmf\Theme\WordpressTheme($container);





