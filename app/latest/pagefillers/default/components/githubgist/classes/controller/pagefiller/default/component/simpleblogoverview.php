<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Controller_Pagefiller_Default_Component_Githubgist extends Controller_Pagefiller_Default_Component_Base
    {
    
		public static $componentname = "githubgist";
		
        public function startEdit($field) 
        {
            // Possibly custom code here
            return parent::startEdit($field);
        }
        
        public function edit($field) 
        {
    		// custom code
            $this->fielddata($field, "edittimestamp", time());
            return parent::edit($field);
        }
        
    }

?>
