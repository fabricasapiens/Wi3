<?php
    
    //extending with Wi3_Plugin makes the plugin register itself the very moment an instance is created
    Class Plugin_jquery_jqtransform extends Wi3_Baseplugin { 
        
        //UI requires core JQuery
        public $wi3_dependencies_plugins = array("Plugin_jquery_core", "Plugin_jquery_easing");
        
        function __construct() {
            //register this Plugin and load dependencies
            parent::__construct();
            
            //load the codaslider css
            $this->css("jqtransform/jqtransform.css");
            
            //load the javascript
            $this->javascript("jquery.jqtransform.js");
            
        }
        
    }

?>