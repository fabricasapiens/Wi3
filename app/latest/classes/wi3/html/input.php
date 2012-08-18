<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Input class
 * @author	Willem Mulder
 */
 
class Wi3_Html_Input extends Wi3_Html_FormElement
{
	
	public $tagname = "input";
	
	public function __construct($name) {
		parent::__construct($name);
		$this->attr("type", "text");
	}
	
	public function onBeforeRender() {
		// Set 'value' attribute if val() is present
		$val = $this->val();
		if (!empty($val)) {
			$this->attr("value", $this->val());
		}
	}
	
}
    
?>
