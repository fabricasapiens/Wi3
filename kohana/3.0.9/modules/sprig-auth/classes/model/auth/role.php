<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Auth Role Model
 * @package Sprig Auth
 * @author 	Paul Banks
 */
class Model_Auth_Role extends Sprig 
{
	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,
			'name' => new Sprig_Field_Char(array(
				'max_length' => 32,
				'unique' => TRUE,
				'empty' => FALSE
			)),
			'description' => new Sprig_Field_Char(array(
				'max_length' => 255,
			)),
			'users' => new Sprig_Field_ManyToMany(array(
				'model' => 'User',
				'through' => 'roles_users'
			)),
		);
	}
} // End Model_Auth_Role