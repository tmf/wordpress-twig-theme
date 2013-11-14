<?php
// the vendor directory is in the root of the wordpress site
$vendors = ABSPATH . '/vendor/autoload.php';

// the composer has registered our namespace ('Tmf') to the src directory.
require_once $vendors;

// load the service container (containing i.e. the twig templating engine)
$container = new Tmf\Theme\ServiceContainer();

// the actual theme
$wordpressTheme = new Tmf\Theme\WordpressTheme($container);





