<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


class MY_Model extends CI_Model {
	
	protected $_table = NULL;
	
	protected $_primary_key = 'id';

	function __call($method, $params = array())
	{
		if (method_exists($this->db, $method))
		{
			call_user_func_array(array($this->db, $method), $params);
			return $this;
		}
	}
	
	function get()
	{
		$query = $this->db->get($this->_table);
		if ($query->num_rows() > 0)
		{
			return $query->row();
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
			return $query->result();
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
			$data[$object->pk()] = $object->{$property};
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
	
	function get_object_or_404()
	{
		$object = $this->get();
		
		if ($object)
		{
			return $object;
		}
		else
		{
			show_404();
		}
	}
	
	function create($props)
	{
		$this->db->insert($this->_table, $props);
		return $this->db->last_insert_id();
	}
	
	function update($id, $props)
	{
		$this->db->where($this->_primary_key, $id)->update($this->_table, $props);
	}
	
	function delete($id)
	{
		$this->db->where($this->_primary_key, $id)->delete($this->_table);
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
