<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Pagefiller_Default_Component_Simpleblog extends Wi3_Base 
    {
        public function render($field)
        {
            
            // Load the articles, and create a basic article if there are none
            $articles = Wi3::inst()->model->factory("site_array")->setref($field)->setname("articles")->load();
            if (!$articles->loaded())
            {
                $articles->create();
            }
            if (count($articles) == 0)
            {
                // Create an article 
                $articleid = Wi3::inst()->date_now();
                //$articles->
            }
        
            // Load the image that is associated with this field 
            $imagedata = Wi3::inst()->model->factory("site_data")->setref($field)->setname("image")->load();
            // If data does not exist, create it
            if (!$imagedata->loaded())
            {
                $imagedata->create();
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
                return "<img src='" . Wi3::inst()->urlof->sitefiles . "data/uploads/200/" . $file->filename . "'> <cms type='editableblock' name='description'>Beschrijving van afbeelding</cms>";
            }
        }
        
        public function fieldactions($field)
        {
            return "<a href='javascript:void(0)' onclick='wi3.request(\"pagefiller_default_component_simpleblog/startEdit\", {fieldid: " . $field->id . "})'>artikelen beheren</a>";
        }
    }

?>
