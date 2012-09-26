<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Pagefiller_Default_Component_Simpleblogarticle extends Pagefiller_Default_Component_Base
    {

        // Model
        public static $model = Array(
            "title" => Array("type" => "text"),
            "image" => Array("type" => "image"),
            "keywords" => Array("type" => "list", "model" => Array(
                "keyword" => Array("type" => "text")
            )),
            "text" => Array("type" => "text", "length" => "nolimit", "default" => "Dit is de blogtekst", "showoneditscreen" => false),
            "summary" => Array("type" => "text",  "length" => "nolimit", "default" => "Dit is de samenvatting"),
            "entertimestamp" => Array("type" => "text", "showoneditscreen" => false),
            "publicationtimestamp" => Array("type" => "text", "showoneditscreen" => false),
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
			$dataobject = $this->fielddata($field);
            $image = Wi3::inst()->model->factory("site_file")->values(Array("id"=>$dataobject->image))->load();
            $imageurl = Wi3::inst()->urlof->image($image,300);
			return $this->view("render")->set("data", $dataobject)->set("field", $field)->set("imageurl", $imageurl)->render();
        }
    }

?>
