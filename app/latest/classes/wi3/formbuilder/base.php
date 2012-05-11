<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Formbuilder Base class
 * @package Wi3
 * @author	Willem Mulder
 */
 
class Wi3_Formbuilder_Base extends Wi3_Base
{

    public $settings;
    public $attributes;
    
    public function __construct()
    {
       $this->settings = $this->attributes = new ArrayObject(Array(), 2); // The 2 flag to enable standard object->something reading and writing
    }
   
    // All these functions are chainable
    public function set($setting, $val)
    {
        $this->settings->$setting = $val;
        return $this;
    }
    
    public function __SET($setting, $val)
    {
        $this->set($setting, $val);
        return $this;
    }
    
    public function attr($attr, $val = null)
    {
        if (is_array($attr)) {
            foreach($attr as $key => $val) {
                $this->attr($key, $val);
            }
        } else {
            if (empty($val))
            {
                if (isset($this->attributes->{$attr})) {
                    unset($this->attributes->{$attr});
                }
            }
            else
            {
            $this->attributes->{$attr} = $val;
            }
        }
        return $this;
    }
    
} // End class
    
?>
