<?php defined('SYSPATH') or die('No direct script access.');

    class Wi3_Sitearea_Navigation_Siblingsmenu extends Wi3_Sitearea_Navigation_Menu
    {
        
		public function renderContent() {
			// Set pagepositions
			if (is_object(Wi3::inst()->sitearea->page->pageposition)) {
				$pageposition = Wi3::inst()->sitearea->page->pageposition->load();
			} else {
				$pageposition = Wi3::inst()->model->factory("site_pageposition")->set("id", Wi3::inst()->sitearea->page->pageposition)->load();
			}
			if ($pageposition->{$pageposition->level_column} == 0) {
				// Lowest level, there is no parent, and all level 0 are siblings
				$pagepositions = Wi3::inst()->model->factory("site_pageposition")->set($pageposition->level_column, "0")->load(NULL,FALSE); // False for no limit
			} else {
				$pagepositions = $pageposition->parent()->children();
			}
			$this->pagePositions($pagepositions);
			return parent::renderContent();
		}
        
    }

?>
