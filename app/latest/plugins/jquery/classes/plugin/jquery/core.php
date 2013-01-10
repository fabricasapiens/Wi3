<?php
    
    //extending with Wi3_Plugin makes the plugin register itself the very moment an instance is created
    Class Plugin_jquery_core extends Wi3_Baseplugin { 
        
        function __construct() {
            //register this Plugin
            parent::__construct();
            
            //make JQuery work
            $this->javascript(array(
                //'http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js',
                'jquery-1.8.1.min.js', //jquery core
                'jquery.base64.js', // base64 support. Belongs to core
            )); 

        }
        
    }

?>
