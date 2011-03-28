<?php

    Class Plugin_example extends Wi3_Plugin {
        
        public $dependencies_plugins = array("Plugin_jquery_1_3_2"); //this plugin needs the jquery 1.3.2 plugin
        
        public function __construct() {
            parent::__construct();
        }
        
        //this register function is called by the hook 'register.php'
        //to create an instance of the plugin anytime Wi3 runs
        //you could choose to not do so and 'wait' untill either 
        // a) the plugin is loaded trough dependencies or with Wi3::$plugins->load("plugin_example") or 
        // b) some code calls Wi3::$plugins->Plugin_example after which an instance will be created
        public static function register() {
            //through Wi3_Plugin by which this class is extended, the creation of an instance will register this instance with Wi3::$plugins and will make sure there is only one instance of this plugin at any time
            new Plugin_example();
        }
        
        public function test() {
            echo "test";
        }
      
    }

?>