<?php  if (! defined('BASEPATH')) exit('No direct script access allowed');


class Base_model {
	
	// db connection to be used
	protected $_db_connection_name = 'default';
	
	// data holding all of the object properties 
	protected $_data = array();
	// updated fields
	protected $_updated_fields = array();
	// errors 
	protected $_errors = array();
	
	// primary key
	protected $_primary_key = 'id';
	// table name 
	protected $_table = '';
	
	// codeigniter instance
	protected $CI = NULL;
	
	// prevents mass assignment atack
	protected $_attr_accessible = array();
	
	// validation rules 
	protected $_rules = array();
	
	
	function __construct()
	{
		$this->CI = get_instance();
	}
	
	function __destruct()
	{
		$this->CI = NULL;
	}
	
	function __get($name) 
	{
		return array_key_exists($name, $this->_data) ? $this->_data[$name] : NULL;
	}
	
	function __set($name, $value) 
	{
		if (array_key_exists($name, $this->_data) and $this->_data[$name] !== $value)
		{
			$this->_updated_fields[$name] = $value;
		}
		$this->_data[$name] = $value;
	}
	
	function __isset($name)
	{
		return array_key_exists($name, $this->_data);
	}
	
	function __unset($name)
	{
		unset($this->_data[$name]);
	}
	
	function as_array()
	{
		return $this->_data;
	}
	
	function as_json()
	{
		return json_encode($this->_data);
	}
	
	function use_connection($connection_name)
	{
		$this->_db_connection_name = $connection_name;
		return $this;
	}
	
	function db()
	{
		return $this->CI->load->database($this->_db_connection_name, TRUE);
	}
	
	function is_new_record()
	{
		return ! array_key_exists($this->_primary_key, $this->_data);
	}
	
	function update_attributes($attributes = array())
	{
		foreach ($this->_attr_accessible as $key => $value)
		{
			if (array_key_exists($key, $attributes)) $this->_data[$key] = $value;
		}
		return $this;
	}
	
	protected function before_save() 
	{
		return TRUE;
	}
	
	protected function after_save() 
	{
		return TRUE;
	}
	
	protected function before_create() 
	{
		return TRUE;
	}
	
	protected function after_create() 
	{
		return TRUE;
	}
	
	protected function before_update() 
	{
		return TRUE;
	}
	
	protected function after_update() 
	{
		return TRUE;
	}
	
	function save()
	{
		if (! $this->before_save()) return FALSE;
		if ($this->is_new_record())
		{
			if (! $this->before_create()) return FALSE;
			$this->create();
			if (! $this->after_create()) return FALSE;
		}
		else
		{
			if (! $this->before_update()) return FALSE;
			$this->update();
			if (! $this->after_update()) return FALSE;
		}
		if (! $this->after_save()) return FALSE;
		return ! $this->has_errors();
	}
	
	function has_errors()
	{
		return ! is_valid();
	}
	
	function is_valid()
	{
		return empty($this->_errors);
	}
	
	function rules()
	{
		if ($this->is_new_record())
		{
			return $this->_rules();
		}
		else
		{
			$rules = array();
			foreach ($this->_rules as $rule)
			{
				if (array_key_exists($rule['field'], $this->_updated_fields))
				{
					array_push($rules, $rule);
				}
			}
			return $rules;
		}
	}
	
	function validate()
	{
		$_POST = $this->is_new_record() ? $this->_data : $_POST = $this->_updated_fields;    
		$this->CI->load->library('form_validation');
		$this->CI->form_validation->set_rules($this->rules());
		if ($this->form_validation->run())
		{
			$this->_errors = array();
			return TRUE;
		}
		else
		{
			$this->_errors = $this->from_validation->errors();
			return FALSE;
		}
	}
	
	function errors()
	{
		return $this->_errors();
	}
	
	protected function create() 
	{
		$data = $this->_data;
		$this->db()->insert($this->_table, $data);
		$this->{$this->_primary_key} = $this->db()->last_insert_id();
	}
	
	protected function update() 
	{
		$data = $this->_updated_fields;
		$this->db()->update($this->_table, $data, array($this->_primary_key, $this->{$this->_primary_key}));
	}
	
	function delete()
	{
		$this->db()->where($this->_primary_key, $this->{$this->_primary_key})
				 ->delete($this->_table);
	}
	
	function all($offset, $limit)
	{
		$query = $this->db()->limit($limit)->offset($offset)->get($this->_table);
		if ($query->num_rows() > 0)
		{
			return $query->result(get_class($this));
		}
		else
		{
			return array();
		}
	}
	
	function find_all($where, $limit = FALSE, $offset = FALSE)
	{
		if ($limit) $this->db()->limit($limit);
		if ($offset) $this->db()->offset($offset);
		
		$query = $this->db()->where($where)->get($this->_table);
		
		if ($query->num_rows() > 0)
		{
			return $query->result(get_class($this));
		}
		else
		{
			return array();
		}
	}
	
	function find($where)
	{
		$query = $this->db()->where($where)->limit(1)->get($this->_table);
		if ($query->num_rows() > 0)
		{
			return $query->row(0, get_class($this));
		}
		else
		{
			return NULL;
		}
	}
	
	function get_object_or_404($where)
	{
		$object = $this->find($where);
		
		if ( ! $object)
		{
			show_404();
		}
		else
		{
			return $object;
		}
	}
	
	function find_by_id($id)
	{
		return $this->find(array($this->_primary_key => $id));
	}
	
	function count_all()
	{
		return $this->db()->count_all($this->_table);
	}
	
	
}
