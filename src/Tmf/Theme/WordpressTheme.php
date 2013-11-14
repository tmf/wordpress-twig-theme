<?php
namespace Tmf\Theme;

class WordpressTheme
{
  protected $container;

  public function __construct(ServiceContainer $container)
  {
    $this->container = $container;
    add_action('after_setup_theme', array($this, 'setup'));
    add_action('init', array($this, 'init'));
  }

  public function setup()
  {

    add_filter('index_template', function ($file) { return 'index.html.twig'; });

    add_filter('template_include', array($this, 'filterTemplateInclude'));
  }

  public function init()
  {
    $uri = get_template_directory_uri();

    wp_register_style('skel-noscript', $uri . '/resources/styles/skel-noscript.css', array('style'), $this->version);
    wp_register_style('skel-desktop', $uri . '/resources/styles/skel-desktop.css', array(), $this->version);
    wp_register_style('style', $uri . '/resources/styles/style.css', array(), $this->version);
    wp_register_script('html5shiv', $uri . '/resources/scripts/html5shiv.js', array(), $this->version);
    wp_enqueue_script('jquery-dropotron', $uri . '/resources/scripts/jquery.dropotron.min.js', array('jquery'), $this->version);
    wp_enqueue_script('skel', $uri . '/resources/scripts/skel.min.js', array('jquery', 'skel-config'), $this->version);
    wp_enqueue_script('skel-panels', $uri . '/resources/scripts/skel-panels.min.js', array('jquery', 'skel'), $this->version);
    wp_enqueue_script('skel-config', $uri . '/resources/scripts/config.js', array(), $this->version);
    wp_localize_script('skel-config', 'theme', array('uri' => trailingslashit($uri)));
  }

  public function filterTemplateInclude($file)
  {
    $pathInfo = pathInfo($file);
    $templateName = $pathInfo['filename'] . '.html.twig';
    $template = $this->container['twig']->loadTemplate($templateName);
    echo $template->render(array());
    return false;
  }


} 