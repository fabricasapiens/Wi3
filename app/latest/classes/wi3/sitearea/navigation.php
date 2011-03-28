<?php defined('SYSPATH') or die('No direct script access.');

    class Wi3_Sitearea_Navigation extends Wi3_Base 
    {
        
        // Container class for the menu, breadcrumbs and sidenavigation
        
        public function __construct()
        {
            $this->menu = Wi3_Sitearea_Navigation_Menu::inst();
            $this->siblingsmenu = Wi3_Sitearea_Navigation_Siblingsmenu::inst();
            $this->breadcrumbs = Wi3_Sitearea_Navigation_Breadcrumbs::inst();
        }
        
    }

?>
