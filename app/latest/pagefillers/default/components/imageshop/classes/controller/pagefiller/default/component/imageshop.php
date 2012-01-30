<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Controller_Pagefiller_Default_Component_imageshop extends Controller_ACL
    {
    
        public $template;
        protected $componentname = "imageshop";
    
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
        
        public function view($viewname)
        {
            // Make this component view extend the base template, with their locations set to the component folders
            $componenturl = Wi3::inst()->urlof->pagefillerfiles("default") . "components/" . $this->componentname . "/";
            $componentpath = Wi3::inst()->pathof->pagefiller("default") . "components/" . $this->componentname . "/";
            $componentbaseview = Wi3_Baseview::instance($this->componentname.'baseview', array(
                'javascript_url' => $componenturl.'static/javascript/', 
                'javascript_path' => $componentpath.'static/javascript/',
                'css_url' => $componenturl.'static/css/',
                'css_path' => $componentpath.'static/css/'
            )); 
            $componentview = View::factory()->set("this", $componentbaseview);
            $componentview->set_filepath($componentpath.'views/'.$viewname.EXT); // set_filepath sets a complete filename on the View
            return $componentview;
        }
    
        public function action_startEditImages() 
        {
            $fieldid = $_POST["fieldid"];
            $field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();
            $html = $this->view("edit".$this->componentname)->set("field", $field)->render();
            echo json_encode(
                Array(
                    "scriptsbefore" => Array(
                        "0" => "wi3.popup.content='" . addcslashes($html, "'") . "'"
                    ),
                    /*"dom" => Array(
                        "fill" => Array(
                            "div[type=popuphtml]" => $html
                        )
                    ),*/
                    "scriptsafter" => Array(
                        "0" => "wi3.popup.show();"
                    )
                )
            );
        }
        
        public function action_editImage() 
        {
            // Load field, and the image-date field that connects to it
            $fieldid = $_POST["fieldid"];
            $field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();
            $data = Wi3::inst()->model->factory("site_data")->setref($field)->setname("folder")->load();
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
                        "0" => "$('[type=field][fieldid=" . $fieldid . "] [type=fieldcontent]').html('" . $field->render() . "');"
                    )
                )
            );
        }
        
        public function action_order() {
            // Send an email to client and to the pre-set emailaddress
            $clientname = $_POST["name"];
            $clientemailaddress = $_POST["emailaddress"];
            $presetemailaddress = "info@fabricasapiens.nl";
            // Create mail
            $message = Kohana::debug($_POST);
            $subject = "Bestelling fotosite";
            // Send mail to 'us' and to client
            mail($presetemailaddress, $subject, $message);
            //mail($clientemailaddress, $subject, $message);
            // Echo result
            echo json_encode(
                Array(
                    "scriptsbefore" => Array(
                        "0" => "$(wi3.popup.getDOM()).fadeOut(100,function() { $(this).html('Bestelling succesvol geplaatst!').fadeIn(); } );"
                    )
                )
            );
        }
        
    }

?>
