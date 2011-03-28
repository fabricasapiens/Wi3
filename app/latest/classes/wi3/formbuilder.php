<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Form-related functions for Wi3
 * @package Wi3
 * @author	Willem Mulder
 */
 
 // This class covers wi3-kohana-specific functions
class Wi3_Formbuilder extends Wi3_Base
{
   
    public function input($settings = Array())
    {
        return Wi3_Formbuilder_Input::inst(NULL, $settings);
    }
    
    public function dateselector($settings = Array())
    {
        return Wi3_Formbuilder_Dateselector::inst(NULL, $settings);
    }
    
    public function fileselector($settings = Array())
    {
        return Wi3_Formbuilder_Fileselector::inst(NULL, $settings);
    }
   
}
    
?>
