<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Input class
 * @author	Willem Mulder
 */
 
class Wi3_Html_Option extends Wi3_Html_Base
{
	
	public $tagname = "option";
	
	public function __construct($id = "", $content = "") {
		parent::__construct();
		// Set attributes
		$this->attr("value", $id);
		$this->content($content);
	}
	
}
    
?>
