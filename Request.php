<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Request {
	
	private $CI;
	const METHOD_HINT = '_method';
	
	function __construct($config = array())
	{
		$this->CI =& get_instance();
	}

    function detect_http_method()
    {
        $method = $this->CI->input->post(self::METHOD_HINT) ? $this->CI->input->post(self::METHOD_HINT) : $_SERVER['REQUEST_METHOD'];
        return strtolower($method);
    }
	
	function is_http_method($method)
	{
		$method = strtolower($method);
        return $method === $this->detect_http_method();
	}
	
	function is_get()
	{
		return $this->is_http_method('get');
	}
	
	function is_post()
	{
		return $this->is_http_method('post');
	}
	
	function is_put()
	{
		return $this->is_http_method('put');
	}
	
	function is_delete()
	{
		return $this->is_http_method('delete');
	}
	
	function is_ajax()
	{
		return $this->CI->input->is_ajax_request();
	}
	
	function is_cli()
	{
		return $this->CI->input->is_cli_request();
	}
	
}

// eof php
