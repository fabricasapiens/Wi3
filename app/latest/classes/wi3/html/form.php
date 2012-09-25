<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Form class
 * @author	Willem Mulder
 */
 
class Wi3_Html_Form extends Wi3_Html_Base
{
	
	public $tagname = "form";
	public $error;
	public $success;
	public $isSubmit;
	
	public function __construct() {
		parent::__construct();
		// Set attributes
		$this->attributes->method = "POST";
		$this->attributes->action = "";
		// Ensure that submitted values will be saved to the session under the current pageInstanceId
		$this->addSavePid();
		// Check if form was submitted
		// TODO: make it work with multiple forms on the page
		$this->isSubmit = !empty($_POST);
	}
	
	public function onBeforeRender() {
		// Check if form was submitted
		// TODO: make it work with multiple forms on the page
		if (!empty($_POST)) {
			// TODO: Let every child element process their input
			// Now trigger a submit
			$this->trigger("submit");
		}
		parent::onBeforeRender();
	}

	// Override renderContent function
	public function renderContent() {
		$innerContent = parent::renderContent();
		$message = "";
		if (!empty($this->error)) {
			$message = '<div class="alert alert-error">
							' . $this->error . '
						</div>';
		} else if (!empty($this->success)) {
			$message = '<div class="alert alert-success">
							' . $this->success . '
						</div>';
		}
		return $message . $innerContent;
	}
	
	public function values() {
		$values = Array();
		foreach($this->children as $child) {
			if ($child instanceof Html_FormElement) {
				if ($child->attr("name") != "" && $child->attr("name") != "savepid" && $child->attr("name") != "pid") {
					$values[$child->attr("name")] = $child->val();
				}
			}
		}
		return $values;
	}
	
	// Add a PageId into which the sent values should be saved
	public function addSavePid($pageinstanceid=null) {
		// Add a hidden field with the pageInstanceId
		$pid = new Html_Input("savepid");
		$pid->attr("type", "hidden");
		if ($pageinstanceid == null) {
			$pageinstance = PageInstanceId::inst();
			$pageinstanceid = $pageinstance->pageinstanceid;
		}
		$pid->val($pageinstanceid);
		$this->add($pid);
	}
	
	// Add a pageId from which the next page will load its form-values
	// If we enter the *current* page, the *next* page will show the values that the user will enter on this page
	public function addLoadPid($id) {
		// Add a hidden field with the pageInstanceId
		$pid = new Html_Input("pid");
		$pid->attr("type", "hidden");
		$pid->val($id);
		$this->add($pid);
	}
	
	public function error($error) {
		$this->error = $error;
	}
	
	public function success($message) {
		$this->success = $message;
	}
	
}
    
?>
