<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
    $this->output->enable_profiler(TRUE);
  }

  
  public function _remap($method, $arguments = array())
  {
    $action = "action_{$method}";
    if (method_exists($this, $action))
    {
        return call_user_func_array(array($this, $action), $arguments);
    }
    
    show_404();
  }
  
  function _is_ajax() 
  {
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
  }  


}
