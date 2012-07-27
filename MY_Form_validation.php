<?php  if (! defined('BASEPATH')) exit('No direct script access allowed');


class MY_Form_validation extends CI_Form_validation {
	
	
	public function rules()
	{
		return $this->_field_data;
	}
	
	
	public function errors()
	{
		return $this->_error_array;
	}
	
	public function valid_color($str)
	{
		return (bool) preg_match('/^#+[0-9a-f]{3}(?:[0-9a-f]{3})?$/iD', $str);
	}
	
	public static function valid_url($url)
	{
		// Based on http://www.apps.ietf.org/rfc/rfc1738.html#sec-5
		if ( ! preg_match(
			'~^

			# scheme
			https?://

			# username:password (optional)
			(?:
				    [-a-z0-9$_.+!*\'(),;?&=%]++   # username
				(?::[-a-z0-9$_.+!*\'(),;?&=%]++)? # password (optional)
				@
			)?

			(?:
				# ip address
				\d{1,3}+(?:\.\d{1,3}+){3}+

				| # or

				# hostname (captured)
				(
					     (?!-)[-a-z0-9]{1,63}+(?<!-)
					(?:\.(?!-)[-a-z0-9]{1,63}+(?<!-)){0,126}+
				)
			)

			# port (optional)
			(?::\d{1,5}+)?

			# path (optional)
			(?:/.*)?

			$~iDx', $url, $matches))
			return FALSE;

		// We matched an IP address
		if ( ! isset($matches[1]))
			return TRUE;

		// Check maximum length of the whole hostname
		// http://en.wikipedia.org/wiki/Domain_name#cite_note-0
		if (strlen($matches[1]) > 253)
			return FALSE;

		// An extra check for the top level domain
		// It must start with a letter
		$tld = ltrim(substr($matches[1], (int) strrpos($matches[1], '.')), '.');
		return ctype_alpha($tld[0]);
	}
	
	public static function unique($value, $extras)
	{
		list($table, $field, $id) = explode('.', $extras);
		$query = get_instance()->db->where($field, $value)->where('id !=', $id)->get($table);
		
		if ($query->num_rows() > 0)
		{
			get_instance()->form_validation->set_message('unique', ucfirst($field).' "'.$value.'" is already taken.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public static function allowed_values($value, $values)
	{
		$values = explode(",", $values);
		if (in_array($value, $values))
		{
			return TRUE;
		}
		else
		{
			get_instance()->form_validation->set_message('allowed_values', "Value {$value} must be one of ".implode(",", $values));
			return FALSE;
		}
	}
	
}
