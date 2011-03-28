<?php
    
    //extending with Wi3_Plugin makes the plugin register itself the very moment an instance is created
    Class Plugin_clientjavascriptvars extends Wi3_Baseplugin { 
        
        function __construct() {
            //register this Plugin
            parent::__construct();
            
            //-------------------------------------------------------------
            // this function ensures that some Wi3 information is always sent to the client for use in javascript files (ie kohana.js)
            //
            //set up the event to pass information to the clientside
            //add this event before the javascript event, so that javascript always have this information available when they load
            Event::instance('wi3.afterexecution.addcontent.javascript.variables')->callback(array("Plugin_clientjavascriptvars", "addclientjavascriptvars"));
            //-------------------------------------------------------------
            
        }
        
        //-------------------------------------------------------------
        // this function adds javascript client variables in the head of the page
        // is called from the System.display event, as set in the Wi3::setup
        //-------------------------------------------------------------
        public static function addclientjavascriptvars() {
            $information = array( 
                "routing" => Array(
                    "controller" => Wi3::inst()->routing->controller,
                    "action" => Wi3::inst()->routing->action,
                ),
                "urlof" => Array(
                    "wi3" => Wi3::inst()->urlof->wi3files,
                    "fileroot" => Wi3::inst()->urlof->wi3files, // Is the same as Wi3::inst()->urlof->wi3files
                    "controllerroot" => Wi3::inst()->urlof->wi3controllers, // Shortcut how sites can ask for a controller
                    "site" => Wi3::inst()->urlof->site,
                ),
                //"editmode" => Wi3::inst()->editmode,
            );
            Request::instance()->response = str_replace("</head>", "<script> var wi3 = " . json_encode($information) . "</script></head>", Request::instance()->response);
        }
        
    }

?>