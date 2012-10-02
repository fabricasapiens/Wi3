<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Controller_Pagefiller_Default_Component_Image extends Controller_Pagefiller_Default_Component_Base
    {

        public static $componentname = "image";
    
        public function action_startEditImage() 
        {
            $fieldid = $_POST["fieldid"];
            $field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();
            $html = $this->view("editImage")->set("field", $field)->render();
            echo json_encode(
                Array(
                    "dom" => Array(
                        "fill" => Array(
                            "div[type=popuphtml]" => $html
                        )
                    ),
                    "scriptsafter" => Array(
                        "0" => "wi3.pagefillers.default.edittoolbar.showPopup();"
                    )
                )
            );
        }
        
        public function action_editImage() 
        {
            // Load field, and the image-date field that connects to it
            $fieldid = $_POST["fieldid"];
            $field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();
            $data = Wi3::inst()->model->factory("site_data")->setref($field)->setname("image")->load();
            $widthobj = Wi3::inst()->model->factory("site_data")->setref($field)->setname("width")->load();
            $width = $widthobj->data;
            if (empty($width)) {
                $width = 200;
            }
            // If data does not exist, create it
            if (!$data->loaded())
            {
                $data->create();
            }
            // Update data field with image-id
            $fileid = $_POST["image"];
            $data->data = $fileid;
            $data->update();
            
            $file = Wi3::inst()->model->factory("site_file")->set("id", $fileid)->load();
            
            echo json_encode(
                Array(
                    "scriptsbefore" => Array(
                        "0" => "$('[type=field][fieldid=" . $fieldid . "] [type=fieldcontent] > img').attr('src', '" . Wi3::inst()->urlof->sitefiles . "data/uploads/" . $width . "/" . $file->filename . "');"
                    )
                )
            );
        }
        
    }

?>
