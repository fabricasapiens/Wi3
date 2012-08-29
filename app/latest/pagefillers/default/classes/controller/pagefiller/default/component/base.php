<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Controller_Pagefiller_Default_Component_Base extends Controller_ACL
    {
    
        public $template;
		
		// These are mandatory
        public static $componentname = "";
		public static $model = Array();
		
		public function before() 
        {
            Wi3::inst()->acl->grant("*", $this, "login"); // Everybody can access login and logout function in this controller
            Wi3::inst()->acl->grant("*", $this, "logout");
            Wi3::inst()->acl->grant("admin", $this); // Admin role can access every function in this controller
            Wi3::inst()->acl->check($this);
        }
		
		public function login() {
			// Enforce the client to login again
			echo json_encode(
                Array(
                    "scriptsbefore" => Array(
                        "0" => "wi3.pagefillers.default.edittoolbar.reAuthenticate();"
                    )
                )
            );
		}
    
        public function view($viewname, $usecomponentlocation = TRUE)
        {
			if ($usecomponentlocation == FALSE) {
				// Use views from component_base
				$componenturl = Wi3::inst()->urlof->pagefillerfiles("default");
				$componentpath = Wi3::inst()->pathof->pagefiller("default");
			} else {
				// Use views from inherited component
				$componenturl = Wi3::inst()->urlof->pagefillerfiles("default") . "components/" . $this::$componentname . "/";
				$componentpath = Wi3::inst()->pathof->pagefiller("default") . "components/" . $this::$componentname . "/";
			}
            // Make this component view extend the base template, with their locations set to the above folders
            $componentbaseview = Wi3_Baseview::instance($this::$componentname.'baseview', array(
                'javascript_url' => $componenturl.'static/javascript/', 
                'javascript_path' => $componentpath.'static/javascript/',
                'css_url' => $componenturl.'static/css/',
                'css_path' => $componentpath.'static/css/'
            )); 
            $componentview = View::factory()->set("this", $componentbaseview);
            $componentview->set_filepath($componentpath.'views/'.$viewname.EXT); // set_filepath sets a complete filename on the View
            return $componentview;
        }
		
		protected function fielddata($field) {
			$dataobject = Wi3::inst()->model->factory("site_array")->setref($field)->setname("image")->load();
			return $dataobject;
		}
		
		public function action_startEdit() 
        {
            $fieldid = $_POST["fieldid"];
            $field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();
			$dataobject = $this->fielddata($field);
            // If data does not exist, create it
            if (!$dataobject->loaded())
            {
                $dataobject->create();
            }
            $html = $this->view("component_base_edit", FALSE)->set("field", $field)->set("model", $this::$model)->set("data", $dataobject)->set("componentname", $this::$componentname)->render();
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
            $fieldid = $_POST["fieldid"];
            $field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();
            $dataobject = $this->fielddata($field);
            // If data does not exist, create it
            if (!$dataobject->loaded())
            {
                $dataobject->create();
            }
            // Update data field with data
            foreach($_POST as $index => $value) {
				$dataobject->{$index} = $value;
			}
            $dataobject->update();
            
			// Let the Front-End rerender the affected field
            echo json_encode(
                Array(
                    "scriptsbefore" => Array(
                        "0" => "wi3.pagefillers.default.renderFieldHtml('" . $fieldid . "', '" . base64_encode($field->render()) . "');"
                    )
                )
            );
        }
        
        public function field($fieldid) {
            // Load the field where this component is attached to
            $field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();
            if ($field->loaded() == false) {
                return null;
            } else {
                return $field;
            }
        }
 
    }

?>
