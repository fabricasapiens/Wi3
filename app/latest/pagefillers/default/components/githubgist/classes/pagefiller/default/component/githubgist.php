<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Pagefiller_Default_Component_Githubgist extends Pagefiller_Default_Component_Base
    {

        // Model
        public static $model = Array(
            //"amount" => Array("type" => "number"),
            "username" => Array("type" => "text"),
            "id" => Array("type" => "text"),
            "entertimestamp" => Array("type" => "text", "showoneditscreen" => false),
            "edittimestamp" => Array("type" => "text", "showoneditscreen" => false)
        );
    

        // This function receives all sorts of events related to the field with this type
        public function fieldevent($eventtype, $field)
        {
            // Execute base actions (creating or deleting field data)
            parent::fieldevent($eventtype, $field);
            // Set entertimestamp
            if ($eventtype == "create")
            {
                // Set entertimestamp
                $this->fielddata($field, "entertimestamp", time());
            }
        }
    
        public function render($field)
        {
			$dataobject = $this->fielddata($field);
			return $this->view("render")->set("username", $dataobject->username)->set("id", $dataobject->id)->render();
        }
    }

?>
