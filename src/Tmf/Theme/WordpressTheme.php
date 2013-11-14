<?php
namespace Tmf\Theme;

/**
 * Class WordpressTheme
 *
 * @author Tom Forrer <tom.forrer@gmail.com>
 * @package Tmf\Theme
 */
class WordpressTheme
{
  /**
   * @var ServiceContainer the pimple service container (injected via constructor)
   */
  protected $container;

  /**
   * @var string used for cache-busting
   */
  protected $version;

  /**
   * @var string theme slug, text domain
   */
  protected $slug;

  /**
   * @var array configured views, identified by wordpress template slug as key
   */
  protected $viewsByTemplate;

  /**
   * Theme constructor
   *
   * @param ServiceContainer $container
   */
  public function __construct(ServiceContainer $container)
  {
    // initialize some fields
    $this->container = $container;
    $this->version = 'v1.0';
    $this->slug = 'wordpress-twig-theme';

    $this->viewsByTemplate = array(
      'home' => 'index.html.twig',
      'archive' => 'index.html.twig',
      'search' => 'index.html.twig',
      'single' => 'post/detail.html.twig',
      'page' => 'post/detail.html.twig',
      '404' => '404.html.twig'
    );

    // setup wordpress hooks
    add_action('after_setup_theme', array($this, 'setup'));
    add_action('init', array($this, 'init'));
  }

  /**
   * after_setup_theme action:
   *
   * trick the WPINC/template-loader.php with the configured views
   */
  public function setup()
  {
    // setup "{$slug}_template" filter to return a slug (and not a file)
    foreach ($this->viewsByTemplate as $template => $view) {
      add_filter($template . '_template', function ($file) use ($template) {
          return $template;
        });
    }

    // setup actual view generation by twig
    add_filter('template_include', array($this, 'filterTemplateInclude'));
  }

  /**
   * init action:
   * Wordpress theme initialization with styles, scripts, theme supports, ...
   */
  public function init()
  {
    $uri = get_template_directory_uri();

    // only load the scripts and styles for the frontend
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

      // the http://html5up.net/escape-velocity/ (uses skelJS) is not compatible with the admin-bar bump
      add_theme_support('admin-bar', array('callback' => '__return_false'));
      remove_action('wp_head', 'mp6_override_toolbar_margin', 11);
    }

    // we want also a navigation menu
    register_nav_menu('header-menu', __('Header Menu', $this->textDomain));
  }

  /**
   * template_include filter:
   *
   * override the WPINC/template-loader.php to actually render twig templates and not include a php file
   *
   * @param string $slug the wordpress template slug
   * @return bool false return always false
   */
  public function filterTemplateInclude($slug)
  {
    // get the template engine
    $twig = $this->container['twig'];

    // if a slug is not configured, output something...
    if (!isset($this->viewsByTemplate[$slug])) {
      $slug = '404';
    }
    // fetch the view
    $view = $this->viewsByTemplate[$slug];

    if ($view) {
      // load the twig template from the configured twig loader
      $template = $twig->loadTemplate($view);

      // render the template with the posts/view data
      echo $template->render($this->getViewData($view));
    }

    // override WPINC/template-loader.php to not additionally include something
    return false;
  }

  /**
   * generate view data from wordpress, depending on view file
   *
   * @param $view the twig view file
   * @return array prepared view data
   */
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

  /**
   * get the posts from the wp_query
   *
   * @return array posts array
   */
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