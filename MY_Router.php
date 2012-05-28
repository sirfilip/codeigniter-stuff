<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MY_Router allows the existence of controllers and controller sub directories of the same name
 *
 * @author Erik Straub
 */
class MY_Router extends CI_Router {

  function __construct(){
    parent::__construct();
  }

  function _validate_request($segments){

    // Is the controller in a sub-directory?
    if (isset($segments[1]) && $segments[1] != 'index' && is_dir(APPPATH.'controllers/'.$segments[0])){
      $dir = '';
      do{
        if (strlen($dir) > 0)
        {
            $dir .= '/';
        }
        $dir .= $segments[0];
        $segments = array_slice($segments, 1);
      }while($segments && is_dir(APPPATH.'controllers/'.$dir .'/'.$segments[0]));
      // Set the directory and remove it from the segment array
      $this->set_directory($dir);

      if (count($segments) > 0){
        // Does the requested controller exist in the sub-folder?
        if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$segments[0].EXT)){
          show_404($this->fetch_directory().$segments[0]);
        }
      }else{
        $this->set_class($this->default_controller);
        $this->set_method('index');

        // Does the default controller exist in the sub-folder?
        if(!file_exists(APPPATH.'controllers/'.$this->fetch_directory().$this->default_controller.EXT)){
          $this->directory = '';
          return array();
        }
      }
      return $segments;
    }
    
    // Does the requested controller exist in the root folder?
    if (file_exists(APPPATH.'controllers/'.$segments[0].EXT)){
      return $segments;
    }

    // Can't find the requested controller...
    show_404($segments[0]);
  }
}

// END MY_Router class

/* End of file MY_Router.php */
/* Location: ./application/core/MY_Router.php */ 
