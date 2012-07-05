<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* Class that acts as DAO (data access object).
* @author Filip Kostovski <filip.kostovski@cosmicdevelopment.com>
*/
class MY_Model extends CI_Model {
	
	protected $_table = NULL;
	
	protected $_primary_key = 'id';
	
	protected $_dto = 'stdClass';
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		// $this->db->save_queries = FALSE;
	}

	/**
	* Delegates all method calls to db if exists.
	*/ 
	function __call($method, $params = array())
	{
		if (method_exists($this->db, $method))
		{
			call_user_func_array(array($this->db, $method), $params);
			return $this;
		}
	}
	
	/**
	* Acts as getter and setter of the database table.
	*/
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
	
	/**
	* Acts as getter and setter of the DTO (data transfer object).
	*
	* DTO is the object used in generating query results by default it is std
	* class object.
	*/
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
	
	/**
	* Returns the primary key of a table
	*/
	function pk()
	{
		return $this->_primary_key;
	}
	
	/**
	* Fetches a single record as an instance of the dto setting.
	*/
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
	
	/**
	* Fetches all records loaded from the db.
	*/
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
	
	/**
	* Loads all records an returns an associative array with primary keys as key.
	*/
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
	
	/**
	* Shorthand for finding an object by id.
	*/
	function find_by_id($id)
	{
		return $this->where(array('id' => $id))->get();
	}
	
	/**
	* Alias for where.
	*/
	function find($where)
	{
		$this->db->where($where);
		return $this;
	}
	
	/**
	* Fetches an object or raises 404.
	*/
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
	
	/**
	* Creates new record in the databse.
	*/
	function create($props)
	{
		$this->db->insert($this->_table, $props);
		return $this->db->insert_id();
	}
	
	/**
	* Updates existing record in the databse.
	*/
	function update($id, $props)
	{
		$this->db->where($this->pk(), $id)->update($this->_table, $props);
		return $this->db->affected_rows();
	}
	
	/**
	* Deletes a record based on it's primary key.
	*/
	function delete($id)
	{
		$this->db->where($this->pk(), $id)->delete($this->_table);
	}
	
	/**
	* Performs pagination.
	*/
	function paginate($offset, $limit)
	{
		$this->db->offset($offset)->limit($limit);
		return $this;
	}
	
	/**
	* Count all records loaded by the db.
	*/
	function count()
	{
		return $this->db->count_all_results($this->_table);
	}
	
	/**
	* Counts all records inside a table.
	*/
	function count_all()
	{
		return $this->db->count_all($this->_table);
	}

}
