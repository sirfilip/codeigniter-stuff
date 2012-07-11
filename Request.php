<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class responsible for request method detection.
 * 
 * @author Filip Kostovski<filip.kostovski@cosmicdevelopment.com>
 */
class Request {
	
	private $CI;
	const METHOD_HINT = '_method';
	
	function __construct($config = array())
	{
		$this->CI =& get_instance();
	}

	/**
	 * Detects http method.
	 * 
	 * Checks if the method is overriden via hidden input field.
	 * 
	 * @return String
	 */
    function detect_http_method()
    {
        $method = $this->CI->input->post(self::METHOD_HINT) ? $this->CI->input->post(self::METHOD_HINT) : $_SERVER['REQUEST_METHOD'];
        return strtolower($method);
    }
	
	/**
	 * Checks if the http method is the method given.
	 * 
	 * @param String $method
	 * @return bool 
	 */
	function is_http_method($method)
	{
		$method = strtolower($method);
        return $method === $this->detect_http_method();
	}
	
	/**
	 * Checks if the http method is get.
	 * 
	 * @return bool
	 */
	function is_get()
	{
		return $this->is_http_method('get');
	}
	
	/**
	 * Checks if the http method is post.
	 * 
	 * @return bool
	 */
	function is_post()
	{
		return $this->is_http_method('post');
	}
	
	/**
	 * Checks if the http method is put.
	 * 
	 * @return bool
	 */
	function is_put()
	{
		return $this->is_http_method('put');
	}
	
	/**
	 * Checks if the http method is delete.
	 * 
	 * @return bool
	 */
	function is_delete()
	{
		return $this->is_http_method('delete');
	}
	
	/**
	 * Checks if the request is ajax.
	 * 
	 * @return bool
	 */
	function is_ajax()
	{
		return $this->CI->input->is_ajax_request();
	}
	
	/**
	 * Checks if the request is triggered via command line.
	 * 
	 * @return bool
	 */
	function is_cli()
	{
		return $this->CI->input->is_cli_request();
	}
	
}

// eof php
