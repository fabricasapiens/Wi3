<?php defined('SYSPATH') or die('No direct script access.');

// Controller_Login provides a login() function that will only simply show a login-form
// A rule must allow access to adminarea.login for everybody
// AACL should try to auto-login a user when it has not logged in yet
// If the check fails, bootstrap.php should send the user to $controller/login
// For the sitearea controller, there is no AACL check on the controller/action, but rather on a page. Redirect will then be to $site->errorpage
class Controller_Adminarea_Menu_Ajax extends Controller_ACL {
        
    public $template;
    
    public function before() 
    {
        // Check whether this controller (fills in current action automatically) can be accessed
        Wi3::inst()->acl->grant("admin", $this); // Admin (of this site!) can access every function in this controller
        Wi3::inst()->acl->check($this);
		// Check if the user gets here via an AJAX POST, and not via a sneaky GET in an Iframe on a weird site
        $ajaxpost = (Request::$is_ajax AND Request::$method=="POST");
        if (!$ajaxpost) { exit; }
    }
    
    protected function view($name)
    {
        return View::factory($name)->set("this", Wi3::inst()->baseview_adminarea);
    }
    
    protected function setview($name)
    {
        $this->template = $this->view($name);
    }
    
    public function action_addPageposition() 
    {
        // Add a new pageposition
        if (isset($_POST["under"]) AND is_numeric(substr($_POST["under"],9)))
        {
            $underid = substr($_POST["under"],9);
            $pageposition = Wi3::inst()->sitearea->pagepositions->add(array("under"=>$underid));
            unset($_POST["under"]); // To not confuse the 'real' page with stuff in the $_POST var
        }
        else
        {
            $pageposition = Wi3::inst()->sitearea->pagepositions->add();
        }
        
        // Add the page
        // Add some values
        $post = $_POST;
        if (!isset($post["longtitle"]) OR empty($post["longtitle"])) { $post["longtitle"] = "nieuwe pagina"; }
        if (!isset($post["slug"])) { $post["slug"] = $post["longtitle"]; }
        // Now make sure the page-slug is not yet taken
        $slug = strtolower($post["slug"]);
        $counter = 0;
        while($counter < 20 && Wi3::inst()->model->factory("site_page")->set("slug", $post["slug"])->load()->loaded() === true)
        {
            $counter++;
            $post["slug"] = $slug . " " . $counter;
        }
		if ($counter == 19) {
			// Not good
			// TODO: fix this
		}
        $post["owner"] = Wi3::inst()->sitearea->auth->user->username;
        $post["filler"] = "default"; // Assume default page filler
        
        // Add the page itself
        $page = Wi3::inst()->sitearea->pages->add($post);
        // And now edit the created page...
        $page->pageposition = $pageposition; // Reference to its 'father'
        $page->versiontags = Wi3::inst()->model->factory("site_array")->setname("versiontags")->setref($page)->create();
        
        // Let all the versionplugins do their job (like adding versiontags etc)
        // They *should* clean the $_POST from any stuff they injected there in the versionhtmlforaddpage() function
        foreach(Wi3::inst()->sitearea->pages->versionplugins() as $plugin)
        {
            list($page,$post) = $plugin->processaddpage($page,$post);
        }
        $page->update();
        $page->versiontags->update();
        
        if ($page == false) {
            echo json_encode(
                Array(
                    "alert" => "pagina kon NIET toegevoegd worden",
                )
            );
        } else {
        
            // Remove cache of everything, since we do not know how this change affects the site
            Wi3::inst()->cache->removeAll();
            
            if ($pageposition->lft == 1 AND $pageposition->rgt == 2)
            {
                // The new pageposition is the only pageposition there is. For the javascript menu to work properly, we need to reload the page.
                echo json_encode(
                    Array(
                        "scriptsbefore" => Array(
                            "reload" => "window.location.reload();"
                        )
                    )
                );
            }
            else
            {
                $li = html::anchor($page->id, $page->longtitle);
                echo json_encode(
                    Array(
                        "alert" => "pagina is toegevoegd ",
                        //"dom" => Array("append" => Array("#menu_pages" => "<li class='treeItem' id='treeItem_" . $page->id . "'><span>" . html::anchor("engine/content/" . $page->id, $page->title) . "</span></li>")),
                        "scriptsafter" => Array(
                            "adminarea.currentTree().addNode('treeItem_" . $page->id . "','" . addslashes($li) . "')",
                        )
                    )
                );
            }
        }
        
    }
    
    public function action_movePagepositionBefore() {
        $movedpage = $_POST["source"];
        $referencepage = $_POST["destination"];
        $pageid = substr($movedpage,9);
        $refid = substr($referencepage,9);
        if (Wi3::inst()->sitearea->pagepositions->moveBefore($pageid, $refid)) {
            
            // Remove cache of everything, since we do not know how this change affects the site
            Wi3::inst()->cache->removeAll();

            echo json_encode(
                Array(
                    "alert" => "pagina is verhuisd"
                )
            );
        } else {
            echo json_encode(
                Array(
                    "alert" => "pagina kon NIET verhuisd worden"
                )
            );
        }
    }
    
   public function action_movePagepositionAfter() {
        $movedpage = $_POST["source"];
        $referencepage = $_POST["destination"];
        $pageid = substr($movedpage,9);
        $refid = substr($referencepage,9);
        if (Wi3::inst()->sitearea->pagepositions->moveAfter($pageid, $refid)) {
            
            // Remove cache of everything, since we do not know how this change affects the site
            Wi3::inst()->cache->removeAll();

            echo json_encode(
                Array(
                    "alert" => "pagina is verhuisd"
                )
            );
        } else {
            echo json_encode(
                Array(
                    "alert" => "pagina kon NIET verhuisd worden"
                )
            );
        }
    }
    
    public function action_movePagepositionUnder() {
        $movedpage = $_POST["source"];
        $referencepage = $_POST["destination"];
        $pageid = substr($movedpage,9);
        $refid = substr($referencepage,9);
        if (Wi3::inst()->sitearea->pagepositions->moveUnder($pageid, $refid)) {
            
            // Remove cache of everything, since we do not know how this change affects the site
            Wi3::inst()->cache->removeAll();

            echo json_encode(
                Array(
                    "alert" => "pagina is verhuisd"
                )
            );
        } else {
            echo json_encode(
                Array(
                    "alert" => "pagina kon NIET verhuisd worden"
                )
            );
        }
    }
    
    public function action_deletePageposition() {
        $pagename = $_POST["pagename"];
        $pageid = substr($pagename,9);
        // A call to pagepositions->delete will delete the pagepositions and descendants recursively, as well as their connected pages
        if (Wi3::inst()->sitearea->pagepositions->delete($pageid)) {
            
            // Remove cache of everything, since we do not know how this change affects the site
            Wi3::inst()->cache->removeAll();

            echo json_encode(
                Array(
                    "alert" => "pagina is verwijderd"
                )
            );
        } else {
            echo json_encode(
                Array(
                    "alert" => "pagina kon NIET verwijderd worden"
                )
            );
        }
    }
    
    public function action_startEditPagepositionSettings() {
        $pageid = substr($_POST["pagepositionname"],9);
                
        // For now, just redirect immediately to the first page under this pageposition
        $pageposition = Wi3::inst()->model->factory("site_pageposition", Array("id" => $pageid))->load();
        Wi3::inst()->sitearea->pageposition  = $pageposition;
        Event::instance("wi3.init.sitearea.pageposition.loaded")->execute();
        foreach($pageposition->pages as $page)
        {
            $_POST["pageid"] = $page->id;
            break;
        }
        return $this->action_startEditPageSettings();
        
        // TODO: proper UI etc for multiple pages under one pageposition
        /*
        $editview = View::factory("adminarea/menu/ajax/pagepositionsettings");
        $editview->site = Wi3::inst()->sitearea->site;
        if (is_numeric($pageid) AND !empty($pageid)) {
            $page = Wi3::inst()->model->factory("site_pageposition", Array("id" => $pageid))->load();
            Wi3::inst()->sitearea->pageposition  = $page;
            Event::instance("wi3.init.sitearea.pageposition.loaded")->execute();
            if ($page->loaded()) {
                $editview->page = $page;
                echo json_encode(
                    Array(
                        "scriptsbefore" => Array("$('#menu_pagesettings_tabs').hide();"),
                        "dom" => Array(
                            "fill" => Array("#menu_pagesettings_tabs" => $editview->render() )
                        ),
                        "scriptsafter" => Array("adminarea.menu_pagesettings_enable();", "$('#menu_pagesettings_tabs').show();", "$('#pagetitle').focus()"),
                    )
                );
            }
        }*/
    }
    
    public function action_startEditPageSettings() {
        //$pageid = substr($_POST["pagename"],9);
        $pageid = $_POST["pageid"];
        $editview = View::factory("adminarea/menu/ajax/pagesettings");
        $editview->site = Wi3::inst()->sitearea->site;
        if (!empty($pageid) AND is_numeric($pageid)) {
            $page = Wi3::inst()->sitearea->setpage("_".$pageid); // the _ prefixes a numeric page-ID
            if ($page->loaded()) {
                $editview->page = $page;
                echo json_encode(
                    Array(
                        "scriptsbefore" => Array("$('#menu_pagesettings_tabs').hide();"),
                        "dom" => Array(
                            "fill" => Array("#menu_pagesettings_tabs" => $editview->render() )
                        ),
                        "scriptsafter" => Array("adminarea.menu_pagesettings_enable();", "$('#menu_pagesettings_tabs').show();", "$('#pagetitle').focus()"),
                    )
                );
            }
        }
    }
    
    public function action_editPageSettings($pageid) {
        if (is_numeric($pageid) AND !empty($_POST)) {
            $page = Wi3::inst()->sitearea->setpage("_".$pageid); // the _ prefixes a numeric page-ID
            Wi3::inst()->acl->grant("admin", $page);
            try {
                // Check admin rights
                Wi3::inst()->acl->check($page);
                $oldname = $page->longtitle;
                
                // strip ID and Slug
                unset($_POST["id"]);
                unset($_POST["slug"]);
                foreach($_POST as $name => $post) {                  
                    if ($name == "visible") {
                        $page->visible = ($post === "0" ? "0" : "1");
                    } else {
                        // Also change slug if title is changed
                        if ($name == "longtitle") {
                            // Find a unique slug
                            $slug = strtolower($post);
                            $counter = 0;
                            while($p = Wi3::inst()->model->factory("site_page")->set("slug", $slug)->load() AND $p->loaded() === true AND $p->id != $page->id AND $counter < 100) {
                                $counter++;
                                $slug = $post . " " . $counter;
                            }
                            // Only set if the slug has changed in something else than lowercase - uppercase
                            // A change in lower <> upper causes validation issues
                            // and slugs should always be lowercase anyway
                            if (strtolower($page->slug) !== strtolower($slug)) {
                               $page->slug = $slug;
                            }
                        }
                        $page->{$name} = $post;
                    }
                }
                
                
                // Remove cache of everything, since we do not know how this change affects the site
                Wi3::inst()->cache->removeAll();

                $page->update();
                echo json_encode(
                    Array(
                        "alert" => "Pagina-eigenschappen van '" . $oldname . "' succesvol gewijzigd!.",
                        "dom" => Array(
                            "fill" => Array("#treeItem_" . $page->pageposition->id   . " > span > a" => $page->longtitle)
                        ),
                        "scriptsbefore" => Array("adminarea.menu_editdiv_hide()")
                    )
                );
            }
            catch(Exception $e) {
                echo json_encode(
                Array(
                    "alert" => "Pagina-eigenschappen konden NIET gewijzigd worden."
                )
                );
            }
        } else {
            echo json_encode(
                Array(
                    "alert" => "Pagina-eigenschappen konden NIET gewijzigd worden."
                )
            );
        }
    }
    
    public function action_editPageRedirectSettings($pageid) {
        if (is_numeric($pageid) AND !empty($_POST)) {
            $page = Wi3::inst()->sitearea->setpage("_".$pageid); // the _ prefixes a numeric page-ID
            Wi3::inst()->acl->grant("admin", $page);
            try {
                Wi3::inst()->acl->check($page);
                $page->redirecttype = $_POST["redirect_type"];
                $page->redirect_wi3 = $_POST["redirect_wi3"];
                $page->redirect_external = $_POST["redirect_external"];

                // Remove cache of everything, since we do not know how this change affects the site
                Wi3::inst()->cache->removeAll();
                
                //save page and return
                $page->update();
                echo json_encode(
                    Array(
                        "alert" => "Pagina-eigenschappen van '" . $page->longtitle . "' succesvol gewijzigd!.",
                    )
                );
            }
            catch(Exception $e)
            {
                echo json_encode(
                Array(
                    "alert" => "Doorverwijzings-eigenschappen konden NIET gewijzigd worden."
                )
                );
            }
        } else {
            echo json_encode(
                Array(
                    "alert" => "Pagina-eigenschappen konden NIET gewijzigd worden."
                )
            );
        }
    }

} // End Menu Ajax Controller
