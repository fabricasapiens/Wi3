<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Formbuilder Textarea class
 * @package Wi3
 * @author	Willem Mulder
 */
 
class Wi3_Formbuilder_Textarea extends Wi3_HTML_Textarea
{
	public function __construct($name) {
		parent::__construct($name);
		$this->attr("style", "width: 100%; height: 110px;");
	}
}
    
?>
