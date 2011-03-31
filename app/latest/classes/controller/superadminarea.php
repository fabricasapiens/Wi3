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
        
        $site = Wi3::inst()->model->factory("site");
        $site->active = $_POST["active"];
        $site->name = $_POST["name"];
        $site->databasesafename = str_replace(".", "_", $_POST["name"]); // Gives a 'database-safe' representation of the sitename (i.e. without dots if it is a domain-name)
        $site->title = $_POST["title"]; // Sitefolder is currently always the same as the sitename
        try 
        {
            $site->create();
            // Set the global site the sitearea->globalsite
            Wi3::inst()->sitearea = Wi3_Sitearea::inst();
            Wi3::inst()->sitearea->globalsite = $site;
            // Set the local site temporarily the same as the global site, so that the DB config can fetch i.e. name etc from that
            Wi3::inst()->sitearea->site = $site;
            // Now try to create a dedicated database for this site
            Wi3::inst()->database->create_database("wi3_".$site->databasesafename);
            // Second create a folder with config files etc
                // TODO: folder for now is assumed. 
            // Third, load the available database-config file for this specific site
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
        }
        /*
        $url = Wi3::inst()->model->factory("site_url");
        $url->url = $_POST["url"];
        $url->site = $site; // This alias will use the column $url->site_id and fill it with $site->id
        try 
        {
            $url->create();
        }
        catch(Exception $e) 
        {
            echo "url die bij site hoort, kon niet aangemaakt worden!";
        }*/
    }
    
    public function action_deletesite()
    {
        $this->setview("superadminarea");
        
        $site = Wi3::inst()->model->factory("site");
        $site->name = $_POST["name"];
        //$site->load();
        try 
        {
            $site->delete(); // The cascading deletion of the corresponding URLs happens in the model itself
            // Now try to create a dedicated database for this site
            Wi3::inst()->database->delete_database("eenwebsitemaken_".str_replace(".", "_", $_POST["name"])); // Gives a 'database-safe' representation of the sitename (i.e. without dots if it is a domain-name)
        }
        catch(Exception $e) 
        {
            echo Kohana::debug($e);
        }
    }
    
    public function action_activatesite()
    {
        $this->setview("superadminarea");
        
        $site = Wi3::inst()->model->factory("site");
        $site->name = $_POST["name"];
        $site->load();
        $site->active = TRUE;
        $site->update();
    }
    
    public function action_deactivatesite()
    {
        $this->setview("superadminarea");
        
        $site = Wi3::inst()->model->factory("site");
        $site->name = $_POST["name"];
        $site->load();
        $site->active = FALSE;
        $site->update();
    }

}
