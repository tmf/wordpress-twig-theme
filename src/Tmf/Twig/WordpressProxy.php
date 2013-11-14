<?php

namespace Tmf\Twig;

class WordpressProxy
{
  public function __call($function, $arguments) {
    if (!function_exists($function)) {
      trigger_error('call to unexisting function ' . $function, E_USER_ERROR);
      return null;
    }

    ob_start();
    $result = call_user_func_array($function, $arguments);

    if(!ob_get_length()){
      ob_end_clean();
    }else{
      ob_end_flush();
      $result =  '';
    }
    return $result;
  }
} 