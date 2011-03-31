<?php defined('SYSPATH') or die('No direct script access.');

    // Class makes sure that extending objects have the instance() function (and its alias inst())
    // That return an instance of the object. Instances can be given names, and constructparameters
    class Wi3_Base
    {
        // Object and array that keep the instances of this class
        public static $_instances = Array();
        public static $_instance;
        
        // Function returns instance of this class
        // Instances can also be labeled with a name, to have multiple 'singleton' instances of a class
        public static function inst($name = NULL, $constructparameters = NULL)
        {
            return self::instance($name, $constructparameters);
        }
        public static function instance($name = NULL, $constructparameters = NULL) 
        {
            if ($name == NULL) {
                if (!(static::$_instance instanceof static)) // Caution: Late Static Binding, only to be found in PHP 5.3.0. 'static' refers to the calling Class that extends Wi3_Base
                {
                    $new = new static($constructparameters); // If no $constructparameters were sent along, then sending NULL will have the same effect, since isset($param) will in both cases return FALSE
                    static::$_instance = & $new; // Pass by reference, so that working with an instance updates back to here (so that the next instance() call will have the updated object)
                }
                return static::$_instance;
            } 
            else 
            {
                if (!isset(static::$_instances[$name]) OR !(static::$_instances[$name] instanceof static))
                {
                    $new = new static($constructparameters); // If no $constructparameters were sent along, then sending NULL will have the same effect, since isset($param) will in both cases return FALSE
                    static::$_instances[$name] = & $new; // Pass by reference, so that working with an instance updates back to here (so that the next instance() call will have the updated object)
                }
                return static::$_instances[$name];
            }
        }
    }
    
?>
