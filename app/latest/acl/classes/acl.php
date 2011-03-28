<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Another ACL
 * 
 * @see			http://github.com/banks/aacl
 * @package		AACL
 * @uses		Auth
 * @uses		Sprig
 * @author		Paul Banks
 * @copyright	(c) Paul Banks 2010
 * @license		MIT
 */
class ACL extends Wi3_Base
{
	/**
	 * All rules that apply to certain users
     * Objects that are structured like
     * ->resource
	 * 
	 * @var	array	contains Model_AACL_Rule objects
	 */
	protected static $_rules;
	
	/**
	 * Grant access to $role for resource
	 * 
	 * @param	mixed	string role name or Model_Role object
	 * @param	string	resource identifier
	 * @param	string	action [optional]
	 * @param	string	condition [optional]
	 * @return 	void
	 */
	public static function grant($role, $resource, $action = NULL, $condition = NULL)
	{
        if (!isset(self::$_rules[$role]))
        {
            self::$_rules[$role] = array();
        }
		self::$_rules[$role][] = new ACL_rule(array("role"=>$role, "resource"=>&$resource, "action"=>$action, "condition"=>$condition));
	}
	
	/**
	 * Revoke access to $role for resource
	 * 
	 * @param	mixed	string role name or Model_Role object
	 * @param	string	resource identifier
	 * @param	string	action [optional]
	 * @param	string	condition [optional]
	 * @return 	void
	 */
	public static function revoke($role, $resource, $action = NULL, $condition = NULL)
	{
		// Normalise $role
		if ( ! $role instanceof Model_Role)
		{
			$role = Sprig::factory('role', array('name' => $role))->load();
		}
		
		// Check role exists
		if ( ! $role->loaded())
		{
			// Just return without deleting anything
			return;
		}
		
		$model = Sprig::factory('aacl_rule', array(
			'role' => $role->id,
		));
		
		if ($resource !== '*')
		{
			// Add normal reources, resource '*' will delete all rules for this role
			$model->resource = $resource;
		}
		
		if ($resource !== '*' AND ! is_null($action))
		{
			$model->action = $action;
		}
		
		if ($resource !== '*' AND ! is_null($condition))
		{
			$model->condition = $condition;
		}
		
		// Delete rule
		$model->delete();
	}
	
	/**
	 * Checks user has permission to access resource
	 * 
	 * @param	AACL_Resource	AACL_Resource object being requested
	 * @param	string			action identifier [optional]
	 * @throw	AACL_Exception	To identify permission or authentication failure
	 * @return	void
	 */
	public static function check($resource, $action = NULL, $returntrueorfalse = FALSE)
	{
        $rulesforeverybody = self::_get_rules("*");
		if ($user = Auth::instance()->get_user()) // NOTE: The Auth instance can be either a global-instance, or a site-instance, depending on the Wi3-init (and thus depending on the controller)!!
		{
            // First check rules for everybody
            foreach ($rulesforeverybody as $rule)
			{
				if ($rule->allows_access_to($resource, $action))
				{
					// Access granted
                    if ($returntrueorfalse === TRUE)
                    {
                        return TRUE;
                    }
                    else
                    {
                        return;
                    }
				}
			}
            
			// User is logged in, check his own rules
			$rules = self::_get_rules($user);
			
			foreach ($rules as $rule)
			{
				if ($rule->allows_access_to($resource, $action))
				{
					// Access granted
					if ($returntrueorfalse === TRUE)
                    {
                        return TRUE;
                    }
                    else
                    {
                        return;
                    }
				}
			}
			
			// No access rule matched
			if ($returntrueorfalse === TRUE)
            {
                return FALSE;
            }
            else
            {
               throw new ACL_Exception_403;
            }
		}
		elseif(!empty($rulesforeverybody))
        {
            // There are rules for not-logged-in users
			$rules = $rulesforeverybody;
			
			foreach ($rules as $rule)
			{
				if ($rule->allows_access_to($resource, $action))
				{
					// Access granted
                    if ($returntrueorfalse === TRUE)
                    {
                        return TRUE;
                    }
                    else
                    {
                        return;
                    }
				}
			}
			
			// User is not logged in, and there are also no rules for everybody to grant any access. Access could be granted if he was logged in, thus authentication required.
			if ($returntrueorfalse === TRUE)
            {
                return FALSE;
            }
            else
            {
               throw new ACL_Exception_401;
            }
            
        }
        else
		{
			// User is not logged in, and there are also no rules for everybody to grant any access. Access could be granted if he was logged in, thus authentication required.
			if ($returntrueorfalse === TRUE)
            {
                return FALSE;
            }
            else
            {
               throw new ACL_Exception_401;
            }
		}
	}
	
	/**
	 * Get all rules that apply to user
	 * 
	 * @param 	Model_User 	$user
	 * @param 	bool		[optional] Force reload from DB default FALSE
	 * @return 	array
	 */
	protected static function _get_rules($user, $force_load = FALSE)
	{
        if ($user instanceof Model_Auth_User) // CHANGED: Model_User to more generic Model_Auth_User 
        {
            $user = $user->username;
        }
		return isset(self::$_rules[$user]) ? self::$_rules[$user] : array();
	}
	
	protected static $_resources;
	
	/**
	 * Returns a list of all valid resource objects based on the filesstem adn reflection
	 * 
	 * @param	mixed	string resource_id [optional] if provided, the info for that specific resource ID is returned, 
	 * 					if TRUE a flat array of just the ids is returned
	 * @return	array 
	 */
	public static function list_resources($resource_id = FALSE)
	{		
		if ( ! isset(self::$_resources))
		{
			// Find all classes in the application and modules
			$classes = self::_list_classes();
			
			// Loop throuch classes and see if they implement AACL_Resource
			foreach ($classes as $i => $class_name)
			{
				$class = new ReflectionClass($class_name);
				
				if ($class->implementsInterface('AACL_Resource'))
				{
					// Ignore interfaces
					if ($class->isInterface())
					{
						continue;
					}
					
					// Ignore abstract classes
					if ($class->isAbstract())
					{
						continue;
					}
	
					// Create an instance of the class
					$resource = $class->getMethod('acl_instance')->invoke($class_name, $class_name);
					
					// Get resource info
					self::$_resources[$resource->acl_id()] = array(
						'actions' 		=> $resource->acl_actions(),
						'conditions'	=> $resource->acl_conditions(),
					);
					
				}
				
				unset($class);
			}			
		}
		
		if ($resource_id === TRUE)
		{
			return array_keys(self::$_resources);
		}
		elseif ($resource_id)
		{
			return isset(self::$_resources[$resource_id]) ? self::$_resources[$resource_id] : NULL;
		}
		
		return self::$_resources;
	}
	
	protected static function _list_classes($files = NULL)
	{
		if (is_null($files))
		{
			// Remove core module paths form search
			$loaded_modules = Kohana::modules();
			
			$exclude_modules = array('database', 'orm', 'sprig', 'auth', 'sprig-auth', 
				'userguide', 'image', 'codebench', 'unittest', 'pagination');
				
			$paths = Kohana::include_paths();
			
			// Remove known core module paths
			foreach ($loaded_modules as $module => $path)
			{
				if (in_array($module, $exclude_modules))
				{					
					unset($paths[array_search($path.DIRECTORY_SEPARATOR, $paths)]);
				}
			}	
			
			// Remove system path
			unset($paths[array_search(SYSPATH, $paths)]);
			
			$files = Kohana::list_files('classes', $paths);
		}
		
		$classes = array();
		
		foreach ($files as $name => $path)
		{
			if (is_array($path))
			{
				$classes = array_merge($classes, self::_list_classes($path));
			}
			else
			{
				// Strip 'classes/' off start
				$name = substr($name, 8);
				
				// Strip '.php' off end
				$name = substr($name, 0, 0 - strlen(EXT));
				
				// Convert to class name
				$classes[] = str_replace(DIRECTORY_SEPARATOR, '_', $name);
			}
		}
		
		return $classes;
	}
	
	/**
	 * Force static access
	 * 
	 * @return	void 
	 */
	protected function __construct() {}
	
	/**
	 * Force static access
	 * 
	 * @return	void 
	 */
	protected function __clone() {}
	
} // End  AACL