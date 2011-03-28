<?php defined('SYSPATH') or die('No direct script access.');

    class Wi3_Configof extends Wi3_Base
    {
        
        public $configinstances = array();
        
        public function __GET($type)
       {
            // Check whether this particular config instance has already been created
            if (isset($this->configinstances[$type]))
            {
                return $this->configinstances[$type];
            }
            
            // Create a new instance of a config loader
            if ($type == "site")
            {
                $this->configinstances[$type] = new Wi3_Config(array("configdir" => Wi3::inst()->pathof->site."config/"));
                return $this->configinstances[$type];
            }
            else if ($type == "wi3" or $type == "app")
            {
                $this->configinstances[$type] = new Wi3_Config(array("configdir" => Wi3::inst()->pathof->wi3."config/"));
                return $this->configinstances[$type];
            }
        }
        
    }

?>