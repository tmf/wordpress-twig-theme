<?php

namespace Tmf\Twig;

/**
 * Class WordpressProxy
 *
 * This class acts as proxy for wordpress functions (or any function) accessible through a global twig object.
 *
 * Inspired/adapted from https://github.com/wdalmut/wp-twig-theme/blob/master/exts/TwigProxy.php .
 * Depending on the behaviour of the called function, an empty string will be returned if the functions output something
 * with echo/printf, in order to override the {{ object.function() }} twig expression evaluation.
 *
 * @author Tom Forrer <tom.forrer@gmail.com>
 * @package Tmf\Twig
 */
class WordpressProxy
{
  /**
   * Call a non-existent method on the instance of this class:
   * act as a proxy to the function residing in the global namespace.
   *
   * @param string $function the function name
   * @param mixed $arguments function arguments
   * @return mixed|string if the function outputs something, return empty string, otherwise return the function result
   */
  public function __call($function, $arguments)
  {
    if (!function_exists($function)) {
      trigger_error('call to unexisting function ' . $function, E_USER_ERROR);
      $result = '';
    } else {
      // start output buffering
      ob_start();
      $result = call_user_func_array($function, $arguments);

      // check if the function output something
      if (!ob_get_length()) {
        ob_end_clean();
      } else {
        ob_end_flush();

        // return empty string to be evaluated by the twig expression
        $result = '';
      }
    }
    return $result;
  }
} 