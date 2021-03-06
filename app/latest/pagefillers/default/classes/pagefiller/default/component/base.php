<?php

    Class Pagefiller_Default_Component_Base extends Wi3_Base
    {

        public static function view($viewname)
        {
            $componentname = strtolower(self::get_calling_componentname());
            // Make this component base view extend the base template, with their locations set to the component folders
            $componenturl = Wi3::inst()->urlof->pagefillerfiles("default") . "components/" . $componentname . "/";
            $componentpath = Wi3::inst()->pathof->pagefiller("default") . "components/" . $componentname . "/";
            $componentbaseview = Wi3_Baseview::instance('pagefillercomponentbaseview'.$componentname, array(
                'javascript_url' => $componenturl.'static/javascript/',
                'javascript_path' => $componentpath.'static/javascript/',
                'css_url' => $componenturl.'static/css/',
                'css_path' => $componentpath.'static/css/',
                'view_path' => $componentpath.'views/',
            ));
            $componentview = View::factory()->set("this", $componentbaseview);
            $componentview->set_filepath($componentpath.'views/'.$viewname.EXT); // set_filepath sets a complete filename on the View
            return $componentview;
        }

        // This function determines where the data of the editable blocks within this field are retrieved from
        // By default, they are loaded from the data-object that is tied to this field
        // The model should have an item that has property "editableblockname"
        public function loadEditableBlockData($field, $blockName) {
            $model = $this->getModel();
            foreach($model as $index => $modelitem) {
                if (isset($modelitem["editableblockname"])) {
                    // Load data from there
                    $dataobject = Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->load();
                    return $dataobject->{$index};
                }
            }
            return false;
        }

        // This function determines where the data of the editable blocks within this component are stored
        // By default, they are stored on the data-object that is tied to this field
        // The model should have an item that has property "editableblockname"
        public function saveEditableBlockData($field, $blockName, $content) {
            $model = $this->getModel();
            foreach($model as $index => $modelitem) {
                if (isset($modelitem["editableblockname"])) {
                    // Load data from there
                    $dataobject = Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->load();
                    $dataobject->{$index} = $content;
                    $dataobject->updateorcreate();
                    return true;
                }
            }
            return false;
        }

        public function fieldevent($eventtype, $field) {
            if ($eventtype == "create") {
                // Set the inserttype to "insert" by default
                // TODO: think about this. It doesn't feel right.
                Controller_Pagefiller_Default_Edittoolbar_Ajax::$responseoptions["inserttype"] = "insertbefore";
                // Create the data that is associated with this field
                $data = Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->create();
                $this->ensureModelExists($data,true);
            }
            // Delete data that was tied to this field
            else if ($eventtype == "delete") {
                Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->delete();
            }
        }

        protected function getModel() {
            return $this::$model;
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
                // Ensure that all elements from the model exist
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
                        $dataobject->update();
                    }
                }
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

        public static function css($filename)
        {
            $componentname = self::get_calling_componentname();
            Wi3::inst()->css->add(Wi3::inst()->urlof->pagefillerfiles("default") . "components/" . $componentname . "/" . "static/css/" . $filename, "view");
        }


        public static function javascript($filename)
        {
            $componentname = self::get_calling_componentname();
            Wi3::inst()->javascript->add(Wi3::inst()->urlof->pagefillerfiles("default") . "components/" . $componentname . "/" . "static/javascript/" . $filename, "view");
        }

        private static function get_calling_componentname() {
            $callingClass = get_called_class(); // Uses late static binding
            $callingClassLastpart = substr($callingClass, strrpos($callingClass, "_")+1);
            return $callingClassLastpart;
        }

        public function fieldactions($field)
        {
            return "<a href='javascript:void(0)' onclick='wi3.request(\"pagefiller_default_component_" . self::get_calling_componentname() . "/startEdit\", {fieldid: " . $field->id . "})'>wijzigen</a>";
        }

    }

?>
