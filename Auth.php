<?php  if (! defined('BASEPATH')) exit('No direct script access allowed');

class Auth {

	protected $_current_user = NULL;
	
	function __construct($config = array())
	{
		$this->config = $config;
		get_instance()->load->library('session');
		get_instance()->load->model('user_model');
	}
	
	/**
	 * Authenticates a user with $username and password.
	 * 
	 * @param string $username
	 * @param string $password.
	 * @param bool $remember_me 
	 * @return bool
	 */
	function authenticate($username, $password, $remember_me = FALSE)
	{
		$user = get_instance()->user_model->where(array('username' => $username))->get();
		
		if ($user and get_instance()->user_model->has_password($user, $password))
		{
			$this->authenticate_user($user);
			if ($remember_me) $this->remember_user();
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Stores the users info in session.
	 * 
	 * @param object $user
	 */
	function authenticate_user($user)
	{
		get_instance()->session->set_userdata('user_id', $user->id);
		$this->_current_user = $user;
	}
	
	/**
	 * Returns current authenticated user or NULL
	 * 
	 * @return object or NULL
	 */
	function current_user()
	{
		if (! $this->is_authenticated()) return NULL;
		
		if (empty($this->_current_user))
		{
			$this->_current_user = get_instance()->user_model->find_by_id(get_instance()->session->userdata('user_id', 0));
		}
		
		return $this->_current_user;
	}
	
	/**
	 * Fetches current user id from session.
	 * 
	 * @return int
	 */
	function user_id()
	{
		return get_instance()->session->userdata('user_id', 0);
	}

	/**
	 * Remembers the current authenticated user with remember me cookie.
	 */
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

	/**
	 * Checks if the user is remembered.
	 * 
	 * If user is remembered via remember me cookie it will authenticate the user.
	 * 
	 * @return bool
	 */
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

		$this->authenticate_user($user);
		return TRUE;
	}

	/**
	 * Forgets user via clearing the remember me cookie.
	 */
	function forget_user()
	{
		get_instance()->user_model->update($this->current_user()->id, array('remember_me_token' => NULL));
		get_instance()->input->set_cookie(array(
			'name' => $this->config['remember_me_cookie'],
			'value' => NULL,
		));
	}
	
	/**
	 * Checks if the user is authenticated.
	 * 
	 * @return bool
	 */
	function is_authenticated()
	{
		return (bool) get_instance()->session->userdata('user_id');
	}
	
	/**
	 * Logs out the current authenticated user.
	 */
	function logout()
	{
		$this->forget_user();
		$this->_current_user = NULL;
		get_instance()->session->unset_userdata('user_id');
	}
	
}
