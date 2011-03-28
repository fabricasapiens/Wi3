<?php defined('SYSPATH') or die('No direct script access.');

    class Wi3_Sitearea extends Wi3_Base 
    {
        
        // Container class for containing the site, the site's database connection and more
        
        public function __construct()
        {
            // Sitenavigation can be constructed while this sitearea class is empty, since the navigation is only called in the templates, at which time the sitearea will already be filled
            $this->navigation = Wi3_Sitearea_Navigation::inst();
        }
        
        // This function sets a page to $this->page (i.e. Wi3::inst()->sitearea->page)
        // If a page is set as object, then just put that page. If it is a string, load it by string
        public function setpage($pageorpagename)
        {
            if (is_object($pageorpagename))
            {
                $this->page = $pageorpagename;
            }
            else if (is_string($pageorpagename))
            {
                $this->page = $this->getpage($pageorpagename);
            }
            else
            {
                $this->page = $this->getpage(NULL);
            }
            // Now notify other entities that use this->page that the page has been loaded
            Event::instance("wi3.init.sitearea.page.loaded")->execute();
            return $this->page;
        }
        
        // This function gets a page for this site. 
        // It will try to fetch the name from $pagename, and if that fails go on to load the landingpage, else the notfoundpage and else simply the first page of the site
        // TODO: we might want to first load the notfoundpage, then only the landingpage?
        public function getpage($pagename)
        {
        
            // Empty pagename should be turned into NULL, or otherwise the Sprig will still load a page successfully (the last added page, to be exact)
            if (empty($pagename))
            {
                $pagename = -1;
            }
        
            // Check whether we deal with a pagename that is meant to identify a numerical id (in the form of domain.com/[something]/_numericid)
            if (substr($pagename, 0, 1) == "_" AND is_numeric(substr($pagename, 1)))
            {
                $page = Wi3::inst()->model->factory("site_page")->set('id', substr($pagename, 1))->load();
            }
            else
            {
                $page = Wi3::inst()->model->factory("site_page")->set('slug', $pagename)->load();
            }
            
            // Check whether the load was succesfull
            if (!$page->loaded()) 
            {
                // Load landingpage. This is always an ID, not a slug
                $pageid = (!empty(Wi3::inst()->sitearea->site->landingpage) ? Wi3::inst()->sitearea->site->landingpage : Wi3::inst()->sitearea->site->notfoundpage);
                if (empty($pageid)) { $pageid = -1; }
                $page = Wi3::inst()->model->factory("site_page")->set('id', $pageid)->load();
                // Check if that worked, else load notfoundpage
                if (!$page->loaded()) 
                {
                    // Load notfoundpage
                    $pageid = Wi3::inst()->sitearea->site->notfoundpage;
                    if (empty($pageid))
                    {
                        // If empty, then load first page
                        $pageposition = Wi3::inst()->model->factory("site_pageposition")->load(NULL,1); // limit to 1
                        $page = Wi3::inst()->sitearea->page = $pageposition->pages[0];
                    } 
                    else
                    {
                        $page = Wi3::inst()->model->factory("site_page")->set('slug', $pageid)->load();
                        // If loading notfoundpage did not work, then load first page
                        if (!$page->loaded()) 
                        {
                            $pageposition = Wi3::inst()->model->factory("site_pageposition")->load(NULL,1); // limit to 1
                            $page = Wi3::inst()->sitearea->page = $pageposition->pages[0];
                        }
                    }
                }
            }
            return $page;
        }
        
    }

?>
