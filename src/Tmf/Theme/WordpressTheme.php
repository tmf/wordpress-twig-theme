<?php
namespace Tmf\Theme;

class WordpressTheme
{
  protected $container;
  protected $version;
  protected $slug;
  protected $viewsByTemplate;

  public function __construct(ServiceContainer $container)
  {
    $this->container = $container;
    $this->version = 'v1.0';
    $this->slug = 'wordpress-twig-theme';

    $this->viewsByTemplate = array(
      'home' => 'index.html.twig',
      'archive' => 'index.html.twig',
      'search' => 'index.html.twig',
      'single' => 'post/detail.html.twig',
      '404' => '404.html.twig'
    );

    add_action('after_setup_theme', array($this, 'setup'));
    add_action('init', array($this, 'init'));
  }

  public function setup()
  {

    foreach ($this->viewsByTemplate as $template => $view) {
      add_filter($template . '_template', function ($file) use ($template) {
          return $template;
        });
    }


    add_filter('template_include', array($this, 'filterTemplateInclude'));
  }

  public function init()
  {
    $uri = get_template_directory_uri();

    if (!is_admin()) {
      wp_register_style('skel-noscript', $uri . '/resources/styles/skel-noscript.css', array('style'), $this->version);
      wp_register_style('skel-desktop', $uri . '/resources/styles/skel-desktop.css', array(), $this->version);
      wp_register_style('style', $uri . '/resources/styles/style.css', array(), $this->version);
      wp_register_script('html5shiv', $uri . '/resources/scripts/html5shiv.js', array(), $this->version);
      wp_enqueue_script('jquery-dropotron', $uri . '/resources/scripts/jquery.dropotron.min.js', array('jquery'), $this->version);
      wp_enqueue_script('skel', $uri . '/resources/scripts/skel.min.js', array('jquery', 'skel-config'), $this->version);
      wp_enqueue_script('skel-panels', $uri . '/resources/scripts/skel-panels.min.js', array('jquery', 'skel'), $this->version);
      wp_enqueue_script('skel-config', $uri . '/resources/scripts/config.js', array(), $this->version);
      wp_localize_script('skel-config', 'theme', array('uri' => trailingslashit($uri)));

      add_theme_support('admin-bar', array('callback' => '__return_false'));
      remove_action('wp_head', 'mp6_override_toolbar_margin', 11);
    }

    register_nav_menu('header-menu', __('Header Menu', $this->textDomain));


  }

  public function filterTemplateInclude($slug)
  {
    $twig = $this->container['twig'];
    if (!isset($this->viewsByTemplate[$slug])) {
      $slug = '404';
    }
    $view = $this->viewsByTemplate[$slug];

    if ($view) {
      $template = $twig->loadTemplate($view);
      echo $template->render($this->getViewData($view));
    }

    return false;
  }

  protected function getViewData($view)
  {
    $data = array();
    switch ($view) {
      case $this->viewsByTemplate['home']:
      case $this->viewsByTemplate['archive']:
      case $this->viewsByTemplate['search']:
        $data['posts'] = $this->preparePosts();
        break;
      case $this->viewsByTemplate['single']:
        $postId = get_the_ID();
        $post = get_post($postId);
        $post->comments = get_comments(array('post_id' => $postId, 'status' => 'approved'));
        $data['post'] = $post;
        break;
    }

    return $data;
  }

  protected function preparePosts()
  {
    $posts = array();


    while (have_posts()) {
      the_post();
      $post['post_title'] = get_the_title();
      $post['ID'] = get_the_ID();
      $post['permalink'] = get_permalink();
      $post['post_content'] = get_the_content();
      $post['post_excerpt'] = get_the_excerpt();
      $post['comment_count'] = get_comments_number();
      $post['post_date'] = get_the_time('F jS, Y');
      $posts[] = $post;
    }
    return $posts;
  }


} 