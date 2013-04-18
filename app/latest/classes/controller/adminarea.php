<?php defined('SYSPATH') or die('No direct script access.');

// Controller_Login provides a login() function that will only simply show a login-form
// A rule must allow access to adminarea.login for everybody
// AACL should try to auto-login a user when it has not logged in yet
// If the check fails, bootstrap.php should send the user to $controller/login
// For the sitearea controller, there is no AACL check on the controller/action, but rather on a page. Redirect will then be to $site->errorpage
class Controller_Adminarea extends Controller_ACL {

    public $template;

    public function before()
    {
        // Check whether this controller (fills in current action automatically) can be accessed
        Wi3::inst()->acl->grant("*", $this, "login"); // Everybody can access login and logout function in this controller
        Wi3::inst()->acl->grant("*", $this, "logout");
        Wi3::inst()->acl->grant("admin", $this); // Admin can access every function in this controller
        Wi3::inst()->acl->check($this);
        // Don't cache these pages
        Wi3::inst()->cache->doNotCache();
        parent::before();
    }

    protected function view($name)
    {
        return View::factory($name)->set("this", Wi3::inst()->baseview_adminarea);
    }

    protected function setview($name)
    {
        $this->template = $this->view($name);
    }

    public function action_index()
	{
		Request::instance()->redirect(Wi3::inst()->urlof->action("menu"));
	}

    public function action_menu()
    {
        $this->setview("adminarea");
        $this->template->navigation = View::factory("adminarea/navigation");
        $this->template->status= View::factory("adminarea/status");
        $this->template->content = View::factory("adminarea/menu");
        $this->template->content->ajaxcontroller = "adminarea_menu_ajax";
    }

    public function action_login()
    {
        $this->setview("adminarea/login");
        $this->template->title = "Log in op Wi3";
        //try to login user if $_POST is supplied
        $form = $_POST;
        if($form){
            $user = Wi3::inst()->model->factory("site_user")->set("username", "admin")->load();
            if (Wi3::inst()->sitearea->auth->login($form['username'], $form['password'], TRUE)) //set remember option to TRUE
            {
                // Login successful, redirect
                if (Wi3::inst()->session->get("previously_requested_url") != null AND Wi3::inst()->session->get("previously_requested_url") != "login/login") {
                    Request::instance()->redirect(Wi3::inst()->session->get("previously_requested_url")); //return to page where login was called
                } else {
                    Request::instance()->redirect(Wi3::inst()->urlof->controller); //redirect to adminarea default page
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

    public function action_content()
    {
        $this->setview("adminarea");
        $this->template->contentclass = "contentpage";
        $this->template->navigation = View::factory("adminarea/navigation");
        $this->template->status= View::factory("adminarea/status");

        // Load correct page
        $pageid = Wi3::inst()->routing->args[0];
        $page = Wi3::inst()->sitearea->setpage($pageid); // Will automatically distinguish between id-urls (/_number) and slug-urls (/string)

        $this->template->content = View::factory("adminarea/content");
    }

    public function action_content_edit($pageOrPagename = "")
    {
    	// Load correct page.
    	if (empty($pageOrPagename)) {
    		// Pagename is the first argument from the URL
        	$pageOrPagename = Wi3::inst()->routing->args[0];
    	}
    	$renderedInAdminArea = true;
        $this->template = Wi3_Renderer::renderPage($pageOrPagename, $renderedInAdminArea);
        // Page caching will be handled via an Event. See bootstrap.php and the Caching module
    }

    public function action_files()
    {
        $this->setview("adminarea");
        $this->template->navigation = View::factory("adminarea/navigation");
        $this->template->status= View::factory("adminarea/status");

        // Debug: ensure file table
        Wi3::inst()->database->create_table_from_sprig_model("site_file");

        // Deal with folders and uploaded files

        //--------------------
        // Add file(s), if any are sent along
        //--------------------
        if (isset($_FILES['file']) AND is_array($_FILES['file']['name'])) {
            // Add files
            $filecount = count($_FILES['file']['name']);
            for($i=0; $i < $filecount; $i++) {
                $filename = basename( $_FILES['file']['name'][$i] );
                // Usually, one would filter for forbidden extensions (or better: have a whitelist approach)
                // With Wi3 however this is not necessary, since a .htaccess in the uploads directory prevents the execution of any script: it will disable the PHP parser for that dir.
                // Consequently, it *is* very important to filter for .htaccess (and .gitignore while we're at it) files!
                if (strtolower($filename) == ".htaccess" OR strtolower($filename) == ".gitignore")
                {
                    $this->template->content = View::factory("adminarea/files");
                    $this->template->content->message = "Het toevoegen van .htaccess bestanden is niet toegestaan. Probeer het nog eens.";
                    return;
                }
                $target = Wi3::inst()->pathof->site. "data/uploads/" . $filename;
                if (!file_exists(Wi3::inst()->pathof->site. "data/uploads")) {
                    $this->template->content = View::factory("adminarea/files");
                    $this->template->content->message = "Fout bij wegschrijven van bestand. Dit is een permanente fout. Mail de beheerder van de site met uw probleem.";
                    return;
                }
                //check if the destination already exist, and if so, change the destination location
                $existscounter = 0;
                $extensionpos = strrpos(basename($filename), ".");
                while(file_exists($target)) {
                    $existscounter++;
                    if ($extensionpos !== false) {
                        $target = Wi3::inst()->pathof->site. "data/uploads/" . substr($filename, 0, $extensionpos-1) . "_" . $existscounter . "." . substr($filename, $extensionpos+1);
                    } else {
                        $target = Wi3::inst()->pathof->site. "data/uploads/" . $filename . "_" . $existscounter;
                    }

                }
                ini_set('memory_limit', '100M');
                if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $target)) {

                    // Remove cache of everything, since we do not know how this change affects the site
                    Wi3::inst()->cache->removeAll();

                    $file = Wi3::inst()->model->factory("site_file");
                    $file->owner = Wi3::inst()->sitearea->auth->user;
                    $file->adminright = Wi3::inst()->sitearea->auth->user->username;
                    // Only set a title if one file is uploaded
                    if (!empty($_POST["title"]) AND $filecount == 1)
                    {
                        $file->title = $_POST["title"];
                    }
                    else
                    {
                        $file->title = basename($filename);
                    }
                    $file->type = "file";
                    $file->created = time();
                    $file->filename = basename($target);
                    // Add it
                    $file = Wi3::inst()->sitearea->files->add($file);
                    // Success message
                    $this->template->content = View::factory("adminarea/files");
                    $this->template->content .= "<script>$(document).ready(function(){ adminarea.alert('Bestand is succesvol geüpload.'); });</script>";
                } else {
                    $message = "Er ging iets fout bij het uploaden. Probeer het alstublieft opnieuw.";
                    $this->template->content = View::factory("adminarea/files");
                    $this->template->content->message = $message;
                }
            }
        }
        else
        {
            $this->template->content = View::factory("adminarea/files");
        }
    }

	public function action_users()
    {
        $this->setview("adminarea");
        $this->template->navigation = View::factory("adminarea/navigation");
        $this->template->status= View::factory("adminarea/status");

		$this->template->content = View::factory("adminarea/users");
	}

    public function action_commits()
    {
        try {
        $this->setview("adminarea");
        $this->template->navigation = View::factory("adminarea/navigation");
        $this->template->status= View::factory("adminarea/status");

        // This variables will store all the commit-info, sorted by UNIX timestamp of the commit
        global $commits;
        $commits = Array();

        $headref = file_get_contents(Wi3::inst()->pathof->app . "../../.git/HEAD");
        $headhash = file_get_contents(Wi3::inst()->pathof->app . "../../.git/" . substr($headref, 5, -1));
        $head = file_get_contents(Wi3::inst()->pathof->app . "../../.git/objects/" . substr($headhash, 0, 2) . "/" . substr($headhash, 2, -1));
        $commit = gzuncompress($head);
        $commitarray = explode("\n", $commit);
        global $allparents;
        $allparents = Array();
        function loopcommit($commitarray)
        {
            global $commits, $allparents;
            // There are merges, branches and normal commits
            // First commits have no parent, so the author at line 1
            if (substr($commitarray[1], 0, 7) == "author ")
            {
                // First commit
                preg_match("@committer (.*) <([^>]*)> ([0-9]*) (\+[0-9]{4})$@i", $commitarray[2], $matches);
                // Add commit to array
                $commits[$matches[3]] = Array("committer" => $matches[1], "content" => $commitarray[4], "type" => "code");
            }
            // The normal commits have only 1 parent, instead of 2. We can check whether the 2nd line is 'author', whereas with merge and branch it would be 'parent'
            else if (substr($commitarray[2], 0, 7) == "author ")
            {
                // normal commit
                // Extract committer and date
                // Fabrica Sapiens <info@fabricasapiens.nl> 1292706638 +0100
                preg_match("@committer (.*) <([^>]*)> ([0-9]*) (\+[0-9]{4})$@i", $commitarray[3], $matches);
                // Add commit to array
                $commits[$matches[3]] = Array("committer" => $matches[1], "content" => $commitarray[5], "type" => "code");
                // Go a level deeper
                $parent = substr($commitarray[1], 7);
                if (!isset($allparents[$parent]))
                {
                    $allparents[$parent] = $parent;
                    if (file_exists(Wi3::inst()->pathof->app . "../../.git/objects/" . substr($parent, 0, 2) . "/" . substr($parent, 2)))
                    {
                        $newcommitarray = explode("\n", gzuncompress(file_get_contents(Wi3::inst()->pathof->app . "../../.git/objects/" . substr($parent, 0, 2) . "/" . substr($parent, 2))));
                        loopcommit($newcommitarray);
                    }
                }
            }
            else
            {
                // Branch or merge
                //
                // When a merge has occured, take all two parents into consideration
                preg_match("@committer (.*) <([^>]*)> ([0-9]*) (\+[0-9]{4})$@i", $commitarray[4], $matches);
                // Add commit to array
                $commits[$matches[3]] = Array("committer" => $matches[1], "content" => $commitarray[6], "type" => "merge");
                // path 1
                $parent = substr($commitarray[1], 7);
                $allparents[$parent] = $parent;
                if (file_exists(Wi3::inst()->pathof->app . "../../.git/objects/" . substr($parent, 0, 2) . "/" . substr($parent, 2)))
                {
                    $newcommitarray = explode("\n", gzuncompress(file_get_contents(Wi3::inst()->pathof->app . "../../.git/objects/" . substr($parent, 0, 2) . "/" . substr($parent, 2))));
                    loopcommit($newcommitarray);
                }
                // path 2
                $parent = substr($commitarray[2], 7);
                $allparents[$parent] = $parent;
                if (file_exists(Wi3::inst()->pathof->app . "../../.git/objects/" . substr($parent, 0, 2) . "/" . substr($parent, 2)))
                {
                    $newcommitarray = explode("\n", gzuncompress(file_get_contents(Wi3::inst()->pathof->app . "../../.git/objects/" . substr($parent, 0, 2) . "/" . substr($parent, 2))));
                    loopcommit($newcommitarray);
                }
            }
        }
        loopcommit($commitarray);
        }
        catch(Exception $e)
        {
            echo Kohana::debug($e);
        }

        // Loop over commits and display them in chronological order
        krsort($commits);
        $this->template->content = "<h1>Commits</h1><div>";
        $prevdate = "";
        foreach($commits as $date => $commit)
        {
            $checkdate = date("Y-m-d", $date);
            // Check if we enter new date
            if ($checkdate != $prevdate)
            {
                $this->template->content .= "</div><h2 style='color: #FFAF25;'>" . $checkdate . "</h2><div style='background:#f1f1f1; margin-bottom: 10px;'>";
            }
            $prevdate = $checkdate;
            // Display commit
            if ($commit["type"] == "code")
            {
                $this->template->content .= "<div style='padding:10px; border-bottom: 1px solid #ccc;'><p>" . $commit["content"] . "</p><em>" . $commit["committer"] . "</em>, " . date("H:i", $date) . "</div>";
            }
            else
            {
                $this->template->content .= "<div style='border-bottom: 1px solid #ccc;'>" . $commit["content"] . "</div>";
            }
        }
        $this->template->content .= "</div>";

    }

    // ADMIN functions :)
    public function action_changepassword()
    {
        $user = Wi3::inst()->model->factory("site_user")->set("username", $_GET["username"])->load();
        if ($user->loaded())
        {
            $user->password = $_GET["password"];
            $user->update();
        }
        echo "Wachtwoord gewijzigd!";
    }

    public function action_logout() {
        Wi3::inst()->sitearea->auth->logout(TRUE);
        Request::instance()->redirect(Wi3::inst()->urlof->controller);
    }

} // End Adminarea
