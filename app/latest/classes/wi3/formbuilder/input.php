<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Formbuilder Input class
 * @package Wi3
 * @author	Willem Mulder
 */
 
class Wi3_Formbuilder_Input extends Wi3_HTML_Input
{
   public function __construct($name) {
		parent::__construct($name);
		$this->attr("style", "width: 100%;");
	}
}
    
?>
