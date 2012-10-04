<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Auth User
 * @package Wi3
 * @author	Willem Mulder
 */
class Model_Site_Arraydata extends Sprig
{
    public $_db = "site";
    
    protected function _init()
	{
        // Overrule the names of the Roles and User_Token model to the Site_... version
		$this->_fields = array(
            'id' => new Sprig_Field_Auto,
            
            'array' => new Sprig_Field_BelongsTo(array(
				'model' => 'Site_Array',
                'column' => 'site_array_id', // $key_id is assumed // Column to be used in the database table for this model
                'foreign_key' => '_id' // 'id' Is assumed
			)),
            
            'key' => new Sprig_Field_Char(array(
				'empty'  => TRUE, // Default FALSE
			)),
            'val' => new Sprig_Field_Text(array(
				'empty'  => TRUE, // Default FALSE
			)),
            
		);
	}
}