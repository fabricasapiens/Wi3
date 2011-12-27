<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Superadminarea extends Controller_ACL {
        
    public $template;
    
    // Init ACL and check rights
    public function before() 
    {
        // Set rules for this controller
        Wi3::inst()->acl->grant("*", $this, "login"); // Grant everybody access to the login function
        Wi3::inst()->acl->grant("*", $this, "logout");
        Wi3::inst()->acl->grant("superadmin", $this);
        // Check whether this controller (fills in current action automatically) can be accessed
        Wi3::inst()->acl->check($this);
    }
    
    // View functions
    public function view($name)
    {
        return View::factory($name)->set("this", Wi3::inst()->baseview_superadminarea);
    }
    
    public function setview($name)
    {
        $this->template = $this->view($name);
    }
    
    // Login and Logout functions
    public function action_login() {
        $this->setview("superadminarea/login");
        $this->template->title = "Log in op Wi3";
        //try to login user if $_POST is supplied
        $form = $_POST;
        if($form){
            $user = Wi3::inst()->model->factory("user")->set("username", "superadmin")->load();
            if (Wi3::inst()->globalauth->login($form['username'], $form['password'], TRUE)) //set remember option to TRUE
            {
                // Login successful, redirect
                if (Wi3::inst()->session->get("previously_requested_url") != null AND Wi3::inst()->session->get("previously_requested_url") != "login/login") {
                    Request::instance()->redirect(Wi3::inst()->session->get("previously_requested_url")); //return to page where login was called
                } else {
                    Request::instance()->redirect(""); //redirect to home-page
                }
            }
            else
            {
                $this->template->content = '<p>Login mislukt.</p>';
                $this->template->content .= View::factory("login/loginform")->render();
                return;
            }
        }
        
        $this->template->content = $this->view("login/loginform");
    }
    
    public function action_logout() {
        Wi3::inst()->globalauth->logout(TRUE);
        Request::instance()->redirect(Wi3::inst()->urlof->controller);
    }
    
    // Rest of template
    
	public function action_index()
	{
		$this->setview("superadminarea");
	}
    
    public function action_createsite()
    {
        $this->setview("superadminarea");
        
        // name should not start with a dot, to prevent issues with a) overwriting the .template folder, and b) hidden folders
        if (substr($_POST["name"], 0, 1) == ".") 
        {
            echo "<p>site kon niet aangemaakt worden!</p>";
            echo "<p>Sitenaam mag niet beginnen met een punt (.)!</p>";
            return;
        }
        
        ###
        # Database settings
        ###
        if (isset($_POST["dbusername"]) AND isset($_POST["dbpassword"]) AND isset($_POST["dbexistingornew"]) AND isset($_POST["dbname"]))
        {
            // Create or use Database!
            $dbname = $_POST["dbname"];
            $dbokay = TRUE;
            for($i=0;$i<1;$i++) // Just do it one time, but now we can use the break command...
            {
                // Try connection
                @$con = mysql_connect("localhost",$_POST["dbusername"],$_POST["dbpassword"]);
                if (!$con)
                {
                    $dbokay = FALSE;
                    $message = __("Connection to database could not be established. Please try again.");
                    break;
                }
                // Save the grants of the current user 
                $result = mysql_query("SHOW GRANTS FOR CURRENT_USER");
                $grants = Array();
                while($row = mysql_fetch_array($result))
                {
                    $grants[] = $row;
                }
                $hasallprivileges = FALSE;
                foreach($grants as $grant)
                {
                    if (strpos($grant[0], "GRANT ALL PRIVILEGES ON *.* TO ") === 0)
                    {
                        // User has all privileges for all dbs, so that's fine
                        $hasallprivileges = TRUE;
                        break; // break from foreach
                    }
                }

                if ($_POST["dbexistingornew"] == "existing")
                {
                    // Try if existing db exists
                    $db_selected = mysql_select_db($dbname, $con);
                    if ($db_selected == FALSE) {
                        $dbokay = FALSE;
                        $message = __("Database '" . $dbname . "' does not exist. Please try again.");
                        break;
                    }
                    // Now check whether we have the rights to create tables in the db 
                    $hasprivileges = FALSE;
                    if ($hasallprivileges)
                    {
                        $hasprivileges = TRUE;
                    }
                    else 
                    {
                        foreach($grants as $grant)
                        {
                            if (strpos($grant[0], "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, REFERENCES, INDEX, ALTER") === 0 AND strpos($grant[0], "ON `" . $dbname . "`") > 0)
                            {
                                // User has privileges for the $dbname db, so that's fine
                                $hasprivileges = TRUE;
                                break; // break from foreach
                            }
                        }
                    }
                    // Final check for db privileges
                    if ($hasprivileges === FALSE)
                    {
                        $dbokay = FALSE;
                        $message = __("User does not have the proper rights to use database '" . $dbname . "'. Please try again.");
                        break;
                    }
                }
                else 
                {
                    // Check if we can create the new DB
                    if ($hasallprivileges)
                    {
                        if (!mysql_query("CREATE DATABASE " . $dbname,$con))
                        {
                            $dbokay = FALSE;
                            // Check if there was an error because the db already existed
                            $db_selected = mysql_select_db($dbname, $con);
                            if ($db_selected) 
                            {
                                $message = __("User was unable to create database '" . $dbname . "' because it already exists. Please delete the db manually or select the 'existing' option to use the existing database.");
                            }
                            else 
                            {
                                $message = __("User was unable to create database '" . $dbname . "', despite having the rights to do so. Please try again.");
                            }
                            break;
                        }
                    }
                    else 
                    {
                        $dbokay = FALSE;
                        $message = "User does not have the proper rights to create database '" . $dbname . "'. Please try again.";
                        break;
                    }
                }
            }
            if (!$dbokay)
            {
                echo "<p>" . $message . "</p>";
                return;
            }
        }
        
        ###
        # Site creation
        ###
        
        $site = Wi3::inst()->model->factory("site");
        $site->active = $_POST["active"];
        $site->name = $_POST["name"];
        $site->title = $_POST["title"]; // Sitefolder is currently always the same as the sitename
        try 
        {
            $site->create();
            // Second create the site folder (with config files etc). Do this by copying the .template folder.
            Wi3::inst()->copy_recursive( APPPATH . "../../sites/.template", APPPATH . "../../sites/" . $site->name);
            // Save the DB configuration file by loading the example file and set the correct values
            $wi3databaseconfig = file_get_contents(APPPATH . "../../sites/" . $site->name . "/config/sitedatabase.php.example");
            $wi3databaseconfig = preg_replace("@\'username\'.*@", "'username' => '" . $_POST["dbusername"] . "',", $wi3databaseconfig);
            $wi3databaseconfig = preg_replace("@\'password\'.*@", "'password' => '" . $_POST["dbpassword"] . "',", $wi3databaseconfig);
            $wi3databaseconfig = preg_replace("@\'database\'.*@", "'database' => '" . $_POST["dbname"] . "',", $wi3databaseconfig);
            $wi3databaseconfig = preg_replace("@dbname\=\w*@", "dbname=" . $_POST["dbname"], $wi3databaseconfig);
            file_put_contents(APPPATH . "../../sites/" . $site->name . "/config/sitedatabase.php", $wi3databaseconfig);
            // Now load the newly created database-config file for this specific site
            $configarray = include( APPPATH . "../../sites/" . $site->name . "/config/sitedatabase.php" );
            $dbinstance = Database::instance("site", $configarray["site"]);
            // Now create all user tables in the site-space. They will use the 'site' DB instance automatically (as this is set in the Model->_db setting)
            Wi3::inst()->database->create_table_from_sprig_model("site_site");
            Wi3::inst()->database->create_table_from_sprig_model("site_pageposition");
            Wi3::inst()->database->create_table_from_sprig_model("site_page");
            Wi3::inst()->database->create_table_from_sprig_model("site_array");
            Wi3::inst()->database->create_table_from_sprig_model("site_arraydata");
            Wi3::inst()->database->create_table_from_sprig_model("site_data");
            Wi3::inst()->database->create_table_from_sprig_model("site_file");
            // Setup the Auth classes
            Wi3::inst()->database->create_table_from_sprig_model("site_user");
            Wi3::inst()->database->create_table_from_sprig_model("site_user_token");
            Wi3::inst()->database->create_table_from_sprig_model("site_role");
            // Now create the admin user 
            $m = Wi3::inst()->model->factory("site_user");
            $m->username = "admin";
            $m->email = "admin@example.com";
            $m->password = "admin";
            $m->password_confirm = "admin"; 
            $m->create();
            // Now create roles
            $role = Wi3::inst()->model->factory("site_role");
            $role->name = "login";
            $role->description = "login role";
            $role->users = $m->id;
            $role->create();
            // Admin role
            $role = Wi3::inst()->model->factory("site_role");
            $role->name = "admin";
            $role->description = "admin role for this site";
            $role->users = $m->id;
            $role->create();
            
            // Finally, loop over all the pagefillers, and check whether they want to insert any tables etc
            // TODO: all pagefillers please
            Pagefiller_default::event("site_created", FALSE);
        }
        catch(Exception $e) 
        {
            echo "<p>site kon niet aangemaakt worden!</p>";
            echo Kohana::debug($e);
            return;
        }
        
        // Redirect to get rid of the superadminarea/someaction URL and to prevent POST issues
        Request::instance()->redirect(Wi3::inst()->urlof->controller("superadminarea"));
    }
    
    public function action_deletesite()
    {
        $this->setview("superadminarea");
        
        $site = Wi3::inst()->model->factory("site");
        $site->name = $_POST["name"];
        $site->load();
        try 
        {
            ## Delete the global site in the global DB
            $site->delete(); // The cascading deletion of the corresponding URLs happens in the model itself
            ## Delete the site-DB
            // Set the local site and add a databasesafename so that the DB config can fetch it
            Wi3::inst()->sitearea = Wi3_Sitearea::inst();
            Wi3::inst()->sitearea->site = $site;
            Wi3::inst()->sitearea->site->databasesafename = str_replace(".", "_", $site->name);
            // Load DB config
            $configarray = include( APPPATH . "../../sites/" . $site->name . "/config/sitedatabase.php" );
            $dbinstance = Database::instance("site", $configarray["site"]);
            if (isset($configarray["site"]["connection"]["database"]) AND !empty($configarray["site"]["connection"]["database"]))
            {
                @Wi3::inst()->database->delete_database($configarray["site"]["connection"]["database"]); 
            }
            ## Delete all files etc for this site 
            $sitename = $site->name;
            if (!empty($sitename)) // Be very sure that we do not delete the whole /sites folder
            {
                @Wi3::inst()->unlink_recursive(APPPATH . "../../sites/" . $sitename . "/");
            }
        }
        catch(Exception $e) 
        {
            echo Kohana::debug($e);
            return;
        }
        
        // Redirect to get rid of the superadminarea/someaction URL and to prevent POST issues
        Request::instance()->redirect(Wi3::inst()->urlof->controller("superadminarea"));
    }
    
    public function action_addurl()
    {
        $this->setview("superadminarea");
        
        $site = Wi3::inst()->model->factory("site");
        $site->name = $_POST["name"];
        $site->load();
        if (Validate::factory($_POST)
            ->filter(TRUE, 'trim')
            ->rule('url', 'not_empty')
            ->rule('url', 'url')
            ->check()
        )
        {
            try 
            {
                $url = Wi3::inst()->model->factory("url");
                $url->url = $_POST["url"];
                $url->site = $site; // This alias will use the column $url->site_id and fill it with $site->id
                // Get the domain and folder for this URL
                preg_match("@^http[s]?\:[\/]{2}([^\/]+)(?:\/(.*))?$@", $_POST["url"], $matches);
                $url->domain = $domain = $matches[1];
                $url->folder = $folder = trim(isset($matches[2])?$matches[2]:"", "/");
                $url->create();
                
                $vhostfolder = Wi3::inst()->unixpath(APPPATH . "../../vhosts/") . "/";
                
                // Create the vhost. Do this by copying the .template folder.
                if (!is_dir($vhostfolder.$domain))
                {
                    Wi3::inst()->copy_recursive($vhostfolder.".template", $vhostfolder.$domain);
                }
                // Create the correct folder
                // TODO: This will probably not work for folders that are nested deeper than 1 level. Fix this recursively
                if (!empty($folder))
                {
                    mkdir($vhostfolder . $domain . "/httpdocs/" . $folder);
                }
                
                // Create correct .htaccess by copying the example.htaccess file and set the correct values
                $htaccess = file_get_contents($vhostfolder . $domain . "/httpdocs/example.htaccess");
                $htaccess = preg_replace("@demosite@", $site->name, $htaccess);
                if (empty($folder))
                {
                    // Make adminarea and superadminarea links functional
                    $htaccess = preg_replace("@vhosts\/\(\.\*\)\/httpdocs@", "vhosts/".$domain."/httpdocs", $htaccess);
                    file_put_contents($vhostfolder . $domain . "/httpdocs/.htaccess", $htaccess);
                }
                else
                {
                    // Make adminarea and superadminarea links functional
                    $htaccess = preg_replace("@vhosts\/\(\.\*\)\/httpdocs@", "vhosts/".$domain."/httpdocs/".$folder, $htaccess);
                    // Make the redirect to the /sites folder correct...
                    $htaccess = preg_replace("@\.\.\/\.\.\/\.\.\/@", "../../../../", $htaccess);
                    file_put_contents($vhostfolder . $domain . "/httpdocs/" . $folder . "/.htaccess", $htaccess);
                }
                
                // Create new rules for root .htaccess 
				$vhostrootrelativefolder = Wi3::inst()->unixpath(APPRELATIVEPATH . "../../vhosts/") . "/";
                $all = Wi3::inst()->model->factory("url")->load(NULL, FALSE); // FALSE for no limit = load all
                $distinctdomains = Array();
                $rules = "RewriteEngine On
                
";
                foreach($all as $one)
                {
                    if (!isset($distinctdomains[$one->domain]))
                    {   
                        $distinctdomains[$one->domain] = $one->domain;
                        // Add rule 
                        $rules .= "RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{SERVER_NAME} ^" . $one->domain . "$ [NC]
RewriteRule (.*) " . $vhostrootrelativefolder . $one->domain . "/httpdocs/$1/ [E=REDIRECTED:TRUE,L]
";
                    }
                }
                
                // Write the .htaccess in /
                // TODO: only do this if the .htaccess actually changed (i.e. if a new domain was added)
                // TODO: backup old .htaccess
                $root = DOCUMENTROOT;
                if (is_writable($root.".htaccess"))
                {
                    file_put_contents($root.".htaccess", $rules);
                }
                else
                {
                    if (!file_exists($root.".htaccess") AND is_writable($root))
                    {
                        file_put_contents($root.".htaccess", $rules);
                    }
                    else
                    {
                        // TODO: present the rules if adding them was not possible
                    }
                }
                
                // Below code is if we did not want to use vhosts for some reason
                /*
                // Now create the .htaccess code for this URL
                $folderhtaccess = View::factory("superadminarea/htaccess/foldertemplate")->set("domain", $domain)->render();
                $vhosthtaccess = View::factory("superadminarea/htaccess/vhosttemplate")->set("sitename", $site->name)->set("currentpath", $folder)->set("wi3path", Wi3::inst()->unixpath(APPPATH."../../").DIRECTORY_SEPARATOR)->render();
                // Set this code in the correct place
                if (empty($folder))
                {
                    // Put .htaccess in root 
                    $root = $_SERVER["DOCUMENT_ROOT"]."/";
                    if (is_writable($root))
                    {
                        file_put_contents($root.".htaccess", $vhosthtaccess);
                    }
                    else 
                    {
                        echo ".htaccess kon niet aangemaakt worden...! Doe dit handmatig.";
                    }
                }
                else 
                {
                    // There is a folder
                    $writefolder = $_SERVER["DOCUMENT_ROOT"]."/".$folder;
                    if (!is_dir($writefolder))
                    {
                       @mkdir($writefolder);
                    }
                    if (is_writable($writefolder))
                    {
                        file_put_contents($writefolder."/".".htaccess", $vhosthtaccess);
                    }
                    else
                    {
                        echo ".htaccess kon niet aangemaakt worden...! Doe dit handmatig.";
                    }
                }
                */
            }
            catch(Exception $e) 
            {
                echo "Url kon niet aangemaakt worden!";
                echo Kohana::debug($e);
                return;
            }
        }
        else 
        {
            echo "Url kon niet aangemaakt worden!";
        }
        
        // Redirect to get rid of the superadminarea/someaction URL and to prevent POST issues
        Request::instance()->redirect(Wi3::inst()->urlof->controller("superadminarea"));
    }
    
    public function action_activatesite()
    {
        $this->setview("superadminarea");
        
        $site = Wi3::inst()->model->factory("site");
        $site->name = $_POST["name"];
        $site->load();
        $site->active = TRUE;
        $site->update();
        
        // Redirect to get rid of the superadminarea/someaction URL and to prevent POST issues
        Request::instance()->redirect(Wi3::inst()->urlof->controller("superadminarea"));
    }
    
    public function action_deactivatesite()
    {
        $this->setview("superadminarea");
        
        $site = Wi3::inst()->model->factory("site");
        $site->name = $_POST["name"];
        $site->load();
        $site->active = FALSE;
        $site->update();
        
        // Redirect to get rid of the superadminarea/someaction URL and to prevent POST issues
        Request::instance()->redirect(Wi3::inst()->urlof->controller("superadminarea"));
    }
    
    public function action_htaccessrules()
    {
        $vhostfolder = Wi3::inst()->unixpath(APPRELATIVEPATH . "../../vhosts/") . "/";
        // Create rules for root .htaccess 
        $all = Wi3::inst()->model->factory("url")->load(NULL, FALSE); // FALSE for no limit = load all
        $distinctdomains = Array();
        $rules = "RewriteEngine On
        
";
        foreach($all as $one)
        {
            if (!isset($distinctdomains[$one->domain]))
            {   
                $distinctdomains[$one->domain] = $one->domain;
                // Add rule 
                $rules .= "RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{SERVER_NAME} ^" . $one->domain . "$ [NC]
RewriteRule (.*) " . $vhostfolder . $one->domain . "/httpdocs/$1/ [E=REDIRECTED:TRUE,L]
";
            }
        }
        
        Request::instance()->response = $rules;
        Request::instance()->send_file(TRUE, "htaccess.txt");
    }

}
