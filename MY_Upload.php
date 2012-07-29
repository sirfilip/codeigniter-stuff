<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class MY_Upload extends CI_Upload {
	
	function errors()
	{
		return $this->error_msg;
	}
	
	function has_uploaded_file($file)
	{
		return $_FILES[$file]['error'] !== UPLOAD_ERR_NO_FILE;
	}
	
}
