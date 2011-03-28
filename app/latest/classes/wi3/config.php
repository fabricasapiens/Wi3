<?php defined('SYSPATH') or die('No direct script access.');

    class Wi3_Config extends Wi3_Base implements Iterator, Countable
    {
        
       public $configdata; // Ready to use data
       public $configfile; // File that should contain the configdata
       public $configdir; // Dir that should contain configfiles
       
       public function __construct($settings = array())
       {
            foreach($settings as $key => $val)
            {
                $this->{$key} = $val;
            }
        }
       
       public function __GET($key)
       {
            if (empty($this->configdata) AND empty($this->configfile) AND !empty($this->configdir))
            {
                // only directory is known. 
                // Check whether a file exists with name $key
                if (is_file($this->configdir.$key.".php"))
                {
                    // Return a Wi3_config that contains the correct file
                    return new self(array("configfile" => $this->configdir.$key.".php"));
                } 
                else if(is_dir($this->configdir.$key))
                {
                    // Return wi3_config that contains a deeper directory
                    return new self(array("configdir" => $this->configdir.$key));
                }
                // Otherwise this doesn't work
                throw new Exception("Config file is not available");
            }
            else if (empty($this->configdata) AND !empty($this->configfile))
            {
                // The config data is in file. Load it.
                $this->configdata = $this->loadconfigfile($this->configfile);
                // Now return the corect key in the configarray
                return $this->__GET($key); // Will call __GET again
            }
            else if (!empty($this->configdata) AND is_array($this->configdata))
            {
                if (is_scalar($this->configdata[$key])) // Scalar = string, int, float or boolean
                {
                    // Just return the string
                    return $this->configdata[$key];
                }
                else if (is_array($this->configdata[$key]))
                {
                    // Return a new config file with the array set
                    $this->configdata[$key] = new self(array("configdata" => $this->configdata[$key])); //replace the key in the config-array with a config-object to speed up future calls
                    return $this->configdata[$key];
                }
                else if(is_object($this->configdata[$key]))
                {
                    // Return the object (which might be an Wi3_Config that was previously created
                    return $this->configdata[$key];
                }
            }
        }
        
        public function loadconfigfile($filename)
        {
            return include($filename);
        }
        
        // Countable function
        public function count()
        {
            return count($this->configdata);
        }
        
        // Iterator functions
        public function __ISSET($key)
        {
            return isset($this->configdata[$key]);
        }
        
        public function __UNSET($key)
        {
            unset($this->configdata[$key]);
        }
        
        public function rewind() {
            reset($this->configdata);
        }

        public function current() {
            $var = current($this->configdata);
            if ($var === FALSE) { return FALSE; } // this will stop the loop
            //return new self(array("configdata"=>$var));
            return $this->{key($this->configdata)};
        }

        public function key() {
            $var = key($this->configdata);
            return $var;
        }

        public function next() {
            $var = next($this->configdata);
            return $var;
        }

        public function valid() {
            $var = $this->current() !== false;
            return $var;
        }
        
    }

?>