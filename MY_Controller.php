<?php

class MY_Controller extends CI_Controller {
	
	function __construct()
	{
        parent::__construct();
        $this->load->library('request');
		$this->load->spark('template/1.9.0');
		$this->template->title('My Site')
				->set_layout('default');
	}
	
	function _remap($method, $params = array())
	{
        $http_method = $this->request->detect_http_method();
        $action = "{$http_method}_$method";
		if (method_exists($this, $action))
		{
			return call_user_func_array(array($this, $action), $params);
		}
		else
		{
			show_404();
		}
	}
	
}
