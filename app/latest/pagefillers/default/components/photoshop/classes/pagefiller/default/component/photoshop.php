<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Pagefiller_Default_Component_Photoshop extends Wi3_Base 
    {
    
        // This function receives all sorts of events related to the field with this type
        public function fieldevent($eventtype, $field)
        {
            if ($eventtype == "create")
            {
                // Set the inserttype
                Controller_Pagefiller_Default_Edittoolbar_Ajax::$responseoptions["inserttype"] = "replace"; // Replace the current selection
                // Create the data that is associated with this field
                Wi3::inst()->model->factory("site_data")->setref($field)->setname("images")->create();
            }
            else if ($eventtype == "delete")
            {
                // Destory data
                Wi3::inst()->model->factory("site_data")->setref($field)->setname("images")->delete();
            }
        }
    
        public function render($field)
        {
            // Load the images that are associated with this field 
            $imagedata = Wi3::inst()->model->factory("site_data")->setref($field)->setname("images")->load();
            if (empty($imagedata->data))
            {
                return "Geen afbeeldingen ingesteld";
            }
            else
            {
                // fetch image-id and render the image with its price
                $array = unserialize($imagedata->data);
                $result = "";
                foreach($array as $id => $information) {
                    $file = Wi3::inst()->model->factory("site_file")->set("id", $information->id)->load();
                    $result .= "<img src='" . Wi3::inst()->urlof->sitefiles . "data/uploads/200/" . $file->filename . "'>";
                    $result .= "<p>" . $information->price . "</p>"; 
                }
                return $result;
            }
        }
        
        public function fieldactions($field)
        {
            return "<a href='javascript:void(0)' onclick='wi3.request(\"pagefiller_default_component_photoshop/startEditImages\", {fieldid: " . $field->id . "})'>afbeeldingen wijzigen</a>";
        }
    }

?>
