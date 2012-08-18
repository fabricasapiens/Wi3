<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * FormElement class
 * These classes have a state in which they save their raw input values
 * @author	Willem Mulder
 */
 
abstract class Wi3_Html_FormElement extends Wi3_Html_Base
{

	public $label;
	public $val;
	public $error;
	public $required = false;
	
	public function __construct($name = "", $label="") {
		parent::__construct();
		// Set attributes
		$this->attr("name", $name);
		$this->label($label);
		// Load the value from the Session if possible
		$pageinstance = PageInstanceId::inst();
		$vars = $pageinstance->getVarsForPageInstance();
		if (isset($vars[$name])) {
			$this->val($vars[$name]);
		}
	}
	
	public function val($val=null) {
		if ($val === null) {
			return $this->val;
		} else {
			$this->val = $val;
			return $this;
		}
	}
		
	public function label($label) {
		$this->label = $label;
	}
	
	// Override
	public function onBeforeRender() {
		if (!empty($error)) {
			$this->addClass("error");
		}
	}

	// Override
	public function renderBeforeTag() {
		$labelContent = "<div class='control-group " . (!empty($this->error) ? "error" : "") . "'>"; // opening control group
		if ($this->attr("type") != "hidden" && !empty($this->label)) {
			$labelContent .= "<label for='" . $this->attr("id") . "'>" . $this->label . "</label>";
		}
		return $labelContent;
	}
	
	// Override
	public function renderAfterTag() {
		$afterContent = "";
		if ($this->required()) {
			$afterContent .= "<span class='help-inline'>*</span>";
		}
		if (!empty($this->error)) {
			$afterContent .= "<span class='help-inline'>" . $this->error . "</span>";
		}
		$afterContent .= "</div>"; // closing control group
		return $afterContent;
	}
	
	public function error($error) {
		$this->error = $error;
	}
	
	public function required($bool = null) {
		if($bool === null) {
			return $this->required;
		} else {
			$this->required = $bool;
		}
	}
	
}
    
?>
