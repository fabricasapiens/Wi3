<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Auth User Model
 * @package Sprig Auth
 * @author	Paul Banks
 */
class Model_Auth_User extends Sprig
{
	protected $_title_key = 'username';

	protected $_sorting = array('username' => 'asc');

	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,
			'username' => new Sprig_Field_Char(array(
				'empty'  => FALSE,
				'unique' => TRUE,
				'max_length' => 32,
				'rules'  => array(
					'regex' => array('/^[\pL_.-]+$/ui')
				),
			)),
			'password' => new Sprig_Field_Password(array(
				'empty' => FALSE,
				'hash_with' => array(Auth::instance(), 'hash_password'),
			)),
			'password_confirm' => new Sprig_Field_Password(array(
				'empty' => TRUE,
				'in_db' => FALSE,
				'hash_with' => NULL,
				'callbacks' => array(
					'matches' => array($this, '_check_password_matches'),
				),
			)),
			'email' => new Sprig_Field_Email(array(
				'unique' => TRUE,
				'empty' => FALSE,
				'max_length' => 127
			)),
			'logins' => new Sprig_Field_Integer(array(
				'empty' => TRUE,
				'editable' => FALSE,
			)),
			'last_login' => new Sprig_Field_Timestamp(array(
				'empty' => TRUE,
				'editable' => FALSE,
			)),
			'tokens' => new Sprig_Field_HasMany(array(
				'model' => 'User_Token',
				'editable' => FALSE,
			)),
			'roles' => new Sprig_Field_ManyToMany(array(
				'model' => 'Role',
				'through' => 'roles_users',
			)),
		);
	}

	/**
	 * Convenience method for getting user by any unique key
	 * @param mixed string unique key (email or username), or integer id
	 * @return Model_Auth_User
	 */
	public function unique_key($key)
	{
		if ( ! empty($key))
		{
			$this->state('loading');
            
			$this->load(DB::select()
				->where('username', '=', $key)
				->or_where('email', '=', $key)
				->or_where('id', '=', $key));
		}
        
		return $this;
	}
	
	/**
	 * NOTE: __sleep no longer needed and was removed
	 *  - Auth no longer stores the whole object in the session
	 *  - It was useless without a __wake
	 *  - Utilizing the Serialize interface in the Sprig core
	 *    class would be a better solution
	 */
	
	/**
	 * Validate callback wrapper for checking password match
	 * @param Validate $array
	 * @param string   $field
	 * @return void
	 */
	public function _check_password_matches(Validate $array, $field)
	{
		$auth = Auth::instance();
		
		$salt = $auth->find_salt($array['password']);		

		if ($array['password'] !== $auth->hash_password($array[$field], $salt))
		{
			// Re-use the error message from the 'matches' rule in Validate
			$array->error($field, 'matches', array('param1' => 'password'));
		}
	}
	
	/**
	 * Check if user has a particular role
	 * @param mixed $role 	Role to test for, can be Model_Role object, string role name of integer role id
	 * @return bool	    Whether or not the user has the requested role
	 */
	public function has_role($role)
	{
		// Check what sort of argument we have been passed
		if ($role instanceof Sprig)
		{
			$key = 'id';
			$val = $role->id;
		}
		elseif (is_string($role))
		{
			$key = 'name';
			$val = $role;
		}
		else
		{
			$key = 'id';
			$val = (int) $role;
		}

		$values = $this->refresh('roles')->as_array(NULL,$key);
		return (in_array($val, $values));
	}

	/**
	 * Check if user has a set of particular roles
	 * @param mixed $role 	Role to test for, can be Model_Role object, string role name of integer role id
	 * @return bool	    Whether or not the user has the requested role
	 */
	public function has_roles(array $roles)
	{
		// Check what sort of arguments
		$check = array('ids' => array(), 'names' => array());
		foreach($roles as $role)
		{
			if ($role instanceof Sprig)
			{
				$check['ids'][] = $role->id;
			}
			elseif (is_string($role))
			{
				$check['names'][] = $role;
			}
			else
			{
				$check['ids'][] = (int) $role;
			}
		}
		$values = $this->refresh('roles')->as_array('id','name');
		$diff  = array_diff($check['ids'], array_keys($values));
		$diff += array_diff($check['names'], array_values($values));
		return (count($diff) == 0);
	}

	/**
	 * Refresh/Reset the _related array
	 *
	 * @param string $field	    The name of teh related field
	 * @return mixed    Returns $this when fields is NULL or the field specified
	 */
	public function refresh($field = NULL)
	{
		// Refresh the user data is $field is NULL
		if ($field === NULL)
		{
			// Save the id
			$id = $this->id;
			//Reset the storage arrays
			$this->_original =
			$this->_changed =
			$this->_related = array();
			// Set the id and return the loaded user
			$this->id = $id;
			return $this->load();
		}

		// Only refresh loaded related fields
		if (isset($this->_related[$field]))
		{
			unset($this->_related[$field]);
			unset($this->_original[$field]);
		}

		return $this->{$field};
	}

} // End Model_Auth_User