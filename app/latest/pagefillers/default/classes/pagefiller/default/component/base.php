<?php

    Class Pagefiller_Default_Component_Base extends Wi3_Base 
    {
    
        public static function view($viewname)
        {
            $componentname = self::get_calling_componentname();
            // Make this component base view extend the base template, with their locations set to the component folders
            $componenturl = Wi3::inst()->urlof->pagefillerfiles("default") . "components/" . $componentname . "/";
            $componentpath = Wi3::inst()->pathof->pagefiller("default") . "components/" . $componentname . "/";
            $componentbaseview = Wi3_Baseview::instance('pagefillercomponentbaseview'.$componentname, array(
                'javascript_url' => $componenturl.'static/javascript/', 
                'javascript_path' => $componentpath.'static/javascript/',
                'css_url' => $componenturl.'static/css/',
                'css_path' => $componentpath.'static/css/',
                'view_path' => $componentpath.'views/',
            )); 
            $componentview = View::factory()->set("this", $componentbaseview);
            $componentview->set_filepath($componentpath.'views/'.$viewname.EXT); // set_filepath sets a complete filename on the View
            return $componentview;
        }
        
        
        public static function css($filename)
        {
            $componentname = self::get_calling_componentname();
            Wi3::inst()->css->add(Wi3::inst()->urlof->pagefillerfiles("default") . "components/" . $componentname . "/" . "static/css/" . $filename, "view");
        }
        
        
        public static function javascript($filename)
        {
            $componentname = self::get_calling_componentname();
            Wi3::inst()->javascript->add(Wi3::inst()->urlof->pagefillerfiles("default") . "components/" . $componentname . "/" . "static/javascript/" . $filename, "view");
        }
        
        private static function get_calling_componentname() {
            $callingClass = get_called_class(); // Uses late static binding
            $callingClassLastpart = substr($callingClass, strrpos($callingClass, "_")+1);
            return $callingClassLastpart;
        }
        
    }
    
?>
