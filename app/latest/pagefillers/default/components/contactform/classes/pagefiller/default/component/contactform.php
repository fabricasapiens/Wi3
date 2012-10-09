<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Pagefiller_Default_Component_Contactform extends Pagefiller_Default_Component_Base
    {

        // Model
        public static $model = Array(
            "emailaddress" => Array("type" => "text", "showoneditscreen" => true),
            "entertimestamp" => Array("type" => "text", "showoneditscreen" => false),
            "edittimestamp" => Array("type" => "text", "showoneditscreen" => false)
        );

        // This function receives all sorts of events related to the field with this type
        public function fieldevent($eventtype, $field)
        {
            if ($eventtype == "create")
            {
                // Set the inserttype
				// TODO: think about this. It doesn't feel right.
                Controller_Pagefiller_Default_Edittoolbar_Ajax::$responseoptions["inserttype"] = "replace";
                // Create the data that is associated with this field
                $data = Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->create();
                $this->fielddata($field, "entertimestamp", time());
            }
            else if ($eventtype == "delete")
            {
                Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->delete();
            }
        }
    
        public function render($field)
        {
			return $this->view("render")->set("field", $field)->render();
        }
    }

?>
