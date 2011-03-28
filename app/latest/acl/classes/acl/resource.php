<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * AACL Resource interface
 * 
 * @see			http://github.com/banks/aacl
 * @package		AACL
 * @uses		Auth
 * @uses		Sprig
 * @author		Paul Banks
 * @copyright	(c) Paul Banks 2010
 * @license		MIT
 */
interface AACL_Resource
{
	/**
	 * Gets a unique ID string for this resource
	 * 
	 * Convention for controllers is 	c:controller_name
	 * Convention for models is 		m:model_name.primary_key_value
	 * 
	 * @return	string 
	 */
	public function acl_id();
	
	/**
	 * Returns actions specific to this resource as an array
	 * 
	 * For no actions return empty array
	 * 
	 * Example: return array('create', 'read', 'update', 'delete')
	 * 
	 * If $return_current is TRUE, return value should be the currently requested action or NULL if not known.
	 * 
	 * @param	bool	$return_current [optional]
	 * @return	mixed
	 */
	public function acl_actions($return_current = FALSE);
	
	/**
	 * Defines any condition fro this resource
	 * 
	 * If params are provided, returns a boolean indicating whether $user meets condition specified in $condition
	 * 
	 * If no params are provided, returns an array or available conditions for this resource in form
	 * 
	 * return array('condition_id' => 'User friendly description of condition');
	 * 
	 * @param	Model_User	$user [optional] logged in user model
	 * @param	object    	$condition [optional] condition to test
	 * @return	mixed
	 */
	public function acl_conditions(Model_User $user = NULL, $condition = NULL);
	
	/**
	 * Returns an instance of the current object suitable for calling the above methods
	 * 
	 * Note that the object instance returned should not be used for anything except querying the acl_* methods
	 * 
	 * @param	string	Class name of object required
	 * @return	Object
	 */
	public static function acl_instance($class_name);
	
} // End  AACL_Resource