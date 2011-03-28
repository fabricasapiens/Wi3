<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Kohana interface for Wi3
 * @package Wi3
 * @author	Willem Mulder
 */
 
 // This class covers wi3-kohana-specific functions
class Wi3_Kohana extends Wi3_Base
{
    public function addmodule($path)
    {
        // The Kohana::modules() function sets the module-paths, and loads the init.php files with include_once()
        // The include_once() functions ensures that every init.php is only executed once
        Kohana::modules(Kohana::modules() + Array($path => $path));
    }
    
    public function addmodules($array)
    {
        // The Kohana::modules() function sets the module-paths, and loads the init.php files with include_once()
        // The include_once() functions ensures that every init.php is only executed once
        Kohana::modules(Kohana::modules() + $array);
    }
}
    
?>
