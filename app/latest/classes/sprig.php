<?php defined('SYSPATH') or die('No direct script access.');

abstract class Sprig extends Sprig_Core 
{

    protected $_db = "global"; // This is overriden by classes like site_site, site_page and site_user which will use the 'site' db
  
    public static $_usedb = "";
    
    protected function __construct() 
    {        
        // Set the $_db to the static $_usedb
        // The static $_usedb thus determines which db future Sprig instances will have
        // Example: Sprig_Wi3::$_usedb = Database::instance("name", $config); $obj = Sprig_Wi3::factory(""); ... more Sprig_Wi3 models
        if (!empty(self::$_usedb))
        {
            $this->_db = self::$_usedb;
        }
        // Now construct the Sprig
        return parent::__construct();
    }
        
    static public function usedb($db) 
    {
        self::$_usedb = $db;
    }
    
    // set a value in a chainable manner
    public function set($key, $val) 
    {
        $this->__set($key, $val);
        return $this;
    }
    
}