<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Controller_Pagefiller_Default_Component_Base extends Controller_ACL
    {
    
        public $template;
        protected $componentname = "";
    
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
