<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Sitearea extends Controller {
    
    public function before()
    {
        // Load the requested page
        $pagename = Wi3::inst()->routing->args[0];
        Wi3::inst()->sitearea->setpage($pagename); // Will also execute the "wi3.init.sitearea.page.loaded" event
        // Now check the rights for this page 
        // Pages can only be viewed if the page has not set any 'viewright' or if the user that requests the page is logged in and has that required viewright
        $page = Wi3::inst()->sitearea->page;
        if (!empty($page->viewright))
        {
            // Check for required role
            $requiredrole = $page->viewright;
            $hasrequiredrole = false;
            // Check if there is a logged-in user for this site at all
            $user = Wi3::inst()->sitearea->auth->user;
            if (is_object($user))
            {
                // Check user rights
                $roles = $user->roles;
                foreach($roles as $role)
                {
                    if (strtolower($role->name) === strtolower($requiredrole) OR $role->name === strtolower("admin"))
                    {
                        $hasrequiredrole = true;
                        break;
                    }
                }
            }
            // Check
            if (!$hasrequiredrole)
            {
                // Redirect to the loginpage of the site (if known, that is)
                $site = Wi3::inst()->sitearea->site;
                if(strlen($site->loginpage) > 0)
                {
                    Request::instance()->redirect(Wi3::inst()->urlof->page($site->loginpage));
                }
                else
                {
                    throw(new ACL_Exception_403()); // Permission denied
                    exit;
                }
            }
        }
    }
    
    public function action_index()
    {
        return $this->action_view();
    }
    
    public function action_view()
    {
        // Correct page has been loaded in the before() function
        // Render page
        $this->request->response = Wi3::inst()->sitearea->page->render(); 
        // Page caching will be handled via an Event. See bootstrap.php and the Caching module
    }

}
