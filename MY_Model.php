<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


class MY_Model extends CI_Model {
	
	protected $_table = NULL;
	
	protected $_primary_key = 'id';
	
	function all($offset, $limit)
	{
		$query = $this->db->limit($limit)->offset($offset)->get($this->_table);
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
		if ($limit) $this->db->limit($limit);
		if ($offset) $this->db->offset($offset);
		
		$query = $this->db->where($where)->get($this->_table);
		
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
		$query = $this->db->where($where)->limit(1)->get($this->_table);
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
	
	function create($props)
	{
		$this->db->insert($this->_table, $props);
		return $this->db->insert_id();
	}
	
	function update($id, $props)
	{
		$this->db->where($this->_primary_key, $id)
				 ->update($this->_table, $props);
	}
	
	function delete($id)
	{
		$this->db->where($this->_primary_key, $id)
				 ->delete($this->_table);
	}
	
	function count()
	{
		return $this->db->count_all($this->_table);
	}


}
