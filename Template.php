<?php 

class Template {
	private $layout = 'template';
	private $data = array();
	private $view = NULL;
	
	public function __construct()
	{
		$this->CI =& get_instance();
	}

	public function use_layout($layout)
	{
		$this->layout = $layout;
	}

	public function __set($prop, $value) 
	{
		$this->data[$prop] = $value;
	}

	public function render($view = NULL, $data = array())
	{
		if ($view !== NULL)
		{
			$this->view = $view;
		}

		$this->data['content'] = $this->CI->load->view($this->view, $data, TRUE);
		$this->CI->load->view($this->layout, $this->data);
	}

}
