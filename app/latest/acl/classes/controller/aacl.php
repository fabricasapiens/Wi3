<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Base class for access controlled controllers
 * 
 * @see			http://github.com/banks/aacl
 * @package		AACL
 * @uses		Auth
 * @uses		Sprig
 * @author		Paul Banks
 * @copyright	(c) Paul Banks 2010
 * @license		MIT
 */
abstract class Controller_AACL extends Controller_Template implements AACL_Resource
{
	/**
	 * AACL_Resource::acl_id() implementation
	 * 
	 * @return	string 
	 */
	public function acl_id()
	{
		// Controller namespace, controller name
		return 'c:'.strtolower($this->request->controller);
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
			return $this->request->action;
		}
		
		// Find all actions in this class
		$reflection = new ReflectionClass($this);
		
		$actions = array();
		
		// Add all public methods that start with 'action_'
		foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
		{
			if (substr($method->name, 0, 7) === 'action_')
			{
				$actions[] = substr($method->name, 7);
			}
		}
		
		return $actions;
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
			// We have no conditions
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
		// Return controller instance populated with manipulated request details
		$instance = new $class_name(Request::instance());
		
		$controller_name = strtolower(substr($class_name, 11));
		
		if ($controller_name !== Request::instance()->controller)
		{
			// Manually override controller name and action
			$instance->request->controller = strtolower(substr(get_class($this), 11));
			
			$instance->request->action = NULL;
		}
		
		return $instance;
	}
	
} // End  Controller_AACL