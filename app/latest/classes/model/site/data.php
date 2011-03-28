<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Auth User
 * @package Wi3
 * @author	Willem Mulder
 */
class Model_Site_Data extends Sprig
{
    public $_db = "site";
    
    protected function _init()
	{
        // Overrule the names of the Roles and User_Token model to the Site_... version
		$this->_fields = array(
            'id' => new Sprig_Field_Auto,
            
           '_refclass' => new Sprig_Field_Char(array(
				'empty'  => TRUE, // Default FALSE
			)),
            '_refid' => new Sprig_Field_Char(array(
				'empty'  => TRUE, // Default FALSE
			)),
            
            'name' => new Sprig_Field_Char(array(
				'empty'  => TRUE, // Default FALSE
			)),
            
            'data' => new Sprig_Field_Text(array(
				'empty'  => TRUE, // Default FALSE
			)),
            
		);
	}
    
    public function setname($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function setref($object)
    {
        $this->_refclass = get_class($object);
        $this->_refid = $object->{$object->pk()}; // It is assumed that any object has an its primary key available through $obj->pk()
        return $this;
    }
}
