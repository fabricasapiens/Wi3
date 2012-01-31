<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Pagefiller_Default_Component_imageshop extends Pagefiller_Default_Component_Base
    {
    
        // This function receives all sorts of events related to the field with this type
        public function fieldevent($eventtype, $field)
        {
            if ($eventtype == "create")
            {
                // Set the inserttype
                Controller_Pagefiller_Default_Edittoolbar_Ajax::$responseoptions["inserttype"] = "replace"; // Replace the current selection
                // Create the data that is associated with this field
                Wi3::inst()->model->factory("site_data")->setref($field)->setname("folder")->create();
            }
            else if ($eventtype == "delete")
            {
                // Destory data
                Wi3::inst()->model->factory("site_data")->setref($field)->setname("folder")->delete();
            }
        }
    
        public function render($field)
        {
            // Load the image that is associated with this field 
            $imagedata = Wi3::inst()->model->factory("site_data")->setref($field)->setname("folder")->load();
            if (empty($imagedata->data))
            {
                return "Geen afbeeldingsmap ingesteld.";
            }
            else
            {
                // fetch image-id and render the image
                $fileid = $imagedata->data;
                $folder = Wi3::inst()->model->factory("site_file")->set("id", $fileid)->load();
                // Load all images for this folder
                $query = DB::select()->
                        where($folder->left_column, ">", $folder->{$folder->left_column})->
                        and_where($folder->right_column, "<", $folder->{$folder->right_column})->
                        order_by($folder->left_column);
                $files = $folder->load($query, NULL); // NULL for no limit
                
                return $this->view("view")->set("files", $files)->render();
            }
        }
        
        public function fieldactions($field)
        {
            return "<a href='javascript:void(0)' onclick='wi3.request(\"pagefiller_default_component_imageshop/startEditImages\", {fieldid: " . $field->id . "})'>afbeeldingen wijzigen</a>";
        }
    }

?>
