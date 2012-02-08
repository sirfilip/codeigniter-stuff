<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


class MY_Model extends CI_Model {
	
	protected $_table = NULL;
	
	protected $_primary_key = 'id';

	protected $_per_page = 10;

	protected $_belongs_to = array(); 
	protected $_has_many = array();
	protected $_has_and_belongs_to_many = array();
	protected $_has_one = array();

	protected $_with = array();
	
	protected $_rules = array();
	
	protected $_updated_fields = array();
	
	protected $_attr_accessible = array();
	
	protected $_errors = array();

	function table()
	{
		return $this->_table;
	}

	function pk()
	{
		return $this->{$this->_primary_key};
	}

	function belongs_to()
	{
		return $this->_belongs_to;
	}


	function has_many()
	{
		return $this->_has_many;
	}

	function has_and_belongs_to_many()
	{
		return $this->_has_and_belongs_to_many;
	}

	function has_one()
	{
		return $this->_has_one;
	}

	function all()
	{
		$query = $this->db->get($this->_table);
		if ($query->num_rows() > 0)
		{
			return $this->hydrate($query->result(get_class($this)));
		}
		else 
		{
			return array();
		}
	}

	function get()
	{
		$query = $this->db->limit(1)->get($this->_table);
		if ($query->num_rows() > 0)
		{
			return $this->hydrate($query->result(get_class($this)), TRUE);
		}	
		else 
		{
			return NULL;
		}
	}

	function with($related)
	{
		if (! is_array($related))
		{
			$related = explode(',', $related);
		}
		
		$this->_with = $related;
		return $this;
	}

	function join($with, $on)
	{
		$this->db->join($with, $on);
		return $this;
	}
	
	function or_where($where)
	{
		$this->db->or_where($where);
		return $this;
	}
	
	function where($where)
	{
		$this->find($where);
		return $this;
	}
	
	function select($select)
	{
		$this->db->select($select);
		return $this;
	}
	
	function as_list($property)
	{
		$data = array();
		
		$objects = $this->all();
		
		foreach ($objects as $object)
		{
			$data[$object->pk()] = $object->{$property};
		}
		
		return $data;
	}

	protected function hydrate($results, $get_one = FALSE)
	{
		$this->load->library('hydrator');
		$objects = $this->hydrator->hydrate($results, $this->_with);
		$this->_with = array();
		return $get_one ? $objects[0] : $objects; 
	}
	
	function find($where)
	{
		$this->db->where($where);
		return $this;
	}

	function paginate($offset, $limit = FALSE)
	{
		$limit = $limit ? $limit : $this->_per_page;
		$this->db->limit($limit)->offset($offset);
		return $this;	
	}
	
	function get_object_or_404($where)
	{
		$object = $this->find($where)->get();
		if ($object)
		{
			return $object;
		}
		else 
		{
			show_404();
		}
	}
	
	function find_by_id($id)
	{
		return $this->find(array($this->_primary_key => $id))->get();
	}
	
	function create($props = array())
	{	
		$props = array_merge($this->updated_fields(), $props);
		
		if (empty($props)) return FALSE;
		
		$this->db->insert($this->_table, $props);
		return $this->db->insert_id();
	}
	
	function update($id = NULL, $props = array())
	{
		$props = array_merge($this->updated_fields(), $props);
		if (empty($props)) return FALSE;
		
		$id = is_null($id) ? $this->pk() : $id;
		
		$this->db->where($this->_primary_key, $id)
				 ->update($this->_table, $props);
	}
	
	function delete($id = NULL)
	{
		$id = is_null($id) ? $this->pk() : $id;
		
		$this->db->where($this->_primary_key, $id)
				 ->delete($this->_table);
	}
	
	function count_all()
	{
		return $this->db->count_all($this->_table);
	}
	
	function count($where)
	{
		return $this->db
					->from($this->table())
					->where($where)
					->count_all_results();
	}
	
	function __call($method, $params = array())
	{
		$alias = "_{$method}";
		
		if ($this->{$alias}) return $this->{$alias};
	}

	function is_new_record()
	{
		return ! isset($this->{$this->_primary_key});
	}
	
	function rules($properties = array())
	{
		if ($this->is_new_record()) return $this->_rules;
		
		$rules = array();
		
		foreach ($this->_rules as $rule)
		{
			if (in_array($rule['field'], array_keys($this->_updated_fields)))
			{
				$rules[] = $rule;
			}
		}
		
		return $rules;
	}
	
	function update_fields($properties = array())
	{
		$properties = $this->mass_protect($properties);
		
		foreach ($properties as $property => $value)
		{
			$this->set($property, $value);
		}
	}
	
	function updated_fields()
	{
		return $this->_updated_fields;
	}
	
	function mass_protect($properties)
	{
		$data = array();
		
		foreach ($properties as $prop => $val)
		{
			if (in_array($prop, $this->_attr_accessible)) $data[$prop] = $val;
		}
		
		return $data;
	}
	
	function set($field, $value)
	{
		if ((! isset($this->{$field})) or (isset($this->{$field}) and $this->{$field} !== $value))
		{
			$this->_updated_fields[$field] = $value;
		}
	}
	
	function is_valid($extra_rules = array())
	{
		$rules = array_merge($this->rules(), $extra_rules);
		
		if (empty($rules)) return TRUE;
		
		$this->load->library('form_validation');
		if ($rules) $this->form_validation->set_rules($rules);
		
		$_POST = array_merge($_POST, $this->updated_fields());
		
		if ($this->form_validation->run())
		{
			$this->_errors = array();
			return TRUE;
		}
		else
		{
			$this->_errors = $this->form_validation->errors();
			return FALSE;
		}
	}
	
	function errors()
	{
		return $this->_errors;
	}

}
