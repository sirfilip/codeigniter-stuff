<?php  if (! defined('BASEPATH')) exit('No direct script access allowed');

class Auth {

	static $_current_user;
	
	function __construct($config = array())
	{
		$this->config = $config;
		get_instance()->load->library('session');
		get_instance()->load->model('users/user_model');
	}
	
	function authenticate($username, $password, $remember_me = FALSE)
	{
		$user = get_instance()->user_model->find(array('username' => $username))->get();
		if ($user and $user->has_password($password))
		{
			get_instance()->session->set_userdata('user_id', $user->pk());
			self::$_current_user = $user;
			if ($remember_me) $this->remember_user();
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	function current_user()
	{
		if (! $this->is_authenticated()) return NULL;
		
		if (empty(self::$_current_user))
		{
			self::$_current_user = get_instance()->user_model->find_by_id(get_instance()->session->userdata('user_id', 0))->get();
		}
		
		return self::$_current_user;
	}

	function user_id()
	{
		return get_instance()->session->userdata('user_id', 0);
	}

	function remember_user()
	{
		$token = get_instance()->user_model->generate_token($this->current_user()->id);
		get_instance()->input->set_cookie(array(
			'name' => $this->config['remember_me_cookie'],
			'value' => $token,
			'expire' => $this->config['remember_me_duration'],
		));
		get_instance()->user_model->update($this->current_user()->id, array('remember_me_token' => $token));
	}

	function remember_me_check()
	{
		$token = get_instance()->input->cookie($this->config['remember_me_cookie'], TRUE);

		if (! $token) return FALSE;

		$user = get_instance()->user_model->find(array('remember_me_token' => $token))->get();

		if (! $user) 
		{
			get_instance()->input->set_cookie(array(
				'name' => $this->config['remember_me_cookie'],
				'value' => NULL,
			));
			return FALSE;
		}

		self::$_current_user = $user;
		get_instance()->session->set_userdata('user_id', $user->id);
		return TRUE;
	}

	function forget_user()
	{
		get_instance()->user_model->update($this->current_user()->id, array('remember_me_token' => NULL));
		get_instance()->input->set_cookie(array(
			'name' => $this->config['remember_me_cookie'],
			'value' => NULL,
		));
	}
	
	function is_authenticated()
	{
		return (bool) get_instance()->session->userdata('user_id');
	}
	
	function logout()
	{
		$this->forget_user();
		self::$_current_user = NULL;
		get_instance()->session->unset_userdata('user_id');
	}
	
}
