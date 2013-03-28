<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Controller_Pagefiller_Default_Component_Livejavascript extends Controller_ACL
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
            $componenturl = Wi3::inst()->urlof->pagefillerfiles("default") . "components/livejavascript/";
            $componentpath = Wi3::inst()->pathof->pagefiller("default") . "components/livejavascript/";
            $componentbaseview = Wi3_Baseview::instance('livejavascriptcomponentbaseview', array(
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
            // Load field
            $fieldid = $_POST["fieldid"];
            $field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();
            // Load data array
            $data = Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->load();
            // Update array
            if(isset($_POST["code"])) {
				$data->code = $_POST["code"];
			}
            $data->update();
            
            echo json_encode(
                Array(
                    "scriptsbefore" => Array(
                        // TODO: make the sending of code work...
                        "0" => "window.wi3.livejavascript_examples['livejavascript_input_" . $field->id . "'].setCode(wi3.pagefillers.default.edittoolbar.fromBase64('" . base64_encode($data->code) . "'));",
                        "1" => "window.wi3.livejavascript_examples['livejavascript_input_" . $field->id . "'].run();",
                        "2" => "wi3.pagefillers.default.edittoolbar.hidePopup();"
                    )
                )
            );
        }
        
    }

?>
