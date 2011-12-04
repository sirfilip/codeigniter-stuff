<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


class MY_Model extends CI_Model {
	
	protected $table = NULL;
	
	function all($offset, $limit)
	{
		$query = $this->db->limit($limit)->offset($offset)->get($this->table);
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
		
		$query = $this->db->where($where)->get($this->table);
		
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
		$query = $this->db->where($where)->limit(1)->get($this->table);
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
		return $this->find(array('id' => $id));
	}
	
	function create($props)
	{
		$this->db->insert($this->table, $props);
		return $this->db->insert_id();
	}
	
	function update($id, $props)
	{
		$this->db->where('id', $id)
				 ->update($this->table, $props);
	}
	
	function delete($id)
	{
		$this->db->where('id', $id)
				 ->delete($this->table);
	}
	
	function count()
	{
		return $this->db->count_all($this->table);
	}


}
