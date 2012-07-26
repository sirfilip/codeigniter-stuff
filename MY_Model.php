<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


class MY_Model extends CI_Model {
	
	protected $_table = NULL;
	
	protected $_primary_key = 'id';
	
	protected $_dto = 'Dto';
    
    protected $_attr_accessible = array();
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		// $this->db->save_queries = FALSE;
	}

	function __call($method, $params = array())
	{
		if (method_exists($this->db, $method))
		{
			call_user_func_array(array($this->db, $method), $params);
			return $this;
		}
	}
    
    function build($properties = array())
    {
        $data = array();
        foreach ($this->_attr_accessible as $key)
        {
            $data[$key] = $properties[$key];
        }
        
        $object = new $this->dto();
        foreach ($data as $property => $value)
        {
            $object->{$property} = $value;
        }
        return $object;
    }
	
	function table($table = NULL)
	{
		if (is_null($table))
		{
			return $this->_table;
		}
		else
		{
			$this->_table = $table;
			return $this;
		}
	}
	
	function dto($class = NULL)
	{
		if (is_null($class))
		{
			return $this->_dto;
		}
		else
		{
			$this->_dto = $class;
			return $this;
		}
	}
	
	function pk()
	{
		return $this->_primary_key;
	}
	
	function get()
	{
		$query = $this->db->get($this->_table);
		if ($query->num_rows() > 0)
		{
			return $query->row(0, $this->dto());
		}
		else
		{
			return NULL;
		}
	}
	
	function all()
	{
		$query = $this->db->get($this->_table);
		
		if ($query->num_rows() > 0)
		{
			return $query->result($this->dto());
		}
		else
		{
			return array();
		}
	}
	
	function as_list($property)
	{
		$data = array();
		$objects = $this->all();
		
		foreach ($objects as $object)
		{
			$data[$object->id] = $object->{$property};
		}
		
		return $data;
	}
	
	function find_by_id($id)
	{
		return $this->where(array('id' => $id))->get();
	}
	
	function find($where)
	{
		$this->db->where($where);
		return $this;
	}
	
	function get_object_or_404($where)
	{
		$object = $this->where($where)->get();
		
		if ($object)
		{
			return $object;
		}
		else
		{
			show_404();
		}
	}
    
    function save($object)
    {
        if ($object->is_new_record())
        {
            return $this->create($object);
        }
        else
        {
            return $this->update($object);
        }
    }
	
	function create($object)
	{
		$this->db->insert($this->_table, $object->data());
		$object->id = $this->db->insert_id();
        return $object;
	}
	
	function update($object)
	{
		$this->db->where($this->pk(), $object->id)->update($this->_table, $object->data());
		return $this->db->affected_rows();
	}
	
	function delete($id)
	{
		$this->db->where($this->pk(), $id)->delete($this->_table);
	}
	
	function paginate($offset, $limit)
	{
		$this->db->offset($offset)->limit($limit);
		return $this;
	}
	
	function count()
	{
		return $this->db->count_all_results($this->_table);
	}
	
	function count_all()
	{
		return $this->db->count_all($this->_table);
	}

}

class Dto {
    
    public $id;
    public $_data = array();
    
    public function __get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : NULL;
    }
    
    public function __set($key, $val)
    {
        $this->_data[$key] = $val;
    }
    
    public function __isset($prop) 
    {
        return isset($this->_data[$prop]);
    }
    
    public function data($data = NULL)
    {
        if (is_null($data))
        {
            return $this->_data;
        }
        
        $this->_data = $data;
        return $this;
    }
    
    public function is_new_record()
    {
        return is_null($this->id);
    }
    
}
