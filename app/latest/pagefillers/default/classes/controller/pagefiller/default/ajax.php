<?php defined('SYSPATH') or die('No direct script access.');

// Controller_Login provides a login() function that will only simply show a login-form
// A rule must allow access to adminarea.login for everybody
// AACL should try to auto-login a user when it has not logged in yet
// If the check fails, bootstrap.php should send the user to $controller/login
// For the sitearea controller, there is no AACL check on the controller/action, but rather on a page. Redirect will then be to $site->errorpage
class Controller_Pagefiller_Default_Ajax extends Controller_ACL {
        
    public $template;
    
    public function before() 
    {
        // Check whether this controller (fills in current action automatically) can be accessed
        Wi3::inst()->acl->grant("admin", $this); // Admin role can access every function in this controller
        Wi3::inst()->acl->check($this);
        $ajaxfromadminarea = (Request::$is_ajax AND isset($_SERVER["HTTP_ORIGIN"]) AND (Wi3::inst()->routing->protocol.Wi3::inst()->routing->host == $_SERVER["HTTP_ORIGIN"]) AND isset($_SERVER["HTTP_REFERER"]) AND strpos($_SERVER["HTTP_REFERER"], "adminarea") !== FALSE );
        if (!$ajaxfromadminarea) { exit; }
        // TODO: A non-admin user could have injected javascript code that calls saveAllEditableBlocks. This will go ahead once an admin opens the page, deleting all content... 
        // Thus: Non-admin users are NOT allowed to insert any script code, onclick, onmouseover events etc etc. That is complicated...
        // Go with a Whitelist, and only allow text inside spans, p, h1, h2, h3, div and that's it! Other elements like links and images should be done with fields...
    }
    
    protected function setview($name)
    {
        $this->template = View::factory($name);
    }
    
    public function action_reloadpageoptionshtml()
    {
        $pagefiller = new Pagefiller_Default();
        $html = $pagefiller->pageoptionshtmlfortemplate($_POST["template"], NULL); //$_POST["dropzonepreset"]);
        echo json_encode(
            Array(
                "dom" => Array(
                    "fill" => Array(
                        "#menu_addpageoptions" => $html
                    )
                ),
            )
        );
    }
    
    public function action_reloadpageoptionstemplatehtml()
    {
        $pagefiller = new Pagefiller_Default();
        $html = $pagefiller->pageoptionstemplatehtmlfortemplate($_POST["template"]);
        echo json_encode(
            Array(
                "dom" => Array(
                    "fill" => Array(
                        "#pageoptionstemplate" => $html
                    )
                ),
            )
        );
    }
    
    public function action_editpagetemplatesettings($pageid)
    {
        $page = Wi3::inst()->model->factory("Site_Page")->set("id", $pageid)->load();
        Wi3::inst()->sitearea->page = $page;
        // Now notify other entities that use this->page that the page has been loaded
        Event::instance("wi3.init.sitearea.page.loaded")->execute();
        // Save the template for this page
        $templatename = $_POST["pagefiller_templatename"];
        $page->templatename = $templatename;
        $page->update();
        echo json_encode(
            Array(
                "alert" => "template is geupdate"
            )
        );
    }

}
