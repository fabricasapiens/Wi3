<?php defined('SYSPATH') or die('No direct script access.');
 
 // This class extends Kohana_View, and alters the 'render' method to enable for '$this' object scope resolution
 // If the 'this' variable is set to an object, then this object is called via $object->capture(...) to get the rendered result
class View extends Kohana_View {
    
    // Array of global variables
	public static $_global_data = array();

	/**
	 * Renders the view object to a string. Global and local data are merged
	 * and extracted to create local variables within the view file.
	 *
	 *     $output = $view->render();
	 *
	 * [!!] Global variables with the same key name as local variables will be
	 * overwritten by the local variable.
	 *
	 * @param    string  view filename
	 * @return   string
	 * @throws   Kohana_View_Exception
	 * @uses     View::capture
	 */
	public function render($file = NULL)
	{
		if ($file !== NULL)
		{
			$this->set_filename($file);
		}

		if (empty($this->_file))
		{
			throw new Kohana_View_Exception('You must set the file to use within your view before rendering');
		}

		// Combine local and global data and capture the output
        // Capture is dependent on the 'this' parameter
        // If it is present, then render this view through that object
        If (isset($this->this) AND !empty($this->this) AND is_object($this->this))
        {
            return $this->this->capture($this->_file, $this->_data);
        }
        else 
        {
            return View::capture($this->_file, $this->_data);
        }
	}
	
	public function set_filename($file) 
	{	
	    // Try to get translated view
		try 
		{
			return parent::set_filename("i18n/".i18n::lang()."/".$file);
		}
		catch(Exception $e) 
		{
		    //  Try to get translated view for the overall language, disregarding area suffix (i.e. 'en' from 'en-gb') 
		    try 
		    {
			    return parent::set_filename("i18n/".substr(i18n::lang(),0,2)."/".$file);
		    }
		    catch(Exception $e) 
		    {
			    return parent::set_filename($file);
		    }
		}
	}
    
    public function set_filepath($filepath)
    {
        // Get path for possible translations
        if (strrpos($filepath, "/") !== FALSE)
        {
            $split = strrpos($filepath, "/");
            $firstpart = substr($filepath, 0, $split+1); // The +1 to catch the /
            $lastpart = substr($filepath, $split+1);
        }
        else
        {
            $firstpart = "";
            $lastpart = $filepath;
        }
        // Check whether translation exists, and load it if so
        if (file_exists($firstpart . "i18n/".i18n::lang()."/".$lastpart)) 
        {
            $this->_file = $firstpart . "i18n/".i18n::lang()."/".$lastpart;
        }
        elseif (file_exists($firstpart . "i18n/".substr(i18n::lang(),0,2)."/".$lastpart)) 
        {
            $this->_file = $firstpart . "i18n/".substr(i18n::lang(),0,2)."/".$lastpart;
        }
        else
        {
            $this->_file = $filepath;
        }
    }

} // End View
