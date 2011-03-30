<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Model interface for Wi3
 * @package Wi3
 * @author	Willem Mulder
 */
 
 // This class provides a manager for adding and deleting pagepositions
 // Pages are loaded via the site_* models, so that the database-name "site" is used. (Wi3 will have assigned the actual site-database-config to this "site"-db. See wi3.php)
class Wi3_Sitearea_Pagepositions extends Wi3_Base
{

    private $allpagepositions = NULL;
    
    function add($properties=array())
    {
        $existingpage = Wi3::inst()->model->factory("site_pageposition");
        if (isset($properties["under"]))
        {
            $existingpage->id = $properties["under"];
            $existingpage->load();
        }
        else
        {
            // Get the last root page. The new page will be inserted after it.
            $existingpage = $existingpage->lastroot(1); // Scope 1
        }
        if ($existingpage->loaded())
        {
            if (isset($properties["under"]))
            {
                $newpage = Wi3::inst()->model->factory("site_pageposition")->insert_as_first_child($existingpage);
            }
            else
            {
                $newpage = Wi3::inst()->model->factory("site_pageposition")->insert_as_next_sibling($existingpage);
            }
        }
        else
        {
            $newpage = Wi3::inst()->model->factory("site_pageposition")->insert_as_new_root();
        }
        return $newpage;
    }
    
    public function moveBefore($page, $refpage) {
            //create Model objects if just IDs are given
            if (is_numeric($page)) {
                $page = Wi3::inst()->model->factory("site_pageposition", array("id"=> $page))->load();
            }
            if (is_numeric($refpage)) {
                $refpage = Wi3::inst()->model->factory("site_pageposition", array("id"=> $refpage))->load();
            }
            
            if ($refpage AND $page) {
                $page->move_to_prev_sibling($refpage);
                $page->reload();
                return true;
            }
        }
        
        public function moveAfter($page, $refpage) {
            //create Model objects if just IDs are given
            if (is_numeric($page)) {
                $page = Wi3::inst()->model->factory("site_pageposition", array("id"=> $page))->load();
            }
            if (is_numeric($refpage)) {
                $refpage = Wi3::inst()->model->factory("site_pageposition", array("id"=> $refpage))->load();
            }
            
            if ($refpage AND $page) {
                $page->move_to_next_sibling($refpage);
                $page->reload();
                return true;
            }
        }
        
        public function moveUnder($page, $refpage) {
            //create Model objects if just IDs are given
            if (is_numeric($page)) {
                $page = Wi3::inst()->model->factory("site_pageposition", array("id"=> $page))->load();
            }
            if (is_numeric($refpage)) {
                $refpage = Wi3::inst()->model->factory("site_pageposition", array("id"=> $refpage))->load();
            }
            
            if ($refpage AND $page) {
                $page->move_to_last_child($refpage);
                $page->reload();
                return true;
            }
        }
        
        public function delete($pageposition) {
            //create Model objects if just IDs are given
            //new Profiler();
            if (is_numeric($pageposition)) {
                $pageposition = Wi3::inst()->model->factory("site_pageposition", array("id"=> $pageposition))->load();
            }
            // Now delete both the pages that are connected to the selected pageposition (and its children), as well as the pagepositions itself
            foreach($pageposition->descendants(TRUE) as $descendant) // TRUE to include $pageposition in the list as well
            {
                foreach($descendant->pages as $page)
                {
                    $page->id = ($page->id * 1);
                    $page = Wi3::inst()->model->factory("site_page", array("id"=> $page->id))->load(); // TODO: This should definitely not be necessary!!!
                    $page->delete();
                }
            }
            // Deleting a node in a tree will delete its descendants as well
            $pageposition->delete();
            //echo View::factory("profiler/stats");
            return true;
        }
        
        public function getall()
        {
            if ($this->allpagepositions == NULL)
            {
                $falseroot = Wi3::inst()->model->factory("site_pageposition");
                $falseroot->set($falseroot->left_column, 0)->set($falseroot->right_column, 99999999999999999)->set($falseroot->scope_column, 1);
                $this->allpagepositions = $falseroot->descendants();
            }
            return $this->allpagepositions;
        }
    
}
    
?>
