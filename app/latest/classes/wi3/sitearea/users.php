<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * @package Wi3
 * @author	Willem Mulder
 */
 
 // This class provides a manager for users
 // Users are loaded via the site_* models, so that the database-name "site" is used. (Wi3 will have assigned the actual site-database-config to this "site"-db. See wi3.php)
class Wi3_Sitearea_Users extends Wi3_Base
{

    public $allfiles = NULL;
    
    // call like add($user, $settings) or ($userproperties, $settings)
    function add($user = array(), $settings = array())
    {
        if (is_object($user))
        {
            $new = $user; // User is provided through parameter
        }
        else
        {
            // No file object, but fileproperties are provided
            $new = Wi3::inst()->model->factory("site_file");
            $fields = $new->fields();
            foreach($file as $key => $val)
            {
                // Only add the value if it is present somewhere in the model
                if (isset($fields[$key]))
                {
                    $new->{$key} = $val;
                }
            }
        }
        // Insert the file at the appropriate place
        // Check that with an existing file 
        $existing = Wi3::inst()->model->factory("site_file");
        if (isset($settings["under"]))
        {
            $existing->id = $settings["under"];
        }
        else
        {
            $existing->lft = 1;
        }
        $existing->load();
        if ($existing->loaded())
        {
            if (isset($settings["under"]))
            {
                $new->insert_as_first_child($existing);
            }
            else
            {
                $new->insert_as_prev_sibling($existing);
            }
        }
        else
        {
            $new->insert_as_new_root();
        }
        // Return inserted file
        return $new;
    }
    
    public function moveBefore($file, $reffile) {
            //create Model objects if just IDs are given
            if (is_numeric($file)) {
                $file = Wi3::inst()->model->factory("site_file", array("id"=> $file))->load();
            }
            if (is_numeric($reffile)) {
                $reffile = Wi3::inst()->model->factory("site_file", array("id"=> $reffile))->load();
            }
            
            if ($reffile AND $file) {
                $file->move_to_prev_sibling($reffile);
                $file->reload();
                return true;
            }
        }
        
        public function moveAfter($file, $reffile) {
            //create Model objects if just IDs are given
            if (is_numeric($file)) {
                $file = Wi3::inst()->model->factory("site_file", array("id"=> $file))->load();
            }
            if (is_numeric($reffile)) {
                $reffile = Wi3::inst()->model->factory("site_file", array("id"=> $reffile))->load();
            }
            
            if ($reffile AND $file) {
                $file->move_to_next_sibling($reffile);
                $file->reload();
                return true;
            }
        }
        
        public function moveUnder($file, $reffile) {
            //create Model objects if just IDs are given
            if (is_numeric($file)) {
                $file = Wi3::inst()->model->factory("site_file", array("id"=> $file))->load();
            }
            if (is_numeric($reffile)) {
                $reffile = Wi3::inst()->model->factory("site_file", array("id"=> $reffile))->load();
            }
            
            if ($reffile AND $file) {
                $file->move_to_last_child($reffile);
                $file->reload();
                return true;
            }
        }
        
        public function delete($file) {
            //create Model objects if just IDs are given
            //new Profiler();
            if (is_numeric($file)) {
                $file = Wi3::inst()->model->factory("site_file", array("id"=> $file))->load();
            }
            // TODO: delete all possible resized versions of images!
            if (file_exists(Wi3::inst()->pathof->site . "data/uploads/" . $file->filename))
            {
                unlink(Wi3::inst()->pathof->site . "data/uploads/" . $file->filename);
            }
            // Deleting a node in a tree will delete its descendants as well
            $file->delete();
            //echo View::factory("profiler/stats");
            return true;
        }
        
        public function getall()
        {
            if ($this->allfiles == NULL)
            {
                $this->allfiles = Wi3::inst()->model->factory("site_file")->load(NULL, FALSE); // False for no limit on results
            }
            return $this->allfiles;
        }
        
        // Function to find certain files, based on specific requirements
        public function find($filteroptions)
        {
        
            $file = Wi3::inst()->model->factory("site_file");
        
            // Create query to find all the files, based on the filteroptions
            $query = DB::select();
            $counter = 0;
            foreach($filteroptions["extensions"] as $ext) 
            {
                $counter++;
                if ($counter == 1)
                {
                    $query = $query->where("filename", "LIKE", "%.".$ext);
                }
                else
                {
                    $query = $query->or_where("filename", "LIKE", "%.".$ext);
                }
            }            
            
			$query = $query->order_by($file->left_column, "ASC");
		
		    return $file->load($query, NULL);
        }
    
}
    
?>
