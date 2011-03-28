<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Auth User
 * @package Wi3
 * @author	Willem Mulder
 */
class Model_Site_Role extends Model_Auth_Role
{
    public $_db = "site";
    
    protected function _init()
	{
        parent::_init();
        // Overrule the names of the User_Roles model to the Site_... version
        $this->_fields = array_merge($this->_fields, array(
            'users' => new Sprig_Field_ManyToMany(array(
				'model' => 'Site_User',
				'through' => 'site_roles_users'
			)),
		));
	}
    
}