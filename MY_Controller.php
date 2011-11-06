<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

  protected $layout = 'template';

  public function __construct()
  {
    parent::__construct();
    $this->output->enable_profiler(TRUE);
    $this->load->library('template');
  }
  
  public function before() {}
  
  public function after() {}
  
  public function _remap($method, $arguments = array())
  {
    $action = "action_{$method}";
    if (method_exists($this, $action))
    {
      $this->before();    
      return call_user_func_array(array($this, $action), $arguments);
      $this->after();
    }
    
    show_404();
  }
  
}
