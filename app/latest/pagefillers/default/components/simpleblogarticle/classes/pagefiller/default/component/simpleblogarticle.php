<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Pagefiller_Default_Component_Simpleblogarticle extends Pagefiller_Default_Component_Base
    {
    
        // This function receives all sorts of events related to the field with this type
        public function fieldevent($eventtype, $field)
        {
            if ($eventtype == "create")
            {
                // Set the inserttype
				// TODO: think about this. It doesn't feel right.
                Controller_Pagefiller_Default_Edittoolbar_Ajax::$responseoptions["inserttype"] = "replace";
                // Create the data that is associated with this field
                $imagedata = Wi3::inst()->model->factory("site_data")->setref($field)->setname("image")->create();
            }
            else if ($eventtype == "delete")
            {
                Wi3::inst()->model->factory("site_data")->setref($field)->setname("image")->delete();
            }
        }
    
        public function render($field)
        {
			$dataobject = $this->fielddata($field);
            $image = Wi3::inst()->model->factory("site_file")->values(Array("id"=>$dataobject->image))->load();
            $imageurl = Wi3::inst()->urlof->image($image,300);
			return $this->view("render")->set("data", $dataobject)->set("field", $field)->set("imageurl", $imageurl)->render();
        }
        
        public function fieldactions($field)
        {
            return "<a href='javascript:void(0)' onclick='wi3.request(\"pagefiller_default_component_simpleblogarticle/startEdit\", {fieldid: " . $field->id . "})'>wijzigen</a>";
        }
    }

?>
