<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Controller_Pagefiller_Default_Component_Repeatedlist extends Controller_Pagefiller_Default_Component_Base
    {
    
		public static $componentname = "repeatedlist";
		
        public function startEdit($field) 
        {
            // Possibly custom code here
            // This function is called from parent::action_startEdit (note the action_) so we don't have to call our parent function
        }
        
        public function edit($field) 
        {
    		// Custom code
            // This function is called from parent::action_edit (note the action_) so we don't have to call our parent function
            $this->fielddata($field, "edittimestamp", time());
        }
        
    }

?>
