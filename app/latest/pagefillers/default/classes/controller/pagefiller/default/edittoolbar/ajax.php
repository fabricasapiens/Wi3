<?php defined('SYSPATH') or die('No direct script access.');

// A rule must allow access to adminarea.login for everybody
// AACL should try to auto-login a user when it has not logged in yet
// If the check fails, bootstrap.php should send the user to $controller/login
// For the sitearea controller, there is no AACL check on the controller/action, but rather on a page. Redirect will then be to $site->errorpage
class Controller_Pagefiller_Default_Edittoolbar_Ajax extends Controller_ACL {
        
    public $template;
    
    public static $responseoptions = Array(); // Array that e.g. a component can use to put information in. The information is subsequently used for creating certain responses.
    
    public function before() 
    {
        // Check whether this controller (fills in current action automatically) can be accessed
        Wi3::inst()->acl->grant("admin", $this); // Admin role can access every function in this controller
        Wi3::inst()->acl->check($this);
        // Check if the user gets here via an AJAX POST, and not via a sneaky GET in an Iframe on a weird site
        $ajaxpost = (Request::$is_ajax AND Request::$method=="POST");
        if (!$ajaxpost) { exit; }
        // TODO: A non-admin user could have injected javascript code that calls saveAllEditableBlocks. This will go ahead once an admin opens the page, deleting all content... 
        // Thus: Non-admin users are NOT allowed to insert any script code, onclick, onmouseover events etc etc. That is complicated...
        // Go with a Whitelist, and only allow text inside spans, p, h1, h2, h3, div and that's it! Other elements like links and images should be done with fields...
        
        // Nice idea for differentiating URLS between users, but does not work against injection because js code is on the page regardless of the URL: an editing-page should have the current username in the URL, and a check is executed, whether that username matches the current logged-in username. The attacker can still inject a document.location.href to an admin-url, in which then the attack can be executed. However, we should not allow admin-pages that come from a redirect...
    }
    
    protected function setview($name)
    {
        $this->template = View::factory($name);
    }
    
    public function action_saveAllEditableBlocks()
    {
        // Add the <cms> element to the Purifier settings 
        Security::addpurifierelement("cms", Array(
            "attributes" => Array(
                'type' => 'Text',
                'fieldid' => 'Text',
                'name' => 'Text',
                'style_float' => 'Text',
                'style_width' => 'Text',
                'style_padding' => 'Text',       
            )                 
        ));
        
        
        // TODO: per-user checking for editing-access to this page
        // an admin-role is assumed for bare login-access to the adminarea, other roles should define access to individual pages
        $page = Wi3::inst()->model->factory("site_page")->set("id", $_POST["pageid"])->load();
        $html = Wi3::inst()->originalpost["html"];
        $allfields = Array(); // This is used to check which fields are in the HTML. If some have been removed, we should remove them here in the back-end as well: keep the DB clean :-)
        // Get all editable blocks *within fields* and save them first
        phpQuery::newDocument($html); // Give PHPQuery a context to work with
        $editableblockswithinfields = pq("[type=field] [type=editableblock][contenteditable=true]");
        foreach($editableblockswithinfields as $editableblock)
        {
            $name = pq($editableblock)->attr("name");
            $refname = pq($editableblock)->attr("ref");
            if (empty($refname)) { $refname = "field"; }
            if ($refname == "field")
            {
                // Get field id and set the ref accordingly!
                $refid = pq($editableblock)->parents("[type=field]")->eq(0)->attr("fieldid");
                $ref = Wi3::inst()->model->factory("site_field")->set("id", $refid)->load();
            }
            else if ($refname == "page")
            {
                $ref = $page;
            }
            // Save in data object
            $content = pq($editableblock)->html();
            $content = Security::xss_clean($content); // Clean the content, to prevent user XSS attacks
            $data = Wi3::inst()->model->factory("site_data")->setref($ref)->set("name",$name)->load();
            $data->data = $content;
            // Save the data
            if ($data->loaded())
            {
                $data->update(); // If exists, then update
            }
            else
            {
                $data->create(); // If it does not yet exist, create it
            }
            // Remove the editableblocks, so that they won't get processed in the next part of this function
            pq($editableblock)->remove();
        }
        // Get all editable blocks, collapse the fields therein, and save them 
        $editableblocks = pq("[type=editableblock][contenteditable=true]");
        foreach($editableblocks as $editableblock)
        {
            $name = pq($editableblock)->attr("name");
            // Extract all fields, and replace them with <cms> expression
            $fields = pq($editableblock)->find("[type=field]");
            foreach($fields as $field)
            {
                $fieldid = pq($field)->attr("fieldid");
                $allfields[$fieldid] = $fieldid; // For processing later
                $padding = pq($field)->attr("style_padding");
                $float = pq($field)->attr("style_float");
                $width = pq($field)->attr("style_width");
                //$parent = pq($field)->parent()->html();
                pq($field)->replaceWith("<cms type='field' style_float='" . $float . "' style_width='" . $width. "' style_padding='" . $padding . "' fieldid='" . $fieldid . "'></cms>");
                //$parent = pq($field)->parent()->html();
            }
            // Save in data object
            $content = pq($editableblock)->html();
            // Postprocessing on the raw content (also executed while rendering the blocks in pagefiller/default.php)
            /* Because there is a 'bug' within phpQuery that it wants no DIVs within P elements, even not when they are display: inline-block;
               Thus, this
               
                <p>sometext
                    <div>field</div>
                othertext</p> 
                
                turns into 
                
                <p>sometext</p>
                <div>field</div>
                sometext</p>
                
               Thus, we need to remove that </p> before the <div>
               It is easy to find only those </p> because there is one omitted *after* the div
            */
            $content = preg_replace("@</(.+)>[\n\r]*(<cms[^>]*></cms>)(?!<\\1>)@i", "$2", $content); // The \\1 is an in-pattern backreference. The flag i results in insensitivity for case. 
            $content = Security::xss_clean($content); // Clean the content, to prevent user XSS attacks
            $data = Wi3::inst()->model->factory("site_data")->setref($page)->set("name",$name)->load(); // the setref($page) ensures that it is impossible to illegally set data for a field not in the current page
            $data->data = $content;
            // Save the data
            if ($data->loaded())
            {
                $data->update(); // If exists, then update
            }
            else
            {
                $data->create(); // If it does not yet exist, create it
            }
        }
        // Finally, check the fields that belong to this page, and remove those that are not in the $allfields array...
        $fields = Wi3::inst()->model->factory("site_field")->setref($page)->load(Null, FALSE); // FALSE for no limit to the amount of results
        foreach($fields as $field)
        {
            if (!isset($allfields[$field->id]))
            {
                $field->delete();
                // Remove the different associated field-data, for as far that is not already done...
                $datas = Wi3::inst()->model->factory("site_data")->setref($field)->load(NULL, FALSE);
                foreach($datas as $data)
                {
                    $data->delete();
                }
                $arrays = Wi3::inst()->model->factory("site_array")->setref($field)->load(NULL, FALSE);
                foreach($arrays as $array)
                {
                    $array->delete();
                }
            }
        }
        
        // Return success
        echo json_encode(
            Array(
                "alert" => "inhoud opgeslagen."
            )
        );
    }
    
    public function action_insertfield()
    {
        // TODO: per-user checking for editing-access to this page
        // an admin-role is assumed for bare login-access to the adminarea, other roles should define access to individual pages
        $page = Wi3::inst()->model->factory("site_page")->set("id", $_POST["pageid"])->load();
        Wi3::inst()->sitearea->setpage($page);
        // Create a new field, belonging to this page
        $fieldtype = $_POST["fieldtype"];
        $field = Wi3::inst()->model->factory("site_field")->setref($page)->set("type", $fieldtype)->create();
        if (!isset($this::$responseoptions["inserttype"])) 
        { 
            $this->responseoptions["inserttype"] = "insertbefore";
        }
        // Return a render of the field
        $html = Pagefiller_default::view("fieldrender_edit")->set("field", $field)->render();
        // Return success
        echo json_encode(
            Array(
                "scriptsbefore" => Array("0" => "wi3.pagefillers.default.edittoolbar.insertFieldHtml(\"" . $field->id . "\", \"" . base64_encode($html) . "\", \"" . $this::$responseoptions["inserttype"] . "\")"),
                // And make sure that the page is saved
                "scriptsafter" => Array("0" => "wi3.pagefillers.default.edittoolbar.saveAllEditableBlocks()")
            )
        );
    }
    
    public function action_removefield()
    {
        // TODO: per-user checking for editing-access to this page
        // an admin-role is assumed for bare login-access to the adminarea, other roles should define access to individual pages
        $page = Wi3::inst()->model->factory("site_page")->set("id", $_POST["pageid"])->load();
        $fieldid = $_POST["fieldid"];
        $field = Wi3::inst()->model->factory("site_field")->setref($page)->set("id", $fieldid)->load();
        // Remove field, and let the field do some processing with its data, if it wants to 
        if ($field->delete())
        {
            // Remove the different associated field-data, for as far that is not already done...
            $datas = Wi3::inst()->model->factory("site_data")->setref($field)->load(NULL, FALSE);
            foreach($datas as $data)
            {
                $data->delete();
            }
            $arrays = Wi3::inst()->model->factory("site_array")->setref($field)->load(NULL, FALSE);
            foreach($arrays as $array)
            {
                $array->delete();
            }
            
            // Base response on whether 'replacefieldwith' is set or not 
            if (isset($this::$responseoptions["replacefieldwith"]) AND !empty($this::$responseoptions["replacefieldwith"]))
            {
                // Replace the field with the 'replacefieldwith'
                echo json_encode(
                    Array(
                        "dom" => Array(
                            "replace" => Array("[type=field][fieldid=" . $fieldid . "]" => $this::$responseoptions["replacefieldwith"])
                        )
                    )
                );
            }
            else
            {
                // Remove the field on the page
                echo json_encode(
                    Array(
                        "dom" => Array(
                            "remove" => Array("[type=field][fieldid=" . $fieldid . "]")
                        )
                    )
                );
            }
        }
    }

}
