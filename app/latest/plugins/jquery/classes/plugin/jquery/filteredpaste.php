<?php
    
    // Extending with Wi3_Baseplugin makes the plugin register itself the very moment an instance is created
    // Once registred, it is known that this Plugin is already loaded and will not be loaded twice
    // It also handles dependency management
    Class Plugin_jquery_filteredpaste extends Wi3_Baseplugin { 
        
        //UI requires core JQuery
        public $wi3_dependencies_plugins = array("Plugin_jquery_core");
        
        function __construct() {
            //register this Plugin and load dependencies
            parent::__construct();
            
            //load the javascript
            $this->javascript("jquery-filteredPaste.js");
        }
        
    }

?>