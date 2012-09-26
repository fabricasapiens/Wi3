<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Pagefiller_Default_Component_SimpleblogRSSFeed extends Pagefiller_Default_Component_Base
    {

        // Model
        public static $model = Array(
            "amount" => Array("type" => "number"),
            "createtimestamp" => Array("type" => "text", "showoneditscreen" => false),
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
                $this->fielddata($field, "createtimestamp", time());
            }
            else if ($eventtype == "delete")
            {
                Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->delete();
            }
        }
    
        public function render($field)
        {
            $href = Wi3::inst()->urlof->wi3controllers . "pagefiller_default_component_simpleblogrssfeed/rssfeed";
			return $this->view("render")->set("href", $href)->render();
        }

    }

?>
