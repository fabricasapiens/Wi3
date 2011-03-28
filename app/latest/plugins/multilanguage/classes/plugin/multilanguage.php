<?php

    Class Plugin_Multilanguage extends Wi3_Baseplugin
    {
        
        public $languagefornewpage = "";
      
        public function __construct()
        {
            // Add this versionplugin to the Wi3::inst()->sitearea->pages->versionplugins list, so that is enquired when necessary
            // This plugins registers for the 
            Wi3::inst()->sitearea->pages->registerversionplugin("language", $this);
        }
        
        public function versionhtmlforaddpage()
        {
            // Keep the name unique, best use the classname in combination with the tagname
            return "<label>Taal:</label><select name='plugin_multilanguage_languagetag'><option value='nl'>Nederlands</option><option value='en'>English</option></select>";
        }
        
        public function processaddpage($page, $post = array())
        {
            if (isset($post["plugin_multilanguage_languagetag"]))
            {
                $this->languagefornewpage = $post["plugin_multilanguage_languagetag"];
                $page->versiontags->language = $post["plugin_multilanguage_languagetag"]; // TODO HIER VERDERGAAN MET SET() METHOD VOOR THE ARRAY MODEL!
            }
            // Important, return the altered $page and $post!
            return array($page, $post);
        }
        
        public function getversiontagforaddpage()
        {
            
        }
      
    }

?>