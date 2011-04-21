<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Setup extends Controller_Base {
    
    public $template = "";
    
	public function action_index()
	{
	
	    // TODO: check if referrer is the 'plain' setup file, and the request is sent via PHP
	
        // Check login as used by the setup process
        session_name("wi3setup");
        if (!session_start()) { echo "session could not be started."; exit; }
        if (!isset($_SESSION["setup_username"]) OR !isset($_SESSION["setup_password"]))
        {
            exit; // User is not logged in
        }
        else 
        {
            $settings = $_SESSION;
        }
        
        // TODO: only create tables when they do not already exist
        
        if (!$this->setup_table("role")) { exit; }
        if (!$this->setup_table("user")) { exit; }
        if (!$this->create_superadmin($settings)) { exit; }
        if (!$this->setup_table("user_token")) { exit; }
        if (!$this->setup_table("site")) { exit; }
        
        echo "tables sucessfully created"; // Succes!
	}
    
    public function setup_table($name=NULL) 
    {
        $result = Wi3::inst()->database->create_table_from_sprig_model($name);
        foreach($result as $tname => $res) {
            if ($res !== FALSE)
            {
                return true;
            } else {
                return false;
            }
        }
    }
    
    public function create_superadmin($settings)
    {
        try { 
            // First delete existing superadmin
            $m = Wi3::inst()->model->factory("user");
            $m->username = $settings["setup_username"];
            $m->email = "superadmin@example.com";
            $m->load();
            foreach($m->roles as $role)
            {
                $role->delete();
            }
            $m->delete();
            // TODO: make sure Sprig understands that the columns with $_in_db == FALSE should NOT be added to the delete() clause
            // Then, place the following line before the $m->password = "superadmin" above
            $m->password = $settings["setup_originalpassword"];
            $m->password_confirm = $settings["setup_originalpassword"]; 
            // (Re)create the existing superadmin
            $m->create();
            // Now create roles
            $role = Wi3::inst()->model->factory("role");
            $role->name = "superadmin";
            $role->description = "superadmin role";
            $role->users = $m->id;
            $role->create();
            $role = Wi3::inst()->model->factory("role");
            $role->name = "login";
            $role->description = "login role";
            $role->users = $m->id;
            $role->create();
        }
        catch (Exception $e) 
        {
            echo Kohana::debug($e);
            return false;
        }
        return true;
    }
    
    // The controller actions

} // End Welcome

?>
