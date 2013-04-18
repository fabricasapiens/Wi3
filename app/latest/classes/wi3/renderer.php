<?php defined('SYSPATH') or die('No direct script access.');

    // Class that deals with rendering of pages
	// Is used by sitearea, adminarea and other specific components
    class Wi3_Renderer
    {
        static public function renderPage($pageOrPagename, $renderedInAdminArea) {
        	// Create a page from pagename. Wi3 will automatically distinguish between id-urls (/_number) and slug-urls (/string) and fetch the correct page
	        Wi3::inst()->sitearea->setpage($pageOrPagename);
	        // Render page
	        // TODO: we should have one siteare per Request::Instance() (i.e. true HMVC) since the current structure is quite inflexible
	        // and dependency injecting everything into everywhere is also quite unfeasable
	        // We should set sitearea etc on the current Request and be able to fire new requests with different URLs etc easily
	        return Wi3::inst()->sitearea->page->render($renderedInAdminArea);
        }
    }

?>
