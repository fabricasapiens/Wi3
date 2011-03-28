<?php defined('SYSPATH') or die('No direct script access.');

    class Wi3_Urlof extends Wi3_Base
    {
        
        public $request;    // Current url, same as Wi3::$routing->url
        
        public $baseurl; // URL after which pagenames, _wi3files, _wi3controller, _sitefiles etc can be appended
        
        public $appfiles; // Alias for $this->wi3files
        public $wi3files;    // Url to wi3 folder directly. (not wi3 controllers!)
        public $appcontrollers; // Alias
        public $wi3controllers; // Url to wi3 controllers (not wi3 files!)
        public $controller; // url to current controller
        public $action;  // url to current action
        
        public $site;   // Base-url to the site
        public $sitefiles; // To get to the files of the site. This involves a _sitefiles part in the URL, so that not the sitearea/view controller is loaded (as with a normal url), but files are loaded directly
        public $page;   // Url to the current page
        
        public $pagefillerfiles; // Url to pagefiller files
        
        //----------------------------------------
        // construct
        //----------------------------------------
        public function __construct() {
            $this->request = Wi3::instance()->routing->url; // Url of request, as found in routing
            
            // base URL determination of the *unrewritten* site URL
            // First get the part of the *rewritten* url that comes after $controller/$action (e.g. "pagename/arg1/arg2" after "sitearea/view")
            // Rewritten URL might be: .../sitearea/view/pagename/arg1 OR .../adminarea/ 
            // (There is NOT NECESSARILY an action after the controller: Do a check for that!)
            if (strpos(Wi3::inst()->routing->filename, "app/index.php/".Wi3::inst()->routing->controller."/".Wi3::inst()->routing->action) !== FALSE)
            {
                // There is an action part present
                $pagepart = substr(Wi3::inst()->routing->filename, (strpos(Wi3::inst()->routing->filename, "app/index.php/".Wi3::inst()->routing->controller."/".Wi3::inst()->routing->action)+strlen("app/index.php/".Wi3::inst()->routing->controller."/".Wi3::inst()->routing->action)) );
            }
            else 
            {
                // There is only the controller and NOT an action part 
                $pagepart = substr(Wi3::inst()->routing->filename, (strpos(Wi3::inst()->routing->filename, "app/index.php/".Wi3::inst()->routing->controller)+strlen("app/index.php/".Wi3::inst()->routing->controller)) );
            }
            if (!empty($pagepart)) {
                // That pagepart is also available in the *unrewritten* URL, at  the very end of the URL
                // The part before it is our 'base' that we want to fetch
                $baseurl = substr(Wi3::inst()->routing->url, 0, (strlen(Wi3::inst()->routing->url) - strlen($pagepart))) . "/";
                    // OR alternatively: $basepart = substr(Wi3::inst()->routing->url, 0, strrpos(Wi3::inst()->routing->url, $pagepart));
            }
            else
            {
                // There is no pagepart, so only a baseurl (should in reality not occur, but anyways)
                $baseurl = Wi3::inst()->routing->url;
            }
            
            // Ensure a slash at the end of the baseurl
            $baseurl = trim($baseurl, "/") . "/";
                
            // The baseurl is now something like http://domain.com/(folder/)adminarea/ or http://domain.com/(folder/)_wi3controller/somecontroller or http://comain.com/(folder/)pagename
            // We however do not want parts like adminarea or _wi3controller in the base url
            // Mind: we do not need to check for things like _wi3files because these URLs will/should not resolve here, but get a file directly
            if (strpos($baseurl, "/_wi3controllers/") !== FALSE)
            {
                $this->baseurl = substr($baseurl, 0, strpos($baseurl, "/_wi3controllers/")+1);
            }
            else if (strpos($baseurl, "/adminarea/") !== FALSE)
            {
                $this->baseurl = substr($baseurl, 0, strpos($baseurl, "/adminarea/")+1);
            }
            else if (strpos($baseurl, "/superadminarea/") !== FALSE)
            {
                $this->baseurl = substr($baseurl, 0, strpos($baseurl, "/superadminarea/")+1);
            }
            
            else
            {
                $this->baseurl = $baseurl; //substr($baseurl, 0, -1);
            }
            
            
            // Now set the different 'urlof' variables
            $this->appfiles = $this->wi3files = $this->baseurl . "_wi3files/";
            $this->appcontrollers = $this->wi3controllers = $this->baseurl . "_wi3controllers/";
            // Adminarea can shorthandedly be accessed like domain.com/something/adminarea/action
            // This also goes for /adminarea_menu_ajax etc
            // Take that into account
            if (strpos(Wi3::inst()->routing->controller, "adminarea") === 0 OR strpos(Wi3::inst()->routing->controller, "superadminarea") === 0 )
            {
                $this->controller =  $this->baseurl . Wi3::inst()->routing->controller . "/";
            }
            else
            {
                $this->controller = $this->controller(Wi3::inst()->routing->controller);
            }
            $this->action = $this->action(Wi3::inst()->routing->action);
           
            //site and pagefiller location can only be known after site and page have been loaded by Engine or View
            //we therefore register an hook on the wi3.siteandpageloaded event
            Event::instance("wi3.init.sitearea.site.loaded")->callback(array("Wi3_urlof", "fillsite"));
            Event::instance("wi3.init.sitearea.page.loaded")->callback(array("Wi3_urlof", "fillpageandpagefiller"));
        }
        
        //----------------------------------------
        // functions that create urls on the fly
        //----------------------------------------
        public function controller($controller = NULL)
        {
            if (empty($controller))
            {
                $controller = Wi3::inst()->routing->controller;
            }
            if ($controller == "adminarea")
            {
                return $this->baseurl . $controller . "/";
            }
            else 
            {
                return $this->wi3controllers.$controller."/";
            }
        }
        
        public function action($controller, $action = NULL)
        {
            // If just $controller is set, then in fact only the action is set, and the controller is assumed to be the current one
            if ($action === NULL) 
            {
                $action = $controller;
                $controller = Wi3::inst()->routing->controller;
            }
            return $this->controller($controller).$action."/";
        }
        
        public function page($page) 
        {
            // Return the url to the page, dependent on whether the user is in edit_mode or not
            if (Wi3::inst()->routing->controller == "adminarea")
            {
                return $this->action("adminarea", "content_edit") . (is_object($page) ? $page->slug : $page);
            }
            else
            {
                return $this->site . (is_object($page) ? $page->slug : $page);
            }
        }
        
        public function file($file) 
        {
            return $this->site . "_uploads/" . (is_object($file) ? $file->slug : $file);
        }
        
        public function image($file, $xsize=-1) 
        {
            return $this->site . "_uploads/" . ($xsize != -1 ? $xsize . "/" : "") . basename((is_object($file) ? $file->url : $file));
        }
        
        public static function fillsite() 
        {
            Wi3::inst()->urlof->site = Wi3::inst()->urlof->baseurl;
            // To get to the files directly, use the _site part in the URL
            Wi3::inst()->urlof->sitefiles = Wi3::inst()->urlof->site."_sitefiles/";
        }
        
        public static function fillpageandpagefiller() 
        {
            Wi3::inst()->urlof->page = Wi3::inst()->urlof->site . Wi3::inst()->sitearea->page->slug . "/";
            Wi3::inst()->urlof->pagefillerfiles = Wi3::inst()->urlof->wi3files . "pagefillers/" . Wi3::inst()->sitearea->page->filler . "/";
        }
        
        public function pagefillerfiles($pagefiller)
        {
            return Wi3::inst()->urlof->wi3files . "pagefillers/" . $pagefiller . "/";
        }
        
        
    }

?>
