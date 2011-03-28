<?php
    
    //extending with Wi3_Plugin makes the plugin register itself the very moment an instance is created
    Class Plugin_jquery_tree extends Wi3_Baseplugin { 
        
        //requires core JQuery
        public $wi3_dependencies_plugins = array("Plugin_jquery_core");
        
        function __construct() {
            //register this Plugin
            parent::__construct();
            
            $this->javascript("jquery.simple.tree.js"); //for tree displaying plus drag&drop
            
        }
        
    }

?>