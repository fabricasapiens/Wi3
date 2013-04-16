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

    // Recursively save content, starting with the deepest
    function collapseFieldsAndSaveBlocks($parentBlock, $page, $allfields) {
    	$childBlocks = pq($parentBlock)->find("[type=field] [type=editableblock][contenteditable=true]");
    	// Recursively go a level deeper
    	foreach($childBlocks as $childBlock) {
    		$this->collapseFieldsAndSaveBlocks($childBlock, $page, $allfields);
    	}
    	// Now collapse fields to <cms> expressions and store in $allfields which fields have been collapsed
    	$this->saveFields($parentBlock, $allfields);
    	// Save parent block, now that the fields have been replaced with <cms> expressions
        $this->saveEditableBlockContent($parentBlock, $page);
        // Remove the editableblocks, so that they won't get processed in the next part of this function
        pq($parentBlock)->remove();
    }
    
    public function action_saveAllEditableBlocks()
    {
        // Add the <cms> element to the Purifier settings 
        Security::addpurifierelement("cms", Array(
            "attributes" => Array(
                'type' => 'Text',
                'fieldid' => 'Text',
                'fieldname' => 'Text',
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
        // The ArrayObject $allfields is used to check which fields are in the HTML. If some have been removed, we should remove them here in the back-end as well: keep the DB clean :-)
        // It is an ArrayObject so that it will be passed by reference
        $allfields = new ArrayObject();
        // Get all editable blocks *within fields* and save them, recursively, using recursive function
        $document = phpQuery::newDocument($html); // Give PHPQuery a context to work with
        $editableblockswithinfields = pq("[type=field] [type=editableblock][contenteditable=true]");
        foreach($editableblockswithinfields as $editableblock)
        {
        	$this->collapseFieldsAndSaveBlocks($editableblock, $page, $allfields);
        }
        // Process all editable blocks that are *not* within a field
        $this->processEditableBlocks($document, $page, $allfields);
        
        // Finally, check the fields that belong to this page, and remove those that are not in the $allfields array...
        $fields = Wi3::inst()->model->factory("site_field")->setref($page)->load(Null, FALSE); // FALSE for no limit to the amount of results
        foreach($fields as $field)
        {
            // Skip the fields that have a name. Those are the sitefields or pagefields set in the templates.
            if (empty($field->name) && !isset($allfields[$field->id]))
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

        // Remove cache of all pages, since we do not know how this change affects other pages
        Wi3::inst()->cache->removeAll();
        
        // Return success
        echo json_encode(
            Array(
                "alert" => "inhoud opgeslagen."
            )
        );
    }

    private function processEditableBlocks($node,$page,$allfields) {
        // Get all editable blocks, collapse the fields therein, and save them 
        $editableblocks = $node->find("[type=editableblock][contenteditable=true]");
        foreach($editableblocks as $editableblock)
        {
            // Collapse fields to <cms> expressions
            $this->saveFields($editableblock,$allfields);
            // Now that the fields have been replaced with <cms> expressions, save the data to the block
            $this->saveEditableBlockContent($editableblock,$page);
        }
    }

    private function saveFields($editableblock,$allfields) {
        // Extract all fields, and replace them with <cms> expression
        $fields = pq($editableblock)->find("[type=field]");
        foreach($fields as $field)
        {
            $fieldid = pq($field)->attr("fieldid");
            $allfields[$fieldid] = $fieldid; // !important! If a field does not end up in this array, it will get deleted!
            $padding = pq($field)->attr("style_padding");
            $float = pq($field)->attr("style_float");
            $width = pq($field)->attr("style_width");
            //$parent = pq($field)->parent()->html();
            pq($field)->replaceWith("<cms type='field' style_float='" . $float . "' style_width='" . $width. "' style_padding='" . $padding . "' fieldid='" . $fieldid . "'></cms>");
            //$parent = pq($field)->parent()->html();
        }
    }

    private function saveEditableBlockContent($editableblock,$page) {
        $name = pq($editableblock)->attr("name"); // used as id to save data
        $refname = pq($editableblock)->attr("ref");
        if (empty($refname)) { $refname = "field"; }
        if ($refname == "field")
        {
            // Get field id and set the ref accordingly!
            $refid = pq($editableblock)->parents("[type=field]")->eq(0)->attr("fieldid");
            if (empty($refid)) {
                $refname = "page";
            }
            $ref = Wi3::inst()->model->factory("site_field")->set("id", $refid)->load();
        }
        if ($refname == "page")
        {
            $ref = $page;
        }
        $content = pq($editableblock)->html();
        $content = Security::xss_clean($content); // Clean the content, to prevent user XSS attacks
        // Let the ref (either a field or a page) determine itself where to store the data
        $ref->saveEditableBlockContent($editableblock, $name, $content);
    }
    
    public function action_insertfield()
    {
        // TODO: (?) use generic class functions and consolidate with pagefiller-fieldcreation at runtime
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
