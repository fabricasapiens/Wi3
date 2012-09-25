<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Controller_Pagefiller_Default_Component_Simpleblogarticle extends Controller_Pagefiller_Default_Component_Base
    {
    
		public static $componentname = "simpleblogarticle";
		
        public function startEdit($field) 
        {
            // Possibly custom code here
        }
        
        public function edit($field) 
        {
    		// custom code
            $this->fielddata($field, "edittimestamp", time());
        }
        
    }

?>
