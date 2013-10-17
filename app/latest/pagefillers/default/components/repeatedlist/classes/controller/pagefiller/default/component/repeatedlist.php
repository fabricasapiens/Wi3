<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Controller_Pagefiller_Default_Component_Repeatedlist extends Controller_Pagefiller_Default_Component_Base
    {

		public static $componentname = "repeatedlist";

		// Actions
		// All actions by default require admin-rights
        public function action_addattop() {
        	$fieldid = $_POST["fieldid"];
        	$field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();

        	$dataobject = $this->fielddata($field);

        	// Add element
        	$elements = $dataobject->elements;
        	$newid = Wi3::inst()->date_now();
        	array_unshift($elements, $newid);

        	$this->fielddata($field, "elements", $elements);
        }

        public function action_addafter() {
        	$fieldid = $_POST["fieldid"];
        	$field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();

        	$dataobject = $this->fielddata($field);

        	// Find reference index
        	$elements = $dataobject->elements;
        	$index = $_POST["index"];
        	$arrayIndex = array_search($index, $elements);

        	// Add element after that index
        	$newid = Wi3::inst()->date_now();
			array_splice($elements, $arrayIndex+1, 0, $newid);

        	$this->fielddata($field, "elements", $elements);
        }

        public function action_remove() {
        	$fieldid = $_POST["fieldid"];
        	$field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();

        	$dataobject = $this->fielddata($field);

        	// Find reference index
        	$elements = $dataobject->elements;
        	$index = $_POST["index"];
        	$arrayIndex = array_search($index, $elements);

        	// Remove element at that index
			unset($elements[$arrayIndex]);

        	$this->fielddata($field, "elements", $elements);
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
