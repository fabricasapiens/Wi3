<?php defined('SYSPATH') or die('No direct script access.');

    class Wi3_Pathof extends Wi3_Base 
    {
        
        public $wi3;
        public $app;
        public $site;
        public $sitetemplates;
        public $wi3templates;
        public $pagetemplate;
        public $pagefiller;
        
        public function __construct() 
        {
            $this->wi3 = $this->app = APPPATH;
            //site and pagefiller location can only be known after site has been loaded by Engine or View
            //we therefore register an hook on the wi3.siteandpageloaded event
            Event::instance("wi3.init.sitearea.site.loaded")->callback(array("Wi3_pathof", "fill_site_paths"));
            Event::instance("wi3.init.page.loaded")->callback(array("Wi3_pathof", "fill_page_paths"));
        }
        
        // Static function, because it is called from an Event callback
        public static function fill_site_paths() 
        {
            Wi3::inst()->pathof->site = APPPATH . "../../sites/" . Wi3::inst()->sitearea->site->name . "/";
        }
        
        public static function fill_page_paths() 
        {
            Wi3::$pathof->pagetemplate = Wi3::$pathof->sitetemplates = Wi3::$pathof->wi3templates = Wi3::$pathof->site . "page_templates/";
            if (isset(Wi3::inst()->sitearea->page)) 
            {
                Wi3::inst()->pathof->pagefiller = APPPATH . "pagefillers/" . Wi3::inst()->sitearea->page . "/";
            }
        }
        
        public static function pagefiller($pagefiller)
        {
            if (empty($pagefiller))
            {
                return Wi3::inst()->pathof->pagefiller;
            }
            else
            {
                 return APPPATH . "pagefillers/" . $pagefiller . "/";
            }
        }
        
        
    }

?>
