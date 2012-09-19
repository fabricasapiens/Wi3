<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Controller_Pagefiller_Default_Component_Simpleblogarticle extends Controller_Pagefiller_Default_Component_Base
    {
    
		public static $componentname = "simpleblogarticle";
		public static $model = Array(
			"title" => Array("type" => "text"),
			"image" => Array("type" => "image"),
			"keywords" => Array("type" => "list", "model" => Array(
				"keyword" => Array("type" => "text")
			)),
			"text" => Array("type" => "text", "length" => "nolimit", "default" => "Dit is de blogtekst"),
			"summary" => Array("type" => "text", "default" => "Dit is de samenvatting")
		);
		
        public function action_startEdit() 
        {
            // Possibly custom code here
			parent::action_startEdit();
        }
        
        public function action_edit() 
        {
            // Possibly custom code here
			parent::action_edit();
        }
        
    }

?>
