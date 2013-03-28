<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Pagefiller_Default_Component_Livejavascript extends Pagefiller_Default_Component_Base
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
                $data->code = "// Code";
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
            // Mark the field as a block-element 
            $field->options["stylearray"]["display"] = "block";
            // Return rendered view
            return $this->view("render")->set("code", $data->code)->set("fieldid", $field->id)->render();
        }
        
        public function fieldactions($field)
        {
            return "<a href='javascript:void(0)' onclick='wi3.request(\"pagefiller_default_component_livejavascript/startEdit\", {fieldid: " . $field->id . "})'>code wijzigen</a>";
        }
    }

?>
