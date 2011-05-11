<?php defined('SYSPATH') or die('No direct script access.');

    // The Wi3_Statickeywords_Defined gets created in the bootstrap, taking into account 
    Class Wi3 extends Wi3_Base {
        
        /** Introduction to this class **
        * This Wi3 class is initialized in the bootstrap.php right before the request is executed
        * Its classes are meant to serve as interface for all kind of Wi3 functions
        * However, these functions only touch sites and pages
        * Any functions 'lower' than pages (like for fields) should be included in the pagefiller
        * When any page requiers a pagefiller, its folder structure will be loaded into the find_path
        * And its init.php will be run. It is in that init.php that any routes should be set up and ie Wi3->fields can be registered
        *
        ** Some about users and areas **
        * There are some roles for users that determine which parts of Wi3 they can access: superadminlogin (in the global table), adminlogin (in the sitetables), sitelogin (in the sitetables)
        * Superadminlogin can access the superadminarea
        * Adminlogin can access the adminarea. It then depends on roles like admin_adminallusers, admin_adminallfiles, admin_adminallcontent if this admin has the power to change all content, all users, etc
        * Sitelogin can login to a site (the sitearea), but CANNOT login to the adminarea. Admin-logins and Site-logins are thus separated! An Admin without sitelogin can also not login to the site.
        */
        
        public $originalpost = Array(); // the original $_POST, before the XSS Clean
        public $originalget = Array(); // the original $_GET, before the XSS Clean
        
        public $globalvars = array(); // To save variables and make them statically available. E.g. set some data before an event is executed, and then retrieve the data afterwards
        
        //----------------------------------------
        // This init is called in bootstrap.php right before the actual request is executed. This init generates a Wi3 instance, which will load a bunch of Wi3 classes
        //----------------------------------------
        public function init() 
        { 
            // Load session handler
            $this->session = Session::instance();
            
            // Determine language
            $lang = Cookie::get('lang', 'nl-nl');
            if(!in_array($lang, array('nl-nl', 'en-us'))) {
               // check the allowed languages, and force the default
               $lang = 'nl-nl';
            }
            // set the target language
            i18n::lang($lang);
            
            // Load wi3-kohana-specific functions
            $this->kohana = new Wi3_Kohana;
            
            // XSS Clean all user input!
            // TODO: only do this if the user is not an admin...
            $this->originalpost = $_POST; // Save original $_POST
            foreach($_POST as $key => $val)
            {
                $_POST[$key] = Security::xss_clean($val);
            }
            $this->originalget = $_GET; // Save original $_GET    
            foreach($_GET as $key => $val)
            {
                $_GET[$key] = Security::xss_clean($val);
            }
            
            // Load some Wi3 classes
            
            // Load a global database configuration
            $this->database = new Wi3_Database; // Helper functions to create databases etc
            $this->globaldatabase = Wi3_Database::instance("global");
            Event::instance("wi3.init.globaldatabase.loaded")->execute();
            
            // Get routing, url and path information
            // These classes in turn add a callback to the wi3.init.site.loaded Event, after which they will update with path and urls to the site
            $this->routing = Wi3_Routing::instance();
            Event::instance("wi3.init.routing.loaded")->execute();
            $this->pathof = Wi3_Pathof::instance();
            Event::instance("wi3.init.pathof.loaded")->execute();
            $this->urlof = Wi3_Urlof::instance();
            Event::instance("wi3.init.urlof.loaded")->execute();
            
            // Load CSS and Javascript 'injectors'
            $this->css = Wi3_Css::instance();
            $this->javascript = Wi3_Javascript::instance();
            
            // Instantiate the Model class, that is an interface to the 'factory' method for any underlying model-systems
            $this->model = Wi3_Model::inst();
            Event::instance("wi3.init.model.loaded")->execute();
            
            // Instantiate the form-builder
            $this->formbuilder = Wi3_Formbuilder::inst();
            Event::instance("wi3.init.formbuilder.loaded")->execute();
            
            // Now find out what is the scope of this request
            // It most often is a site-scope (i.e. the admin or view of a site), but might also be a global scope (i.e. superadmin)
            // This depends on the controller.
            // Pagefiller-specific controllers are always for the the sitearea
            $this->scope = (substr(Request::instance()->controller, 0, 9) == "adminarea" OR substr(Request::instance()->controller, 0, 10) == "pagefiller"  OR Request::instance()->controller == "sitearea" ) ? "site" : "global";
            if ($this->scope == "site") 
            {
                $this->sitearea = Wi3_Sitearea::inst();
                // Find out what site we are working with
                // Both the admin controller and the site controller need to know this in order to work properly
                // Find the site by apache 'sitename' variable
                if (isset($_SERVER['REDIRECT_SITENAME']))
                {
                    $sitename = $_SERVER['REDIRECT_SITENAME']; // With correct loading, $_SERVER['REDIRECT_SITENAME'] should always be present, as it is set in the vhosts .htaccess that redirect here
                    $sitedatabasesafename = str_replace(".", "_", $_SERVER['REDIRECT_SITENAME']); // Gives a 'database-safe' representation of the sitename (i.e. without dots if it is a domain-name)
                    // Global site is the site in the global space, i.e. the Site model in the 'list of sites' that is always accesible
                    // ( In the per-site database, there can only exist one Site model )
                    $this->sitearea->globalsite = $this->model->factory("site")->set('name', $sitename)->load();
                    Event::instance("wi3.init.sitearea.globalsite.loaded")->execute();
                    $this->sitearea->site = $this->sitearea->globalsite; // This site instance will be replaced by the local user site. The ->name will be added to that local site, since it does not store that in the local db
                    $this->sitearea->site->databasesafename = $sitedatabasesafename;
                } 
                // If the sitename not present, the page request came here via some illegal method.
                // If the site was not loaded correctly or is not active, we cannot show the site either
                if (!isset($_SERVER['REDIRECT_SITENAME']) OR empty($sitename) OR !$this->sitearea->globalsite->loaded() OR $this->sitearea->globalsite->active == FALSE) {
                    // Site does not exist. Quit.
                    throw new Kohana_Exception("site does not exist");
                }
                // Global site has been loaded and it was found to be active
                // Now we load the local site and requested page from within the user Database
                // This requires the inclusion of the site as a module and an init on its database-config
                //
                // First, Include the whole site-tree in the find_file() function
                Kohana::modules(Kohana::modules() + array("site" => APPPATH . "../../sites/" . $sitename . "/")); // Because Kohana uses include_once() this will only init the new module, without double-including the others
                // Load the sitedatabase config. It will be fetched from the sites/sitename/config folder since the sites/sitename is now in the Kohana find_file paths
                $siteconfig = Kohana::config('sitedatabase')->site;
                // Set up a site database connection, to be used by the site-based-models like Site_Page, Site_User, File etc
                $this->sitearea->database = Wi3_Database::instance("site", $siteconfig);
                Event::instance("wi3.init.sitearea.database.loaded")->execute();
                // Load the user-site 
                $this->sitearea->site = $this->model->factory("site_site")->set('id', 1)->load();
                $this->sitearea->site->name = $sitename; // Add name, since this is not stored in the local site tables, but only in the global ones
                Event::instance("wi3.init.sitearea.site.loaded")->execute();
                
                // Load the pageposition, page and file manager, both within the sitearea
                $this->sitearea->pagepositions = Wi3_Sitearea_Pagepositions::inst();
                $this->sitearea->pages = Wi3_Sitearea_Pages::inst();
                $this->sitearea->files = Wi3_Sitearea_Files::inst();
            
            }
            
            // Load baseviews that are passed as $this into views in order to enable some in-view functions
            // Different setups are possible with the different parameters supplied
            // An instance is created, so that they can also be referenced simply from again loading e.g. Wi3_Baseview::instance('superadminarea');
            // These instances are used as 'object scope' for the $this variables in views. See i.e. the superadminarea-controller's ->view function and the Baseview->capture() for more details
            $this->baseview_superadminarea = Wi3_Baseview::instance('superadminarea', array(
                'javascript_url' => $this->urlof->appfiles.'static/javascript/', 
                'javascript_path' => $this->pathof->app.'static/javascript/',
                'css_url' => $this->urlof->appfiles.'static/css/',
                'css_path' => $this->pathof->app.'static/css/'
            )); //Maybe just define the asset-path(s), from which the URLs are deduced, based on the Wi3::inst()->urlof ?
            $this->baseview_adminarea = Wi3_Baseview::instance('adminarea', array(
                'javascript_url' => $this->urlof->appfiles.'static/javascript/', 
                'javascript_path' => $this->pathof->app.'static/javascript/',
                'css_url' => $this->urlof->appfiles.'static/css/',
                'css_path' => $this->pathof->app.'static/css/'
            )); 
            $this->baseview_sitearea = Wi3_Baseview::instance('sitearea', array(
                'javascript_url' => $this->urlof->site.'static/javascript/', 
                'javascript_path' => $this->pathof->site.'static/javascript/',
                'css_url' => $this->urlof->site.'static/css/',
                'css_path' => $this->pathof->site.'static/css/'
            )); 
            Event::instance("wi3.init.baseviews.loaded")->execute();
            
            // Set up an config loader
            $this->configof = Wi3_Configof::instance();
            
            // Set up auth. This will try to login the current user from either the site db or the global db, based on the scope
            if ($this->scope == "site") 
            {
                $this->sitearea->auth = Wi3_Auth_Site::instance();
            }
            else
            {
                // If user is in setup, then don't yet load Auth and Database instances, since they most probably don't yet exist
                if (Request::instance()->controller != "setup")
                {
                    $this->globalauth = Wi3_Auth_Global::instance();
                }
            }
            $this->acl = Wi3_ACL::instance();
            
            // Load the plugin-manager. The manager will also include the paths to the plugins in the modules-system
            $this->plugins = new Wi3_Plugins;
            
            if ($this->scope == "site") 
            {
                // Make all the pageversion-plugins to load
                // The versionplugins should respond to this event call, and add them to the $this->versionplugins array
                Event::instance('wi3.sitearea.pages.versionplugins.load')->execute();
            }
            
        }
        
        //----------------------------------------
        // small helper functions
        //----------------------------------------
        
        public static function date_now($length=20) {
            return substr(date("YmdHis") . (microtime()*1000000) , 0, $length);
        }
        
        public static function optionlist($list, $selectedval) {
            $ret = "";
            //watch out, (string)false == (string)"" !!!
            foreach($list as $val => $label) {
                $ret .= "<option value='" . $val . "' " . ((strlen($selectedval)>0 AND strlen($val)>0 AND (string)$val === (string)$selectedval) ? "selected='selected'" : "") . ">" . $label . "</option>";
            }
            return $ret;
        }
        
        public function unixpath($path)
        {
            // convert forward slashes to backslashes
            $path = str_replace(DIRECTORY_SEPARATOR, "/", $path);
            // return path without .. and . parts
            return Wi3::inst()->truepath($path);
        }
        
        // Return a nice path without .. and .
        /**
         * This function is to replace PHP's extremely buggy realpath().
         * @param string The original path, can be relative etc.
         * @return string The resolved path, it might not exist.
         */
        public function truepath($path)
        {
            // attempts to detect if path is relative in which case, add cwd
            if(strpos($path,':')===false && (strlen($path)==0 || $path{0}!='/'))
            {
                $absolute = false;
                $path=getcwd().DIRECTORY_SEPARATOR.$path;
            }
            else 
            {
                $absolute = true;
            }
            // resolve path parts (single dot, double dot and double delimiters)
            $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
            $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
            $absolutes = array();
            foreach ($parts as $part) {
                if ('.'  == $part) continue;
                if ('..' == $part) {
                    array_pop($absolutes);
                } else {
                    $absolutes[] = $part;
                }
            }
            $path=($absolute?"/":"") . implode(DIRECTORY_SEPARATOR, $absolutes);
            // if file exists and it is a link, use readlink to resolves links
            //if(file_exists($path) && linkinfo($path)>0)$path=readlink($path);
            return $path;
        }
        
        // Thanks PHP.net
        function copy_recursive( $path, $dest )
        {
            if(is_dir($path))
            {
                @mkdir( $dest );
                $objects = scandir($path);
                if( sizeof($objects) > 0 )
                {
                    foreach($objects as $file)
                    {
                        if( $file == "." || $file == ".." )
                            continue;
                        // go on
                        if( is_dir( $path.DIRECTORY_SEPARATOR.$file ) )
                        {
                            $this->copy_recursive( $path.DIRECTORY_SEPARATOR.$file, $dest.DIRECTORY_SEPARATOR.$file );
                        }
                        else
                        {
                            copy( $path.DIRECTORY_SEPARATOR.$file, $dest.DIRECTORY_SEPARATOR.$file );
                        }
                    }
                }
                return true;
            }
            elseif(is_file($path))
            {
                return copy($path, $dest);
            }
            else
            {
                return false;
            }
        }
        
        // Function to unlink dirs and/or files recursively
        // Based on code found on php.net
        function unlink_recursive($dir) 
        { 
            if (is_dir($dir)) 
            { 
                $objects = scandir($dir); 
                foreach ($objects as $object) { 
                    if ($object != "." && $object != "..") { 
                        if (filetype($dir."/".$object) == "dir")
                        {
                            $this->unlink_recursive($dir."/".$object); 
                        }
                        else
                        {
                            unlink($dir."/".$object); 
                        }
                    } 
                } 
                reset($objects); 
                rmdir($dir); 
            }
            else 
            {
                unlink($dir);
            }
        } 
    }
    
?>
