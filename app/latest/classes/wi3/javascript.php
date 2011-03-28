<?php defined('SYSPATH') OR die('No direct access allowed.');

class Wi3_Javascript extends Wi3_Base
{
	static protected $scripts = array();
    static protected $will_already_be_auto_rendered = false;

    //@input: $file should be an absolute URL to the javascript file
	static public function add($file, $category = "wi3")
	{
        if (is_array($file)) {
            foreach($file as $js) {
                self::add($js, $category);
            }
        } else {
            //if there is not yet an array for this category, create it
            if (!isset(self::$scripts[$category]))
                self::$scripts[$category] = Array();
            //if this file is not already present in this category, add it
            if (!in_array($file, self::$scripts[$category]))
                self::$scripts[$category][$file] = $file;
            //make sure the script tags are inserted in the header just before sending the page to the browser
            self::set_auto_render();
        }
	}

	static public function render($print = FALSE)
	{
		$output = '';
        //first, render the Wi3-scripts
        if (isset(self::$scripts["wi3"])) {
            foreach(self::$scripts["wi3"] as $script) {
                $output .= self::script($script);
            }
            unset(self::$scripts["wi3"]);
        }
        //then, render the Component-scripts
        if (isset(self::$scripts["component"])) {
            foreach(self::$scripts["component"] as $script) {
                $output .= self::script($script);
            }
            unset(self::$scripts["component"]);
        }
        //finally, render the user-page scripts and other scripts
		foreach (self::$scripts as $category => $filenames) {
            foreach($filenames as $script) {
                $output .= self::script($script);
            }
        }

		if ($print == true)
			echo $output;

		return $output;
	}
    
    static public function set_auto_render() {
        //add a hook to the system.display Event. This event is called just before flushing content to the browser
        //only add the hook if we didn't set the hook already sometime earlier
        if (self::$will_already_be_auto_rendered == false) {
            //add before the page is cached, so that javascript files are cached as well
            Event::instance('wi3.afterexecution.addcontent.javascript')->callback(array('Wi3_Javascript','render_in_head'));
            self::$will_already_be_auto_rendered = true;
        }
    }
    
    static public function render_in_head() {
        //insert the script tags just before the </head> tag
        //The to be flushed data is found in Event::$data
        Request::instance()->response = str_replace("</head>", self::render() . " </head>", Request::instance()->response);
    }
    
    /**
	 * Creates a script link.
	 *
	 * @param   string|array  filename
	 * @param   boolean       include the index_page in the link
	 * @return  string
	 */
	public static function script($script, $index = FALSE)
	{
        return '<script type="text/javascript" src="'.$script.'"></script>';
	}
    
}