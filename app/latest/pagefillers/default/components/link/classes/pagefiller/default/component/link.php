<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Pagefiller_Default_Component_Link extends Wi3_Base 
    {
    
        // This function receives all sorts of events related to the field with this type
        public function fieldevent($eventtype, $field)
        {
            if ($eventtype == "create")
            {
                // Set the inserttype
                Controller_Pagefiller_Default_Edittoolbar_Ajax::$responseoptions["inserttype"] = "replace"; // This component is an inline component that replaces the current selection
                // Create the data that is associated with this field and save the selection text as our linktext
                $data = Wi3::inst()->model->factory("site_array")->setref($field)->setname("data");
                $data->destinationtype = "url";
                $data->url = "http://fabricasapiens.nl";
                $data->linktext = $_POST["selectiontext"];
                $data->create();
            }
            else if ($eventtype == "delete")
            {
                // Load data and fetch linktext
                $data = Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->load();
                if (isset($data->linktext))
                {
                    Controller_Pagefiller_Default_Edittoolbar_Ajax::$responseoptions["replacefieldwith"] = $data->linktext;
                }
            }
        }
    
        public function render($field)
        {
            // Load the data that is associated with this field 
            $data = Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->load();
            $destinationtype = $data->destinationtype;
            // Create URL based on the destinationtype
            if ($destinationtype == "url")
            {
                $url = $data->url;
            }
            else if ($destinationtype == "page")
            {
                $pageid = Wi3::inst()->model->factory("site_data")->setref($field)->setname("url")->load();
                $page = Wi3::inst()->model->factory("site_page")->set("id", $pageid)->load();
                if ($page->loaded())
                {
                    $url = Wi3::inst()->urlof->page($page->slug);
                }
                else
                {
                    $url = "";
                }
            }
            // Mark the field as a inline-element 
            $field->options["style"]["display"] = "inline";
            return "<a href='" . $url . "'>" . $data->linktext . "</a>";
        }
        
        public function fieldactions($field)
        {
            return "<a href='javascript:void(0)' onclick='wi3.request(\"pagefiller_default_component_link/startEdit\", {fieldid: " . $field->id . "})'>link wijzigen</a>";
        }
    }

?>
