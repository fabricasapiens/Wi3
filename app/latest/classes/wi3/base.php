<?php defined('SYSPATH') or die('No direct script access.');

    // Class makes sure that extending objects have the instance() function (and its alias inst())
    // That return an instance of the object. Instances can be given names, and constructparameters
    class Wi3_Base
    {
        
        // Object and array that keep the instances of this class
        public static $_instance = NULL;
        public static $_instances = Array();
        
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
                    // Create new instance (with the constructparameters, if any)
                    if ($constructparameters !== NULL) 
                    {
                        $new = new static($constructparameters);
                        static::$_instance = & $new; // Pass by reference, so that working with an instance updates back to here (so that the next instance() call will have the updated object)
                    } 
                    else 
                    {
                        $new = new static;
                        static::$_instance = & $new; // Pass by reference
                    }
                }
                return static::$_instance;
            } 
            else 
            {
                if (!isset(static::$_instances[$name]) OR !(static::$_instances[$name] instanceof static))
                {
                    // Create new instance (with the constructparameters, if any)
                    if ($constructparameters !== NULL) 
                    {
                        $new = new static($constructparameters);
                        static::$_instances[$name] = & $new; // Pass by reference, so that working with an instance updates back to here (so that the next instance() call will have the updated object)
                    } 
                    else 
                    {
                        $new = new static;
                        static::$_instances[$name] = & $new; // Pass by reference
                    }
                }
                return static::$_instances[$name];
            }
        }
    }

?>
