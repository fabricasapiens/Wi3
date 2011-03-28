<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Setup extends Controller_Base {
    
    public $template = "";
    
    public function view($name)
    {
        return View::factory($name)->set("this", Wi3::inst()->baseview_superadminarea);
    }
    
    public function setview($name)
    {
        $this->template = $this->view($name);
    }
    
	public function action_index()
	{
        
        $this->setview("superadminarea");
        
        ob_start();
                
        echo "<h1>Auth</h1>";
        echo "<a href='".Wi3::inst()->urlof->action("setup", "setup_table")."role'>setup roles</a><br />";
        echo "<a href='".Wi3::inst()->urlof->action("setup", "setup_table")."user'>setup users</a><br />";
        echo "<a href='".Wi3::inst()->urlof->action("setup", "create_superadmin")."user'>(re)create the superadmin-user</a><br />";
        echo "<a href='".Wi3::inst()->urlof->action("setup", "setup_table")."user_token'>setup user-tokens</a><br />";
        
        echo "<h1>Global</h1>";
        echo "<a href='".Wi3::inst()->urlof->action("setup", "setup_table")."site'>setup sites</a><br />";
        
        echo "<h1>Site specific</h1>";
       // TODO: show setup for all sites that are available. New sites need first to be inserted in the global site-table via the superadmin-interface
       /*
            echo "<a href='".$this->request->uri."/setup_table/user'>setup users</a><br />";
            echo "<a href='".$this->request->uri."/setup_table/role'>setup roles</a><br />";
            echo "<a href='".$this->request->uri."/setup_table/user_token'>setup user-tokens</a><br />";
        */
        
        $content = ob_get_clean();
        
		$this->template->content = $content;
	}
    
    public function action_setup_table($name=NULL) 
    {
        $result = Wi3::inst()->database->create_table_from_sprig_model($name);
        foreach($result as $tname => $res) {
            if ($res !== FALSE)
            {
                echo "<p>table ".$tname." has been generated.</p>";
            } else {
                echo "<p>Creating table ".$tname." raised an error.</p>";
            }
        }
        echo "<p>Back to <a href='..'>setup</a></p>";
    }
    
    public function action_create_superadmin()
    {
        try { 
            $m = Wi3::inst()->model->factory("user");
            $m->username = "superadmin";
            $m->email = "superadmin@example.com";
            $m->load();
            foreach($m->roles as $role)
            {
                $role->delete();
            }
            $m->delete();
            // TODO: make sure Sprig understands that the columns with $_in_db == FALSE should NOT be added to the delete() clause
            // Then, place the following line before the $m->password = "superadmin" above
            $m->password = "superadmin";
            $m->password_confirm = "superadmin"; 
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
            return;
        }
        echo "<p>Superadminuser has been recreated.</p>";
        echo "<p>Back to <a href='..'>setup</a></p>";
    }
    
    // The controller actions

} // End Welcome
