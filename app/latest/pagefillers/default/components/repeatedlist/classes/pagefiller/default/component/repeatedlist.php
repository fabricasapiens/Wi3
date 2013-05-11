<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Pagefiller_Default_Component_Repeatedlist extends Pagefiller_Default_Component_Base
    {

        // Model
        public static $model = Array(
            "amount" => Array("type" => "number"),
            "elements" => Array("type" => "array"),
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
                $this->fielddata($field, "amount", 1);
            }
        }

        public function render($field, $renderedinadminarea, $pqfield)
        {
			$dataobject = $this->fielddata($field);

			// Amount of fields
			$amount = intval($dataobject->amount);
			if (!is_integer($amount) || empty($amount)) {
				$amount = 1;
			}

        	// Get elements
        	$elements = $dataobject->elements;

			// Render
			$returnhtml = $this->view("render")->set("renderedinadminarea", $renderedinadminarea)->set("pqfield", $pqfield)->set("elements", $elements)->set("amount", $amount)->render();
			return $returnhtml;
        }
    }

?>
