<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Request {
	
	private $CI;
	const METHOD_HINT = '_method';
	
	function __construct($config = array())
	{
		$this->CI =& get_instance();
	}
	
	function is_http_method($method)
	{
		$method = strtoupper($method);
		$method_hint = $this->CI->input->post(self::METHOD_HINT); 
		if ($method_hint)
		{
			return strtoupper($method_hint) === $method; 
		}
		else
		{
			return strtoupper($_SERVER['REQUEST_METHOD']) === $method;
		}
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
