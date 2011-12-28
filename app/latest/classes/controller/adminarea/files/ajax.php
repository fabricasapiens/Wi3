<?php defined('SYSPATH') or die('No direct script access.');

// Controller_Login provides a login() function that will only simply show a login-form
// A rule must allow access to adminarea.login for everybody
// AACL should try to auto-login a user when it has not logged in yet
// If the check fails, bootstrap.php should send the user to $controller/login
// For the sitearea controller, there is no AACL check on the controller/action, but rather on a file. Redirect will then be to $site->errorfile
class Controller_Adminarea_Files_Ajax extends Controller_ACL {
        
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
    
    public function action_moveFileBefore() {
        $movedfile = $_POST["source"];
        $referencefile = $_POST["destination"];
        $fileid = substr($movedfile,9);
        $refid = substr($referencefile,9);
        if (Wi3::inst()->sitearea->files->moveBefore($fileid, $refid)) {
            // TODO: caching
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
    
   public function action_moveFileAfter() {
        $movedfile = $_POST["source"];
        $referencefile = $_POST["destination"];
        $fileid = substr($movedfile,9);
        $refid = substr($referencefile,9);
        if (Wi3::inst()->sitearea->files->moveAfter($fileid, $refid)) {
            // TODO: caching
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
    
    public function action_moveFileUnder() {
        $movedfile = $_POST["source"];
        $referencefile = $_POST["destination"];
        $fileid = substr($movedfile,9);
        $refid = substr($referencefile,9);
        if (Wi3::inst()->sitearea->files->moveUnder($fileid, $refid)) {
            //#remove cache of the *complete* site!
            //Wi3::$cache->field_delete_all_wheresite(Wi3::$site);
            //#
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
    
    public function action_deleteFile() {
        $filename = $_POST["filename"];
        $fileid = substr($filename,9);
        // A call to files->delete will delete the files and descendants recursively, as well as their connected files
        if (Wi3::inst()->sitearea->files->delete($fileid)) {
            //#remove cache of the *complete* site!
            //Wi3::$cache->field_delete_all_wheresite(Wi3::$site);
            //#
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
       
    public function action_startEditFileSettings()
    {
        $fileid = substr($_POST["fileid"],9);
        $editview = View::factory("adminarea/files/ajax/filesettings");
        $editview->site = Wi3::inst()->sitearea->site;
        if (!empty($fileid) AND is_numeric($fileid)) {
            $file = Wi3::inst()->model->factory("site_file")->set("id", $fileid)->load();
            if ($file->loaded()) {
                $editview->file = $file;
                echo json_encode(
                    Array(
                        "scriptsbefore" => Array("$('#files_filesettings_tabs').hide();"),
                        "dom" => Array(
                            "fill" => Array("#files_filesettings_tabs" => $editview->render() )
                        ),
                        "scriptsafter" => Array("adminarea.files_filesettings_enable();", "$('#files_filesettings_tabs').show();", "$('#filetitle').focus()"),
                    )
                );
            }
        }
    }
    
    public function action_editFileSettings($fileid) {
        if (is_numeric($fileid) AND !empty($_POST)) {
            $file = Wi3::inst()->model->factory("site_file")->set("id", $fileid)->load();
            Wi3::inst()->acl->grant("admin", $file);
            try {
                Wi3::inst()->acl->check($file);
                $oldname = $file->title;
                
                foreach($_POST as $name => $post) 
                {                    
                    if ($name == "visible") 
                    {
                        //$file->visible = ($post === "0" ? "0" : "1");
                    } 
                    else if ($name == "viewright" OR $name == "editright" OR $name == "adminright") 
                    {
                        //check for admin privileges
                        Wi3::inst()->acl->check($file);
                        $file->{$name} = $post;
                    } 
                    else 
                    {
                        $file->{$name} = $post;
                    }
                }
                
                // TODO: caching
                
                $file->update();
                echo json_encode(
                    Array(
                        "alert" => "Eigenschappen van '" . $oldname . "' succesvol gewijzigd!",
                        //"dom" => Array(
                        //    "fill" => Array("#treeItem_" . $fileid   . " > span > a" => $file->longtitle)
                        //),
                        //"scriptsbefore" => Array("adminarea.menu_editdiv_hide()")
                    )
                );
            }
            catch(Exception $e)
            {
                echo json_encode(
                Array(
                    "alert" => "Eigenschappen konden NIET gewijzigd worden."
                )
                );
            }
        } else {
            echo json_encode(
                Array(
                    "alert" => "Eigenschappen konden NIET gewijzigd worden."
                )
            );
        }
    }

} // End Files Ajax Controller
