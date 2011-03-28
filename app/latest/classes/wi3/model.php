<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Model interface for Wi3
 * @package Wi3
 * @author	Willem Mulder
 */
 
 // This class provides an interface to constructing models
 // Throughout Wi3, one can expect models to have the API as used by Sprig (http://github.com/sittercity/sprig). 
// If there ever is a switch to another modelling system, this Model class will provide the conversions to that other modelling system
class Wi3_Model extends Wi3_Base
{
    function factory($name, array $values = NULL) {
         // Currently, we use Sprig for our models
        return Sprig::factory($name, $values);
    }
}
    
?>