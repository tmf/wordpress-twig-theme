# Wordpress Twig Theme

This theme demonstrates how the Twig templating engine and other reusable, modern components can be used in an
object-oriented way in a wordpress theme.

The actual twig template is based on the free, responsive http://html5up.net/escape-velocity/ template.

Inspired / adapted from

* [Your Guide to Composer in WordPress](http://composer.rarst.net/)
* [Wordpress Twig Theme Integration](https://github.com/wdalmut/wp-twig-theme)

## Installation

This theme requires the composer autoloader: the vendor directory (with the autoload.php entry point) is expected in the
root of the wordpress installation. The easiest way to install:
* get composer (http://getcomposer.org/download/)
* create composer.json with the following contents: https://gist.github.com/tmf/7461046
* run composer install
* visit the wordpress site to create the wp-config.php

## Usage

* adapt the needed views field ($viewsByTemplate) in the Tmf\Theme\WordpressTheme class
* register the needed scripts in Tmf\Theme\WordpressTheme::init
* adapt Tmf\Theme\WordpressTheme::getViewData to the configured views
* edit the twig templates in the views folder