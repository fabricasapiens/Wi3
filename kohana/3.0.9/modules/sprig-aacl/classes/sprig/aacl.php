<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Base class for access controlled Sprig Models
 * 
 * @see			http://github.com/banks/aacl
 * @package		AACL
 * @uses		Auth
 * @uses		Sprig
 * @author		Paul Banks
 * @copyright	(c) Paul Banks 2010
 * @license		MIT
 */
abstract class Sprig_AACL extends Sprig implements AACL_Resource
{
	/**
	 * AACL_Resource::acl_id() implementation
	 * 
	 * @return	string 
	 */
	public function acl_id()
	{
		// Create unique id from primary key if it is set		
		if (is_array($this->_primary_key))
		{
			$id = '';
			
			foreach ($this->_primary_key as $name)
			{
				$id .= (string) $this->$name;
			}
		}
		else
		{
			$id = (string) $this->{$this->_primary_key};
		}
		
		if ( ! empty($id))
		{
			$id = '.'.$id;
		}
		
		// Model namespace, model name, pk
		return 'm:'.strtolower($this->_model).$id;
	}
	
	/**
	 * AACL_Resource::acl_actions() implementation
	 * 
	 * @param	bool	$return_current [optional]
	 * @return	mixed
	 */
	public function acl_actions($return_current = FALSE)
	{
		if ($return_current)
		{
			// We don't know anything about what the user intends to do with us!
			return NULL;
		}
		
		// Return default model actions
		return array('create', 'read', 'update', 'delete');
	}
	
	/**
	 * AACL_Resource::acl_conditions() implementation
	 * 
	 * @param	Model_User	$user [optional] logged in user model
	 * @param	object    	$condition [optional] condition to test
	 * @return	mixed
	 */
	public function acl_conditions(Model_User $user = NULL, $condition = NULL)
	{
		if (is_null($user) AND is_null($condition))
		{
			// We have no conditions - they will be model specific
			return array();
		}
		else
		{
			// We have no conditions so this test should fail!
			return FALSE;
		}
	}
	
	/**
	 * AACL_Resource::acl_instance() implementation
	 * 
	 * Note that the object instance returned should not be used for anything except querying the acl_* methods
	 * 
	 * @param	string	Class name of object required
	 * @return	Object
	 */
	public static function acl_instance($class_name)
	{		
		$model_name = strtolower(substr($class_name, 6));
		
		return Sprig::factory($model_name);
	}
	
} // End  Sprig_AACL