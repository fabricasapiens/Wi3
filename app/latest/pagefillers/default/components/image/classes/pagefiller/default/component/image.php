<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Pagefiller_Default_Component_Image extends Wi3_Base 
    {
    
        // This function receives all sorts of events related to the field with this type
        public function fieldevent($eventtype, $field)
        {
            if ($eventtype == "create")
            {
                // Set the inserttype
                Controller_Pagefiller_Default_Edittoolbar_Ajax::$responseoptions["inserttype"] = "replace"; // Replace the current selection
                // Create the data that is associated with this field
                $imagedata = Wi3::inst()->model->factory("site_data")->setref($field)->setname("image")->create();
                $imagewidth = Wi3::inst()->model->factory("site_data")->setref($field)->setname("width")->create();
            }
            else if ($eventtype == "delete")
            {
                
            }
        }
    
        public function render($field)
        {
            // Load the image that is associated with this field 
            $imagedata = Wi3::inst()->model->factory("site_data")->setref($field)->setname("image")->load();
            $width = $field->options["stylearray"]["width"];
            if (empty($width)) {
                // No hard value for width means that we can fallback to our stored value
                $width = Wi3::inst()->model->factory("site_data")->setref($field)->setname("width")->load()->data;
                if (empty($width)) {
                    $width = "200";
                }
            } else {
                // Hard value is set. Save to fallback
                if (strpos($width, "px") !== false) {
                    $width = substr($width, 0, -2);
                }
                $widthobj = Wi3::inst()->model->factory("site_data")->setref($field)->setname("width")->load();
                $widthobj->data = $width;
                $widthobj->updateorcreate();
            }
            if (empty($imagedata->data))
            {
                return "<img src='" . Wi3::inst()->urlof->pagefillerfiles . "components/image/static/images/noimage.png'> <cms type='editableblock' name='description'>Beschrijving van afbeelding</cms>";
            }
            else
            {
                // fetch image-id and render the image
                $fileid = $imagedata->data;
                $file = Wi3::inst()->model->factory("site_file")->set("id", $fileid)->load();
                return "<img src='" . Wi3::inst()->urlof->sitefiles . "data/uploads/" . $width . "/" . $file->filename . "'> <cms type='editableblock' name='description'>Beschrijving van afbeelding</cms>";
            }
        }
        
        public function fieldactions($field)
        {
            return "<a href='javascript:void(0)' onclick='wi3.request(\"pagefiller_default_component_image/startEditImage\", {fieldid: " . $field->id . "})'>afbeelding wijzigen</a>";
        }
    }

?>
