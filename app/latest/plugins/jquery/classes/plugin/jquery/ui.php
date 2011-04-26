<?php
    
    //extending with Wi3_Plugin makes the plugin register itself the very moment an instance is created
    Class Plugin_jquery_ui extends Wi3_Baseplugin { 
        
        //UI requires core JQuery
        public $wi3_dependencies_plugins = array("Plugin_jquery_core");
        
        function __construct() {
            //register this Plugin and load dependencies
            parent::__construct();
            
            //load JQuery UI
            $this->javascript('jquery-ui-1.8.12.custom.min.js'); 
            
            //load the UI css as well
            $this->css("jquery_ui/jquery-ui-1.8.12.custom.css");
            
        }
        
    }

?>
