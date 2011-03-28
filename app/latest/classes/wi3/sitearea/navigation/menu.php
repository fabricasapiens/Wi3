<?php defined('SYSPATH') or die('No direct script access.');

    class Wi3_Sitearea_Navigation_Menu extends Wi3_Base 
    {
        
        // Container class for the menu
        
        private $activepage = NULL;
        
        public function setactivepage($page)
        {
            $this->activepage = $page;
        }   
        
        public function render()
        {
        
            // Set the page, if not done already so 
            if ($this->activepage == NULL)
            {
                $this->setactivepage(Wi3::inst()->sitearea->page);            
            }
        
            // Get all pagepositions and render the menu
            ob_start();
            echo "<ul>";
            $pagepositions = Wi3::inst()->sitearea->pagepositions->getall();
            $prevpageposition = NULL;
            foreach($pagepositions as $pageposition)
            {

                // If there is a previous pageposition, we can check if we went up or down in the tree
                if ($prevpageposition != NULL)
                {
                    if ($pageposition->{$prevpageposition->level_column} > $prevpageposition->{$prevpageposition->level_column})
                    {
                        // Going a level deeper
                        echo "<ul>";
                    }
                    else if ($pageposition->{$prevpageposition->level_column} < $prevpageposition->{$prevpageposition->level_column})
                    {
                        // Going a level up, or maybe even more than 1 level 
                        // Find out how many levels we go up and close every level properly
                        for($i=($prevpageposition->{$prevpageposition->level_column} - $pageposition->{$prevpageposition->level_column}); $i > 0; $i--)
                        {
                            echo "</li></ul></li>";
                        }
                    } 
                    else 
                    {
                        echo "</li>";
                    }
                }
                $prevpageposition = $pageposition;
                $pages = $pageposition->pages;
                $page = $pages[0]; // Simply get first page
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
                echo "<li><span><a " . ($page->id == $this->activepage->id?"class='active'":"") . " " . ($page->redirecttype == "external"?"target='_blank'":"") . " href='" . $url . "'>" . $page->longtitle . "</a></span>";
            }
            // Now, if we have ended far from root (i.e. a deep node), we need to add some </li></ul>
            if ($pageposition->{$prevpageposition->level_column} > 0)
            {
                for($i=$pageposition->{$prevpageposition->level_column}; $i > 0; $i--)
                {
                    echo "</li></ul>";
                }
            }
            echo "</li></ul>";
            return ob_get_clean();
        }
        
    }

?>
