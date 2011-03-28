<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Access rule model
 * 
 * @see			http://github.com/banks/aacl
 * @package		AACL
 * @uses		Auth
 * @uses		Sprig
 * @author		Paul Banks
 * @copyright	(c) Paul Banks 2010
 * @license		MIT
 */
class Model_AACL_Rule extends Sprig_AACL
{
	protected function _init()
	{
		$this->_fields += array(
			'id' 		=> new Sprig_Field_Auto,
			'role' 	=> new Sprig_Field_BelongsTo(array(
				'model'			=> 'role',
			)),
			'resource' 	=> new Sprig_Field_Char(array(
				'max_length' 	=> 45,
				'null'			=> FALSE,
			)),
			'action'	=> new Sprig_Field_Char(array(
				'max_length' 	=> 25,
				'null'			=> TRUE,
			)),
			'condition'	=> new Sprig_Field_Char(array(
				'max_length' 	=> 25,
				'null'			=> TRUE,
			)),
		);
	}
	
	/**
	 * Check if rule matches current request
	 * 
	 * @param AACL_Resource	AACL_Resource object that user requested access to
	 * @param string        action requested [optional]
	 * @return 
	 */
	public function allows_access_to(AACL_Resource $resource, $action = NULL)
	{
		if ($this->resource === '*')
		{
			// No point checking anything else!
			return TRUE;
		}
		
		if (is_null($action))
		{
			// Check to see if Resource whats to define it's own action
			$action = $resource->acl_actions(TRUE);
		}
		
		// Get string id
		$resource_id = $resource->acl_id();
		
		// Make sure action matches
		if ( ! is_null($action) AND ! is_null($this->action) AND $action !== $this->action)
		{
			// This rule has a specific action and it doesn't match the specific one passed
			return FALSE;
		}
		
		$matches = FALSE;
		
		// Make sure rule resource is the same as requested resource, or is an ancestor
		while( ! $matches)
		{
			// Attempt match
			if ($this->resource === $resource_id)
			{
				// Stop loop
				$matches = TRUE;
			}
			else
			{
				// Find last occurence of '.' separator
				$last_dot_pos = strrpos($resource_id, '.');
				
				if ($last_dot_pos !== FALSE)
				{
					// This rule might match more generally, try the next level of specificity
					$resource_id = substr($resource_id, 0, $last_dot_pos);
				}
				else
				{
					// We can't make this any more general as there are no more dots
					// And we haven't managed to match the resource requested
					return FALSE;
				}
			}
		}
		
		// Now we know this rule matches the resource, check any match condition
		if ( ! is_null($this->condition) AND ! $resource->acl_conditions(Auth::instance()->get_user(), $this->condition))
		{
			// Condition wasn't met (or doesn't exist)
			return FALSE;
		}
		
		// All looks rosy!
		return TRUE;
	}
	
	/**
	 * Override create to remove less specific rules when creating a rule
	 * 
	 * @return $this
	 */
	public function create()
	{
		// Delete all more specifc rules for this role
		$delete = DB::delete($this->_table)
			->where($this->_fields['role']->column, '=', $this->_changed['role']);
		
		// If resource is '*' we don't need any more rules - we just delete every rule for this role
		
		if ($this->resource !== '*')
		{
			// Need to restrict to roles with equal or more specific resource id
			$delete->where_open()
				->where($this->_fields['resource']->column, '=', $this->resource)
				->or_where($this->_fields['resource']->column, 'LIKE', $this->resource.'.%')
				->where_close();
		}
		
		if ( ! is_null($this->action))
		{
			// If this rule has an action, only remove other rules with the same action
			$delete->where($this->_fields['action']->column, '=', $this->action);
		}
		
		if ( ! is_null($this->condition))
		{
			// If this rule has a condition, only remove other rules with the same condition
			$delete->where($this->_fields['condition']->column, '=', $this->condition);
		}		
		
		// Do the delete
		$delete->execute($this->_db);
		
		// Create new rule
		parent::create();
	}
	
	/**
	 * Override Default model actions
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
		return array('grant', 'revoke');
	}
	
} // End  Model_AACL_Rule