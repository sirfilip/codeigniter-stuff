<?php  if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Screenshoter 
* @author Filip Kostovski
* 
* Captures screenshot for a site url and stores it
* on the file system.
*/
class Screenshooter {
	const STATUS_SUCCESS = 0;

	private $_config = array();
	private $_screenshot = null;
	private $_errors = array();

	function __construct($config = array())
	{
		$this->_config = $config;
	}

	/**
	* Makes screenhot for a site url sent.
	* 
	* Creates screenshot for a url using wkhtmltoimage 
	* stored inside applications bin folder.
	*
	* @param String $url the url of the site to capture.
	* @param String $out name and location of the screenshot.
	* @return boolean true on success false on error.
	*/
	function make_scrrenshot_for($url, $out = 'out.jpeg')
	{
		$result = system(APPPATH.'bin/wkhtmltoimage '.$url.' '.$out, $return_var);

		if ($return_var == self::STATUS_SUCCESS)
		{
			$this->screenshot($out); 
			return TRUE;
		}
		else
		{
			$this->_errors[] = $result;
			return FALSE;
		}
	}

	/**
	* Acts as a getter and setter for the screenshot.
	*
	* If called as $this->screenshot() it returns the screenshot location
	* stored on the system.
	* If called as $this->screenshot($location) stores the location of the screenshot.
	*
	* @param $screenshot mixed if null acts as getter else acts as setter.
	* @return mixed $this object when used as setter and String path when called as getter.
	*/
	function screenshot($screenshot = NULL)
	{
		if (is_null($screenshot))
		{
			$this->_screenshot = $screenshot;
			return $this;
		}

		return $this->_screenshot;
	}

	/**
	* Returns errors that happened during the screenshot capture phase.
	*
	* @return array the array of errors.
	*/
	function errors()
	{
		return $this->_errors;
	}

}