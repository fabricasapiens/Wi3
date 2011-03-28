<?php

    class Wi3_plugins {
        
        // Stores all instances of the plugins
        public $plugins = Array();
        
        public function __construct()
        {
            // TODO: might load plugins dynamically via Wi3::inst()->kohana->addmodule()
            // For now, just load in the bootstrap.php to speed things up
        }
        
        //----------------------------
        // method for registering plugins, as available through Wi3::$plugins
        //----------------------------
        public function register($pluginname, $class = "") 
        {
            if (empty($class)) { $class = $pluginname; }
            if (is_string($class)) 
            {
                $class = ucfirst($class);
                //create an instance if a string (classname) is provided
                //by creating the class, it will register itself if it is extending Wi3_Plugin
                new $class();
            } else {
                //add the plugin to the plugins array
                $this->plugins[$pluginname] = $class;
            }
        }
        
        //----------------------------
        // function to register a plugin if it is not already registered
        //----------------------------
        public function registeronce($pluginname, $class = "") 
        {
           if (!isset($this->plugins[ucfirst($pluginname)])) 
           {
                $pluginname = ucfirst($pluginname);
                $this->register($pluginname, $class);
            }
        }
        
        //----------------------------
        // static function to 'require' a plugin, so to make it loaded
        // the static function is important, so it can be loaded in Event::instance('some')->callback(array('Wi3_Plugins', 'load'), 'someplugin)
        //----------------------------
        public static function load($pluginname) 
        {
            Wi3::inst()->plugins->registeronce($pluginname);
        }
        
        //----------------------------
        // magic methods for retrieving and setting plugins
        //----------------------------
        public function __GET($pluginname) 
        {
            //is called when someone wants to have the plugin
            //return the instance of the plugin from our plugins-array
            //first, create an instance if there is not already one
            $pluginname = ucfirst($pluginname);
            $this->registeronce($pluginname, $pluginname);
            return $this->plugins[$pluginname];
        }
        
        public function __SET($pluginname, $val) 
        {
            //well, this probably does not happen that often, but is supported anyway
            return $plugins[$pluginname] = $val;
        }
        
    }

?>