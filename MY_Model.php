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
        if (method_exists($this->db, $method))
        {
            call_user_func_array(array($this->db, $method), $params);
        }

        return $this;
	}

	function is_new_record()
	{
		return ! isset($this->{$this->_primary_key});
	}

}
