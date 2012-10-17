<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Controller_Pagefiller_Default_Component_Simpleblogsearch extends Controller_Pagefiller_Default_Component_Base
    {
    
		public static $componentname = "simpleblogoverview";
		
        public function startEdit($field) 
        {
            // Possibly custom code here
        }
        
        public function edit($field) 
        {
    		// custom code
            $this->fielddata($field, "edittimestamp", time());
            return parent::edit($field);
        }
        
    }

?>
