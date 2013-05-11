<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Controller_Pagefiller_Default_Component_Repeatedlist extends Controller_Pagefiller_Default_Component_Base
    {

		public static $componentname = "repeatedlist";

		// Actions
		// All actions by default require admin-rights
        public function action_increaseamount() {
        	$fieldid = $_POST["fieldid"];
        	$field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();

        	$dataobject = $this->fielddata($field);

        	// Add element
        	$elements = $dataobject->elements;
        	$elements[] = "anyvalue";

        	$this->fielddata($field, "elements", $elements);
        }
        public function action_decreaseamount() {
        	$fieldid = $_POST["fieldid"];
        	$field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();

        	$dataobject = $this->fielddata($field);

			// Amount of fields
			$amount = intval($dataobject->amount);
			if (!is_integer($amount) || empty($amount)) {
				$amount = 1;
			}

			// Decrease amount
			$amount--;

        	$this->fielddata($field, "amount", $amount);
        }

        // Non-action functions stemming from the use of the Base class
        public function startEdit($field)
        {
            // Possibly custom code here
            // This function is called from parent::action_startEdit (note the action_) so we don't have to call our parent function
        }

        public function edit($field)
        {
    		// Custom code
            // This function is called from parent::action_edit (note the action_) so we don't have to call our parent function
            $this->fielddata($field, "edittimestamp", time());
        }

    }

?>
