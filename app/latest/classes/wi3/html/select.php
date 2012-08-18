<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Select class
 * @author	Willem Mulder
 */
 
class Wi3_Html_Select extends Wi3_Html_FormElement
{
	
	public $tagname = "select";
	
	public function __construct($name = "") {
		parent::__construct($name);
		// Set attributes
		$this->attr("name", $name);
	}
	
	public function onBeforeRender() {
		// Set 'checked' of correct option child
		$val = $this->val();
		if (!empty($val)) {
			foreach($this->children as $child) {
				if ($child instanceof Html_Option) {
					if($child->attr("value") == $val) {
						$child->attr("selected", "selected");
					} else {
						$child->attr("selected", "");
					}
				}
			}
		}
	}
	
}
    
?>
