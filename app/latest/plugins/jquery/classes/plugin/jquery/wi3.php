<?php
    
    //extending with Wi3_Plugin makes the plugin register itself the very moment an instance is created
    Class Plugin_jquery_wi3 extends Wi3_Baseplugin { 
        
        public $wi3_dependencies_plugins = array("Plugin_jquery_core", "Plugin_clientjavascriptvars", "Plugin_jquery_fancybox");
        
        function __construct() {
            //register this Plugin
            parent::__construct();
            
            Wi3::inst()->baseview_adminarea->javascript(array(
                    'wi3.js', //enables certain basic Javascript functions like clientside client<>server communication, tinymce initialization and popups
            )); 
            
        }
        
    }

?>
