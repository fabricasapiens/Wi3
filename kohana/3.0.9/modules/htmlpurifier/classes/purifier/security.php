<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Modifies Kohana to use [HTML Purifier](http://htmlpurifier.org/) for the
 * [Security::xss_clean] method.
 *
 * @package    Purifier
 * @category   Security
 * @author     Kohana Team
 * @copyright  (c) 2010 Woody Gilk
 * @license    BSD
 */
class Purifier_Security extends Kohana_Security {

	/**
	 * @var  HTMLPurifier  singleton instance of the HTML Purifier object
	 */
	protected static $htmlpurifier;
	
	/**
	 * @var  HTMLPurifierconfig  config to be used for the next instantiation of HTMLPurifier
	 */
	protected static $htmlpurifierconfig = False;

	/**
	 * Returns the singleton instance of HTML Purifier. If no instance has
	 * been created, a new instance will be created. Configuration options
	 * for HTML Purifier can be set in `APPPATH/config/purifier.php` in the
	 * "settings" key.
	 *
	 *     $purifier = Security::htmlpurifier();
	 *
	 * @return  HTMLPurifier
	 */
	public static function htmlpurifier()
	{
		if ( ! Security::$htmlpurifier)
		{
			if (Kohana::config('purifier.preload'))
			{
				// Load the all of HTML Purifier right now.
				// This increases performance with a slight hit to memory usage.
				require_once Kohana::find_file('vendor', 'htmlpurifier/library/HTMLPurifier.includes');
			}

			// Load the HTML Purifier auto loader
			require_once Kohana::find_file('vendor', 'htmlpurifier/library/HTMLPurifier.auto');

			// Create a new configuration object, or load it if there is already one set
			if (Security::$htmlpurifierconfig)
			{
			    $config = Security::$htmlpurifierconfig;
			}
			else
			{
			    $config = HTMLPurifier_Config::createDefault();
			    $config->autoFinalize = false; // To allow for later changes to the config

			    if (is_array($settings = Kohana::config('purifier.settings')))
			    {
				    // Load the settings
				    $config->loadArray($settings);
			    }
			
			    // Save configuration for later use
			    Security::$htmlpurifierconfig = $config;
		    }

			// Configure additional options
			$config = Security::configure($config);

			// Create the purifier instance
			Security::$htmlpurifier = new HTMLPurifier($config);
		}

		return Security::$htmlpurifier;
	}
	
	/**
	 * Adds an element to the allowedElements list
	 *
	 *  Security::addpurifierelement("cms", Array("attributes" => Array("name" => "Text")));
	 *
	 * @param   elementname elementname to add to the allowedelements
	 * @param   elementconfig   array with config options for the new element; currently only 'attributes' are supported
	 */
	 public static function addpurifierelement($elementname, $elementconfig = Array())
	 {
	    // Create a new configuration object, or load it if there is already one set
		if (Security::$htmlpurifierconfig != False)
		{
		    $config = Security::$htmlpurifierconfig;
		}
		else
		{
		    $config = HTMLPurifier_Config::createDefault();
		    $config->autoFinalize = false; // To allow for later changes to the config

		    if (is_array($settings = Kohana::config('purifier.settings')))
		    {
			    // Load the settings
			    $config->loadArray($settings);
		    }
	    }
		
		if (!isset($elementconfig["attributes"]) OR !is_array($elementconfig["attributes"]))
		{
		    $elementconfig["attributes"] = Array();
		}
        
        $config->set('Core.Encoding', "UTF-8");
        $config->set('HTML.DefinitionID', 'cms-specific');
        $config->set('Cache.DefinitionImpl', null); // Do not use caching
        
        $def = $config->getHTMLDefinition(true);
        $element = $def->addElement(
          $elementname,   // name
          'Inline',  // content set
          'Flow', // allowed children
          'Common', // attribute collection
           $elementconfig["attributes"]
        );
	    // Save configuration for later use
	    Security::$htmlpurifierconfig = $config;
	 }

	/**
	 * Modifies the configuration before creating a HTML Purifier instance.
	 *
	 * [!!] You must create an extension and overload this method to use it.
	 *
	 * @param   HTMLPurifier_Config  configuration object
	 * @return  HTMLPurifier_Config
	 */
	public static function configure(HTMLPurifier_Config $config)
	{
		return $config;
	}

	/**
	 * Removes broken HTML and XSS from text using [HTMLPurifier](http://htmlpurifier.org/).
	 *
	 *     $text = Security::xss_clean(Arr::get($_POST, 'message'));
	 *
	 * The original content is returned with all broken HTML and XSS removed.
	 *
	 * @param   mixed   text to clean, or an array to clean recursively
	 * @return  mixed
	 */
	public static function xss_clean($str)
	{
		if (is_array($str))
		{
			foreach ($str as $i => $s)
			{
				// Recursively clean arrays
				$str[$i] = Security::xss_clean($s);
			}

			return $str;
		}

		// Load HTML Purifier
		$purifier = Security::htmlpurifier();

		// Clean the HTML and return it
		return $purifier->purify($str);
	}

} // End Purifier Security
