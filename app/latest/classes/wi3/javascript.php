<?php defined('SYSPATH') OR die('No direct access allowed.');

class Wi3_Javascript extends Wi3_Base
{
	static protected $scripts = array();
    static protected $will_already_be_auto_rendered = false;

    //@param file $file should be an absolute URL to the javascript file OR a bunch of raw JS that should be on the page (see externalfile param)
    //@param externalfile indicates whether $file is a URL or raw JS
	static public function add($file, $category = "wi3", $externalfile=true)
	{
        if (is_array($file)) {
            foreach($file as $js) {
                self::add($js, $category, $externalfile);
            }
        } else {
            //if there is not yet an array for this category, create it
            if (!isset(self::$scripts[$category])) {
                self::$scripts[$category] = Array();
            }
            //if this file is not already present in this category, add it
            $id = md5($file."_".($externalfile?"true":"false"));
            if (!in_array($id, self::$scripts[$category])) {
                if ($externalfile) {
                    self::$scripts[$category][$id] = Array("url" => $file);
                } else {
                    self::$scripts[$category][$id] = Array("javascript" => $file);
                }
            }
            //make sure the script tags are inserted in the header just before sending the page to the browser
            self::set_auto_render();
        }
	}

	static public function render($print = FALSE)
	{
		$output = '';
        //first, render the Wi3-scripts
        if (isset(self::$scripts["wi3"])) {
            foreach(self::$scripts["wi3"] as $info) {
                $output .= self::script($info);
            }
            unset(self::$scripts["wi3"]);
        }
        //then, render the Component-scripts
        if (isset(self::$scripts["component"])) {
            foreach(self::$scripts["component"] as $info) {
                $output .= self::script($info);
            }
            unset(self::$scripts["component"]);
        }
        //finally, render the user-page scripts and other scripts
		foreach (self::$scripts as $category => $infoarray) {
            foreach($infoarray as $info) {
                $output .= self::script($info);
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
	 * @param   array    description of the javascript to be inserted
	 * @param   boolean       include the index_page in the link
	 * @return  string
	 */
	public static function script($info, $index = FALSE)
	{
        if (isset($info["url"])) {
            return '<script type="text/javascript" src="' . $info["url"] . '"></script>';
        } else if (isset($info["javascript"])) {
            return '<script type="text/javascript">' . $info["javascript"] . '</script>';
        }
	}
    
}