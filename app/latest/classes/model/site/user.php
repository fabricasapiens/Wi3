<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Auth User
 * @package Wi3
 * @author	Willem Mulder
 */
class Model_Site_User extends Model_Auth_User 
{
    public $_db = "site";
    
    protected function _init()
	{
        parent::_init();
        // Overrule the names of the Roles and User_Token model to the Site_... version
		$this->_fields = array_merge($this->_fields, array(
            'tokens' => new Sprig_Field_HasMany(array(
				'model' => 'Site_User_Token',
				'editable' => FALSE,
			)),
			'roles' => new Sprig_Field_ManyToMany(array(
				'model' => 'Site_Role',
				'through' => 'site_roles_users',
			)),
		));
	}
}