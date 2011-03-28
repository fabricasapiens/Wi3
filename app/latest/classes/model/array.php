<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Auth User
 * @package Wi3
 * @author	Willem Mulder
 */
class Model_Array extends Sprig
{
    public $_db = "global";
    
    protected function _init()
	{
        parent::_init();
        // Overrule the names of the Roles and User_Token model to the Site_... version
		$this->_fields = array(
            'id' => new Sprig_Field_Auto,
            
           'refclass' => new Sprig_Field_Char(array(
				'empty'  => TRUE, // Default FALSE
				'unique' => TRUE, // Default FALSE
			)),
            'refid' => new Sprig_Field_Char(array(
				'empty'  => TRUE, // Default FALSE
				'unique' => TRUE, // Default FALSE
			)),
            
            'arraydatas' => new Sprig_Field_HasMany(array(
                'model' => 'Arraydata'
            )),
            
		);
	}
}