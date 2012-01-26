<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Hydrator {

	function hydrate($objects, $with)
	{
		get_instance()->load->database();

		$sample = $objects[0];

		foreach ($with as $related)
		{
			$related = trim($related); // remove extra white space if present

			if (array_key_exists($related, $sample->belongs_to()))
			{
				$belongs_to = $sample->belongs_to(); 
				$objects = $this->hydrate_belongs_to($related,$belongs_to[$related], $objects);
			}

			if (array_key_exists($related, $sample->has_many()))
			{
				$has_many = $sample->has_many();
				$objects = $this->hydrate_has_many($related, $has_many[$related], $objects);
			}

			if (array_key_exists($related, $sample->has_and_belongs_to_many()))
			{
				$has_and_belongs_to_many = $sample->has_and_belongs_to_many();
				$objects = $this->hydrate_has_and_belongs_to_many($related,$has_and_belongs_to_many[$related], $objects);
			}

			if (array_key_exists($related, $sample->has_one()))
			{
				$has_and_belongs_to_many = $sample->has_one();
				$objects = $this->hydrate_has_one($related,$has_and_belongs_to_many[$related], $objects);
			}
		}

		return $objects;
	}

	function hydrate_belongs_to($alias, $relation, $objects) 
	{
		$ci = get_instance();
		$ci->load->model($relation['model']);
		$related_ids = array();
		foreach ($objects as $object)
		{
			$related_ids[] = $object->{$relation['with_key']};
		}

		$related_ids = array_unique($related_ids);

		$related_key = isset($relation['related_key']) ? $relation['related_key'] : 'id';
		$query = $ci->db
					->where_in($related_key, $related_ids)
					->get("{$relation['related_table']}");
		if ($query->num_rows() > 0)
		{
			$class_name = ucfirst($relation['model']);
			$result = $query->result($class_name);
		} 
		else 
		{
			$result = array();
		}

		$data = array();

		foreach ($result as $row)
		{
			$data[$row->{$related_key}] = $row;
		}

		foreach ($objects as $object)
		{
			$object->{$alias} = $data[$object->{$relation['with_key']}];
		}

		return $objects;
	}

	function hydrate_has_many($alias, $relation, $objects) 
	{
		$ci = get_instance();
		$ci->load->model($relation['model']);
		$with_ids = array();
		$with_key = isset($relation['with_key']) ? $relation['with_key'] : 'id';
		foreach ($objects as $object)
		{
			$with_ids[] = $object->{$with_key};
		}
		$related_key = $relation['related_key'];
		$query = $ci->db
					->where_in($related_key, $with_ids)
					->get("{$relation['related_table']}");
		if ($query->num_rows() > 0)
		{
			$class_name = ucfirst($relation['model']);
			$results = $query->result($class_name);
		}
		else
		{
			$results = array();
		}
		
		$data = array();
		
		foreach ($results as $row)
		{
			$data[$row->{$related_key}] = isset($data[$row->{$related_key}]) ? $data[$row->{$related_key}] : array();
			$data[$row->{$related_key}][] = $row;
		}
		
		foreach ($objects as $object)
		{
			$object->{$alias} = isset($data[$object->{$with_key}]) ? $data[$object->{$with_key}] : array(); 
		}
		
		return $objects;
	}

	function has_and_belongs_to_many($alias, $relation, $objects) 
	{
		$ci = get_instance();
		$ci->load->model($relation['model']);
		$with_ids = array();
		foreach ($objects as $object)
		{
			$with_ids[] = $object->pk();
		}

		$with_key = $relation['with_key'];
		$related_key = $relation['related_key'];
		$join_table = $relation['through'];
		$related_table = $relation['related_table'];

		$query = $ci->db->from($related_table)
					->join("{$join_table}", "{$join_table}.{$related_key} = {$related_table}.id")
					->where_in("{$join_table}.{$with_key}")->get();
		if ($query->num_rows() > 0)
		{
			$class_name = ucfirst($relation['model']);
			$results = $query->result($class_name);
		}
		else
		{
			$results = array();
		}

		$data = array();

		foreach ($results as $row)
		{
			$data[$row->{$with_key}] = isset($data[$row->{$with_key}]) ? $data[$row->{$with_key}] : array();
			$data[$row->{$with_key}][] = $row;
		}

		foreach ($objects as $object)
		{
			$object->{$alias} = $data[$object->pk()];
		}

		return $objects;
	}

	function hydrate_has_one($alias, $relation, $objects)
	{
		$ci = get_instance();
		$ci->load->model($relation['model']);

		$object_ids = array();

		foreach ($objects as $object)
		{
			$object_ids[] = $object->pk();
		}

		$related_table = $relation['related_table'];
		$related_key = $relation['related_key'];
		$query = $ci->db->where_in($related_key, $object_ids)->get($related_table);

		if ($query->num_rows() > 0)
		{
			$class_name = ucfirst($relation['model']);
			$results = $query->result($class_name);
		}
		else 
		{
			$results = array();
		}

		$data = array();
		foreach ($results as $row)
		{
			$data[$row->{$related_key}] = isset($data[$row->{$related_key}]) ? $data[$row->{$related_key}] : array();
			$data[$row->{$related_key}] = $row; 
		}

		foreach ($objects as $object)
		{
			$object->{$alias} = $data[$object->pk()];
		}
		
		return $objects;
	}

}