<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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
    
    function css($filename, $attrs = array())
    {
        $file_path = $this->config['assets_path'].$this->config['styles_folder'].$filename; 
        $file_url  = base_url($this->config['assets_url'].$this->config['styles_folder'].$filename).'?'.filemtime($file_path); 
        $this->styles[] = '<link type="text/css" rel="stylesheet" 
                           href="'.$file_url.'" '.$this->attrs_to_html($attrs).'/>';
        return $this;
    }
    
    function js($filename, $group = 'default')
    {
        $this->scripts[$group] = isset($this->scripts[$group]) ? $this->scripts[$group] : array();
        $file_path = $this->config['assets_path'].$this->config['scripts_folder'].$filename; 
        $file_url  = base_url($this->config['assets_url'].$this->config['scripts_folder'].$filename).'?'.filemtime($file_path);
        $this->scripts[$group][] = '<script type="text/javascript" src="'.$file_url.'"></script>';
        return $this;
    }
    
    function render_styles()
    {
       return implode("\n", $this->styles);
    }
    
    function render_scripts($group = 'default')
    {
        return isset($this->scripts[$group]) ? implode("\n", $this->scripts[$group]) : '';
    }
    
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
