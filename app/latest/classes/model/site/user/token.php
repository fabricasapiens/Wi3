<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Auth User
 * @package Wi3
 * @author	Willem Mulder
 */
class Model_Site_User_Token extends Model_Auth_User_Token
{
    public $_db = "site";
    
    protected function _init()
	{
        parent::_init();
        // Overrule the names of the User_Roles model to the Site_... version
		$this->_fields = array_merge($this->_fields, array(
            'user' => new Sprig_Field_BelongsTo(array(
				'model' => 'Site_User',	
                'column' => 'site_user_id'
			)),
		));
	}
}