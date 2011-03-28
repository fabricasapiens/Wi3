<?php defined('SYSPATH') OR die('No direct access allowed.');

class Wi3_Css extends Wi3_Base
{
	static protected $files = array();
    static protected $will_already_be_auto_rendered = false;

    //@input: $file should be an absolute URL to the javascript file
	static public function add($file, $category = "wi3")
	{
        if (is_array($file)) {
            foreach($file as $css) {
                self::add($css, $category);
            }
        } else {
            //if there is not yet an array for this category, create it
            if (!isset(self::$files[$category]))
                self::$files[$category] = Array();
            //if this file is not already present in this category, add it
            if (!in_array($file, self::$files[$category]))
                self::$files[$category][$file] = $file;
            //make sure the script tags are inserted in the header just before sending the page to the browser
            self::set_auto_render();
        }
	}

	static public function render($print = FALSE)
	{
		$output = '';
        //first, render the Wi3-scripts
        if (isset(self::$files["wi3"])) {
            foreach(self::$files["wi3"] as $script) {
                $output .= self::link($script);
            }
            unset(self::$files["wi3"]);
        }
        //then, render the Component-scripts
        if (isset(self::$files["component"])) {
            foreach(self::$files["component"] as $script) {
                $output .= self::link($script);
            }
            unset(self::$files["component"]);
        }
        //finally, render the user-page scripts and other scripts
		foreach (self::$files as $category => $filenames) {
            foreach($filenames as $script) {
                $output .= self::link($script);
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
            //add before the page is cached, so that css files are cached as well
            Event::instance('wi3.afterexecution.addcontent.css')->callback(array('Wi3_Css','render_in_head'));
            self::$will_already_be_auto_rendered = true;
        }
    }
    
    static public function render_in_head() {
        //insert the script tags just before the </head> tag
        //The to be flushed data is found in Event::$data
        //preferably, the CSS gets before the <script> tags, so it gets loaded first
        $headpos = strpos(Request::instance()->response, "<head>");
        if ($headpos > 0) {
            $scriptpos = strpos(Request::instance()->response, "<script ", $headpos);
            if ($scriptpos > 0) {
                //$temp = Event::$data;
                //Event::$data = substr($temp, 0, $scriptpos) . self::render() . substr($temp, $scriptpos);
                Request::instance()->response = str_replace("</head>", self::render() . " </head>", Request::instance()->response);
                return Request::instance()->response;
            }
        }
        Request::instance()->response= str_replace("</head>", self::render() . " </head>", Request::instance()->response);
    }
    
    /**
	 * Creates a link tag.
	 *
	 * @param   string|array  filename
	 * @param   string|array  relationship
	 * @param   string|array  mimetype
	 * @param   string        specifies suffix of the file
	 * @param   string|array  specifies on what device the document will be displayed
	 * @param   boolean       include the index_page in the link
	 * @return  string
	 */
	public static function link($href, $index=FALSE)
	{
        $attr = array
        (
            'type' => "text/css",
            'media' => 'all',
            'rel' => "stylesheet",
            'href' => $href,
        );
        
        return '<link '.html::attributes($attr).' />';
	}
    
}