<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Formbuilder Input class
 * @package Wi3
 * @author	Willem Mulder
 */
 
class Wi3_Formbuilder_Input extends Wi3_Formbuilder_Base
{
   
    public function render()
    {
        $ret = "<label for='" . $this->attributes->name . "'/><input name='" . $this->attributes->name . "' ";
        if (isset($this->attributes->value)) 
        {
            $ret .= "value='" . $this->attributes->value . "' ";
        }
        $ret .= "></input>";
        return $ret;
    }
   
}
    
?>
