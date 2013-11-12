<?php
namespace Tmf\Theme;

use ServiceContainer;

class WordpressTheme
{
  public function __construct(ServiceContainer $container)
  {
    add_action('after_setup_theme', array($this, 'setupTheme'));
  }

  public function setupTheme()
  {
    $templateTypes = array('index', 'date', '404', 'search', 'front', 'front_page', 'home', 'archive', 'author', 'category', 'tag', 'taxonomy', 'page', 'paged', 'single', 'attachment', 'comments_popup');
    foreach ($templateTypes as $type) {
      add_filter($type . '_template', array($this, 'registerTemplate'));
    }

    add_filter('template_include', array($this, 'filterTemplateInclude'));
  }

  public function filterTemplateInclude($file)
  {
    return false;
  }

  public function registerTemplate($template)
  {
    return $template;
  }
} 