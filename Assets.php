<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class responsible for assets (css and js) management.
 * 
 * @Author Filip Kostovski<filip.kostovski@cosmicdevelopment.com>
 */
class Assets {
    
    protected $styles = array();
    protected $scripts = array();
    protected $config = array(
        'assets_path'    => './assets/',
        'assets_url'     => 'assets/',
        'scripts_folder' => 'js/',
        'styles_folder'  => 'css/',
    );
    
    function __construct($config = array())
    {
        get_instance()->load->helper('url');
        $this->initialize($config);
    }
    
    function initialize($config)
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }
    
	/**
	 * Adds css file relative to the styles folder.
	 * 
	 * @param String $filename relative path to the styles_folder
	 * @param array $attrs additional params like media etc.
	 * @return object self to enable method chaining.
	 */
    function css($filename, $attrs = array())
    {
        $file_path = $this->config['assets_path'].$this->config['styles_folder'].$filename; 
        $file_url  = base_url($this->config['assets_url'].$this->config['styles_folder'].$filename).'?'.filemtime($file_path); 
        $this->styles[] = '<link type="text/css" rel="stylesheet" 
                           href="'.$file_url.'" '.$this->attrs_to_html($attrs).'/>';
        return $this;
    }
    
	/**
	 * Adds js file relative to the scripts_folder.
	 * 
	 * @param String $filename relative path to the scripts folder
	 * @param String $group name of the group to be included defaults to default
	 * @return object self to enable method chaining
	 */
    function js($filename, $group = 'default')
    {
        $this->scripts[$group] = isset($this->scripts[$group]) ? $this->scripts[$group] : array();
        $file_path = $this->config['assets_path'].$this->config['scripts_folder'].$filename; 
        $file_url  = base_url($this->config['assets_url'].$this->config['scripts_folder'].$filename).'?'.filemtime($file_path);
        $this->scripts[$group][] = '<script type="text/javascript" src="'.$file_url.'"></script>';
        return $this;
    }
    
	/**
	 * Renders all css files included to their propper html representation.
	 * 
	 * @return String
	 */
    function render_styles()
    {
       return implode("\n", $this->styles);
    }
    
	/**
	 * Renders all javascript files included to their propper html representation.
	 * 
	 * @return String
	 */
    function render_scripts($group = 'default')
    {
        return isset($this->scripts[$group]) ? implode("\n", $this->scripts[$group]) : '';
    }
    
	/**
	 * Performs conversion from array to html attribute representation.
	 * 
	 * @param array @attrs
	 * @return String
	 */
    private function attrs_to_html($attrs) 
    {
        $data = array();
        foreach ($attrs as $key => $val)
        {
            $data[] = $key.'="'.$val.'"';
        }
        
        return implode(' ', $data);
    }
}
