<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Controller_Pagefiller_Default_Component_Simpleblog extends Controller_ACL
    {
    
        public $template;
    
        public function before() 
        {
            Wi3::inst()->acl->grant("*", $this, "login"); // Everybody can access login and logout function in this controller
            Wi3::inst()->acl->grant("*", $this, "logout");
            Wi3::inst()->acl->grant("admin", $this); // Admin role can access every function in this controller
            Wi3::inst()->acl->check($this);
        }
        
        public function login()
        {
            
        }
        
        public static function view($viewname)
        {
            // Make this component view extend the base template, with their locations set to the component folders
            $componenturl = Wi3::inst()->urlof->pagefillerfiles("default") . "components/simpleblog/";
            $componentpath = Wi3::inst()->pathof->pagefiller("default") . "components/simpleblog/";
            $componentbaseview = Wi3_Baseview::instance('imagecomponentbaseview', array(
                'javascript_url' => $componenturl.'static/javascript/', 
                'javascript_path' => $componentpath.'static/javascript/',
                'css_url' => $componenturl.'static/css/',
                'css_path' => $componentpath.'static/css/'
            )); 
            $componentview = View::factory()->set("this", $componentbaseview);
            $componentview->set_filepath($componentpath.'views/'.$viewname.EXT); // set_filepath sets a complete filename on the View
            return $componentview;
        }
    
        public function action_startEdit() 
        {
            $fieldid = $_POST["fieldid"];
            $field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();
            $html = $this->view("edit")->set("field", $field)->render();
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
        
        public function action_edit() 
        {
            // Load field, and the image-date field that connects to it
            $fieldid = $_POST["fieldid"];
            $field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();
            $data = Wi3::inst()->model->factory("site_data")->setref($field)->setname("image")->load();
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
                        "0" => "$('[type=field][fieldid=" . $fieldid . "] [type=fieldcontent] > img').attr('src', '" . Wi3::inst()->urlof->sitefiles . "data/uploads/200/" . $file->filename . "');"
                    )
                )
            );
        }
        
    }

?>
