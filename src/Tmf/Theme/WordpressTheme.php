<?php
namespace Tmf\Theme;

use Pimple;

class WordpressTheme
{
  public function __construct(ServiceContainer $container)
  {
    add_filter('index_template', array($this, 'redirectWordpressTemplateParts'));
  }
  public function redirectWordpressTemplateParts($part){
    return false;
  }
} 