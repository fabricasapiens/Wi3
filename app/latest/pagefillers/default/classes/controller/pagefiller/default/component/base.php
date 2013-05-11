<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Controller_Pagefiller_Default_Component_Base extends Controller_ACLBase
    {

        public $template;

		// These are mandatory
        public static $componentname = "abc";
        public static $component = null;
		public static $model = Array();

		public function before()
        {
            Wi3::inst()->acl->grant("*", $this, "login"); // Everybody can access login and logout function in this controller
            Wi3::inst()->acl->grant("*", $this, "logout");
            Wi3::inst()->acl->grant("admin", $this); // Admin role can access every function in this controller
            Wi3::inst()->acl->check($this);
            parent::before();
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
            $componentbaseview = Wi3_Baseview::instance($this::$componentname.'baseview_'.($usecomponentlocation?"true":"false"), array(
                'javascript_url' => $componenturl.'static/javascript/',
                'javascript_path' => $componentpath.'static/javascript/',
                'css_url' => $componenturl.'static/css/',
                'css_path' => $componentpath.'static/css/'
            ));
            $componentview = View::factory()->set("this", $componentbaseview);
            $componentview->set_filepath($componentpath.'views/'.$viewname.EXT); // set_filepath sets a complete filename on the View
            return $componentview;
        }

        protected function getComponent() {
            if ($this::$component === null) {
                $componentname = "Pagefiller_Default_Component_" . $this::$componentname;
                $this::$component = new $componentname;
            }
            return $this::$component;
        }

        protected function getModel() {
            $component = $this->getComponent();
            return $component::$model;
        }

        protected function getTypeForModelKey($key) {
        	$model = $this->getModel();
        	if (!isset($model[$key])) {
        		return false;
        	}
        	return $model[$key]["type"];
        }

        protected function ensureModelExists($dataobject,$savedata=true) {
            $changed = false;
            foreach($this->getModel() as $key => $info) {
                if (!isset($dataobject->{$key})) {
                	$type = $this->getTypeForModelKey($key);
                	if ($type === "number") {
                		$dataobject->{$key} = 0;
                	} else if ($type === "array") {
                		$dataobject->{$key} = Array();
                	} else {
                		$dataobject->{$key} = "";
                	}
                    $changed = true;
                }
            }
            if ($changed && $savedata) {
                $dataobject->update();
            }
        }

        protected function ensureProperTypeForDataObject($dataobject) {
        	foreach($this->getModel() as $key => $info) {
                if (isset($dataobject->{$key})) {
                	$type = $this->getTypeForModelKey($key);
                	if ($type === "number") {
                		$dataobject->{$key} = intval($dataobject->{$key});
                	} else if ($type === "array") {
                		if (is_string($dataobject->{$key})) {
	                		$dataobject->{$key} = unserialize($dataobject->{$key});
	                		// TODO: if type is not array, create new empty array
                		}
                	} else {
                		// Ensure a string
                		$dataobject->{$key} = strval($dataobject->{$key});
                	}
                }
            }
        }

		public function action_startEdit()
        {
            $fieldid = $_POST["fieldid"];
            $field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();
            // check for 'callback'
            if (method_exists($this, "startEdit")) {
                $this->startEdit($field);
            }
			$dataobject = $this->fielddata($field);
            // If data does not exist, create it
            if (!$dataobject->loaded())
            {
                $dataobject->create();
            }
			// Check if every part of the model is present in the data, and if not: create it
			$this->ensureModelExists($dataobject);
            $html = $this->view("component_base_edit", FALSE)->set("field", $field)->set("model", $this->getModel())->set("data", $dataobject)->set("componentname", $this::$componentname)->render();
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
            // check for 'callback'
            if (method_exists($this, "edit")) {
                $this->edit($field);
            }
            $dataobject = $this->fielddata($field);
            // If data does not exist, create it
            if (!$dataobject->loaded())
            {
                $dataobject->create();
            }
            // Check if every part of the model is present in the data, and if not: create it
            $this->ensureModelExists($dataobject);
            // Update data field with data
            foreach($_POST as $index => $value) {
				$dataobject->{$index} = $value;
			}
            $dataobject->update();

            // Remove cache of all pages, since we do not know how this change affects other pages
            Wi3::inst()->cache->removeAll();

			// Let the Front-End rerender the affected field

			// We however do not want *just* the rendered field with its own content (return e.g. a <cms> element),
			// we want to have the field as it would appear on the page (e.g. with expanded <cms> elements into <div field...> elements)
			// including all mutations that any plugin or top-level element might do
			// Thus we first render the complete page, and then extract the field from there
			$pageid = $_POST["pageid"];
			// TODO: do not render if user does not have proper rights
			// Render page, and check if our field is within it
			$page = $field = Wi3::inst()->model->factory("site_page")->set("id", $pageid)->load();
			$renderedInAdminArea = true;
			$pageHtml = Wi3_Renderer::renderPage($page, $renderedInAdminArea);
			// Now get the proper part of the page
			if (strpos($pageHtml, $fieldid) > 0) {
				// The field probably exists. Search for it using phpQuery
				$document = phpQuery::newDocument($pageHtml); // Give PHPQuery a context to work with
        		$fieldHtml = pq("[type=field][fieldid=" . $fieldid . "]")->html();
			} else {
				$fieldHtml = "Field does not exist on the page.";
			}
            echo json_encode(
                Array(
                    "scriptsbefore" => Array(
                        "0" => "wi3.pagefillers.default.edittoolbar.renderFieldHtml('" . $fieldid . "', '" . base64_encode($fieldHtml) . "');"
                    )
                )
            );
        }

        protected function fielddata($field=null, $key=null, $value=null) {
            if ($field === null) {
                return false;
            } else {
                if (is_string($field)) {
                    $field = Wi3::inst()->model->factory("site_field")->set("id", $field)->load();
                }
            }
            $dataobject = Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->load();
            if ($key === null) {
                // Ensure that all elements from the model exist and are of the proper type
                $this->ensureModelExists($dataobject, false);
                $this->ensureProperTypeForDataObject($dataobject);
                return $dataobject;
            } else {
                if (is_object($key) && $value !== null) {
                    // set object as data
                    $key->setref($field)->setname("data")->update();
                } elseif (is_string($key)) {
                    if ($value === null) {
                        // Return data-field
                        return $dataobject->{$key};
                    } else {
                        // Set data-field
                        $dataobject->{$key} = $value;
                        // The dataobject will serialize any Arrays, if there are any in the model
                        $dataobject->update();
                    }
                }
            }
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
