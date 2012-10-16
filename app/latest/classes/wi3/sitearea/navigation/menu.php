<?php defined('SYSPATH') or die('No direct script access.');

    class Wi3_Sitearea_Navigation_Menu extends Wi3_Sitearea_Navigation_Base
    {
        
        // Container class for the menu
        
        // Override
        public $tagname = "ul";
        
        private $activepage = NULL;
        private $itemTag; // Should always be a li
        private $activeItemTag; // Should always be a li
		private $pagePositions = NULL; // Should be a list of pagePositions

        private $pages;
        
        public function setactivepage($page)
        {
            $this->activepage = $page;
            return $this;
        }
        
        public function itemTag($tag) {
            $this->itemTag = $tag;
            return $this;
        }
        
        public function activeItemTag($tag) {
            $this->activeItemTag = $tag;
            return $this;
        }
		
		public function pagePositions($list = null) {
			if ($list != null) {
				$this->pagePositions = $list;
				return $this;
			}
			return $this->pagePositions;
		}

        public function loadPages() {
            if (!isset($this->pages)) {
                $pages = Array();
                if ($this->pagePositions() == null) {
                    $this->pagePositions(Wi3::inst()->sitearea->pagepositions->getall());
                }
                $pagepositions = $this->pagePositions();
                foreach($pagepositions as $pageposition)
                {
                    $pagepositionpages = $pageposition->pages;
                    $page = $pagepositionpages[0]; // Simply get first page
                    $pages[] = $page;
                }
                $this->pages = $pages;
            }
            return $this->pages;
        }
        
        function __construct() {
            parent::__construct();
            $newTag = new Wi3_Sitearea_Navigation_Base();
            $this->itemTag($newTag->tagName("li"));
            $newTag2 = new Wi3_Sitearea_Navigation_Base();
            $this->activeItemTag($newTag2->tagName("li")->attr("class", "active"));
        }
        
        public function renderContent()
        {
        
            // Ensure that itemTags are a li
            $this->itemTag->tag = "li";
            $this->activeItemTag->tag = "li";
        
            // Set the page, if not done already so 
            if ($this->activepage == NULL)
            {
                $this->setactivepage(Wi3::inst()->sitearea->page);            
            }
        
            // Get all pagepositions and render the menu
            ob_start();
            $this->loadPages(); // Loads pages under all the pagePositions
            $pagePositions = $this->pagePositions();
            $prevpageposition = NULL;
            $hiddenfromlevel = -1;
            foreach($pagePositions as $pageposition)
            {
                $page = $pageposition->pages[0];
                // Notice the level from where the menu is hidden
                if ($page->visible == FALSE) {
                    if ($hiddenfromlevel == -1) { // Only the lowest hidden level is important. The rest under it is hidden anyways 
                        $hiddenfromlevel = $pageposition->{$pageposition->level_column};
                    }
                }
                // If there is a previous pageposition, we can check if we went up or down in the tree
                if ($prevpageposition != NULL)
                {
                    if ($pageposition->{$pageposition->level_column} > $prevpageposition->{$prevpageposition->level_column})
                    {
                         // Going a level deeper
                         if($page->visible == TRUE) {
                            // Don't start menu level if a parent is hidden anyway
                            // Only start if all tree up is visible
                            if ($hiddenfromlevel == -1) {
                                echo "<ul>";
                            }
                        }
                    }
                    else if ($pageposition->{$pageposition->level_column} < $prevpageposition->{$prevpageposition->level_column})
                    {
                        // Going a level up, or maybe even more than 1 level 
                        // Find out how many levels we go up and close every level properly
                        for($i=($prevpageposition->{$prevpageposition->level_column} - $pageposition->{$prevpageposition->level_column}); $i > 0; $i--)
                        {
                            // Only close menu parts that were indeed rendered (i.e. the page was not hidden)
                            // We know  that all menu parts are hidden that have a *higher or equal* level than hiddenfromlevel
                            $currentlevel = $pageposition->{$pageposition->level_column}+$i;
                            if ($hiddenfromlevel == -1 || $currentlevel < $hiddenfromlevel) {
                                echo "</li></ul>";
                            }
                        }
                    } 
                    else 
                    {
                        echo "</li>";
                    }
                }
                $prevpageposition = $pageposition;
                // If page is visible but within an invisible parent, don't show
                if ($page->visible == TRUE && $hiddenfromlevel != -1 && $pageposition->{$pageposition->level_column} > $hiddenfromlevel) {
                    continue;
                }
                // If page is visible and on same or lower level than hiddenfromlevel, then the hiddenfromlevel should be reset (i.e. to -1)
                if ($page->visible == TRUE && $pageposition->{$pageposition->level_column} <= $hiddenfromlevel) {
                    $hiddenfromlevel = -1;
                }
                if ($page->visible == FALSE)
                {
                    continue;
                }
                
                // Determine URL, based on the redirect-type
                if ($page->redirecttype == "external")
                {
                    $url = $page->redirect_external;
                }
                else if ($page->redirecttype == "wi3")
                {
                    // Load correct page, and get the slug from it 
                    $redirectpage = Wi3::inst()->model->factory("site_page")->set("id", $page->redirect_wi3)->load();
                    $url = Wi3::inst()->urlof->page($redirectpage->slug);
                }
                else
                {
                    $url = Wi3::inst()->urlof->page($page->slug);
                }
                // If page is the same as the 'activepage', then add class='active'
                echo ($page->id == $this->activepage->id?$this->activeItemTag->renderOpenTag():$this->itemTag->renderOpenTag()) . "<span><a" . ($page->redirecttype == "external"?"target='_blank'":"") . " href='" . $url . "'>" . $page->longtitle . "</a></span>";
            }
            // Now, if we have ended far from root (i.e. a deep node), we need to add some </li></ul>
            if ($pageposition->{$prevpageposition->level_column} > 0)
            {
                for($i=$pageposition->{$prevpageposition->level_column}; $i > 0; $i--)
                {
                    echo "</li></ul>";
                }
            }
            echo "</li>";
            return ob_get_clean();
        }
        
    }

?>
