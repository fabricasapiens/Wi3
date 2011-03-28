<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Model interface for Wi3
 * @package Wi3
 * @author	Willem Mulder
 */
 
 // This class provides a manager for adding, deleting and moving pages
 // Pages are loaded via the site_* models, so that the database-name "site" is used. (Wi3 will have assigned the actual site-database-config to this "site"-db. See wi3.php)
 // Note: This class will NOT search for landingspages, errorpages etc. Things like $site->landingspage should be handled on a higher level
class Wi3_Sitearea_Pages extends Wi3_Base
{

    private $allpages = NULL;
    
    public $_versionplugins = array();
    
    public function registerversionplugin($tag, $versionplugin)
    {
        $this->_versionplugins[$tag] = &$versionplugin;
    }
    
    public function versionplugins()
    {
        return $this->_versionplugins;
    }
    
    function add($properties=array())
    {
        $newpage = Wi3::inst()->model->factory("site_page");
        $fields = $newpage->fields();
        foreach($properties as $key => $val)
        {
            // Only add the value if it is present somewhere in the model
            if (isset($fields[$key]))
            {
                $newpage->{$key} = $val;
            }
        }
        $newpage->create();
        $this->allpages = NULL; // remove 'cache' of all the pages
        return $newpage;
    }
    
    function getall()
    {
        if ($this->allpages == NULL)
        {
            // The , FALSE parameter sets no limit to the amount of records loaded
            $this->allpages = Wi3::inst()->model->factory("site_page")->load(NULL, FALSE);
        }
        return $this->allpages;
    }
    
}
    
?>
