<?php

    Class Pagefiller_Default_Component_Base extends Wi3_Base 
    {

        public static $model = Array(
            "title" => Array("type" => "text"),
            "image" => Array("type" => "image"),
            "keywords" => Array("type" => "list", "model" => Array(
                "keyword" => Array("type" => "text")
            )),
            "text" => Array("type" => "text", "length" => "nolimit", "default" => "Dit is de blogtekst", "showoneditscreen" => false),
            "summary" => Array("type" => "text", "default" => "Dit is de samenvatting"),
            "entertimestamp" => Array("type" => "text", "showoneditscreen" => false),
            "publicationtimestamp" => Array("type" => "text", "showoneditscreen" => false),
            "edittimestamp" => Array("type" => "text", "showoneditscreen" => false)
        );
    
        public static function view($viewname)
        {
            $componentname = self::get_calling_componentname();
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

        protected function getModel() {
            return $this::$model;
        }

        protected function ensureModelExists($dataobject,$savedata=true) {
            $changed = false;
            foreach($this->getModel() as $key => $info) {
                if (!isset($dataobject->{$key})) {
                    $dataobject->{$key} = "";
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
                return $dataobject;
            } else {
                if (is_object($key) && $value !== null) {
                    // set object as data
                    $key->setref($field)->setname("data")->update();
                } elseif (is_string($key)) {
                    if ($val === null) {
                        // Return data-field
                        return $dataobject->{$key};
                    } else {
                        // Set data-field
                        $dataobject->{$key} = $val;
                        $dataobject->update();
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
        
    }
    
?>
