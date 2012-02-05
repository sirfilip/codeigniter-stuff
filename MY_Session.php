<?php  if (! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Session extends CI_Session {
	
	function userdata($item, $default = FALSE)
	{
		return ( ! isset($this->userdata[$item])) ? $default : $this->userdata[$item];
	}
	
}