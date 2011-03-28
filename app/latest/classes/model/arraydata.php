<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Auth User
 * @package Wi3
 * @author	Willem Mulder
 */
class Model_Arraydata extends Sprig
{
    public $_db = "global";
    
    protected function _init()
	{
        parent::_init();
        // Overrule the names of the Roles and User_Token model to the Site_... version
		$this->_fields = array(
            'id' => new Sprig_Field_Auto,
            
            'array' => new Sprig_Field_BelongsTo(array(
				'model' => 'Array',
                // 'column' => 'array_id', // $key_id is assumed // Column to be used in the database table for this model
                // 'foreign_key' => 'id' // Is assumed
			)),
            
            'key' => new Sprig_Field_Char(array(
				'empty'  => TRUE, // Default FALSE
				'unique' => TRUE, // Default FALSE
			)),
            'val' => new Sprig_Field_Char(array(
				'empty'  => TRUE, // Default FALSE
				'unique' => FALSE, // Default FALSE
			)),
            
		);
	}
}