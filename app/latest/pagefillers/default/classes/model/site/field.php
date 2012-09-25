<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Auth User
 * @package Wi3
 * @author	Willem Mulder
 */
class Model_Site_Field extends Sprig
{
    public $_db = "site";
    
    public $options = Array();
    
    protected function _init()
    {
        // Overrule the names of the Roles and User_Token model to the Site_... version
        $this->_fields = array(
            'id' => new Sprig_Field_Auto,

            '_refclass' => new Sprig_Field_Char(array(
                'empty'  => TRUE, // Default FALSE
            )),
            '_refid' => new Sprig_Field_Char(array(
                'empty'  => TRUE, // Default FALSE
            )),

            'name' => new Sprig_Field_Char(array(
                'empty'  => TRUE, // Default FALSE
            )),

            'type' => new Sprig_Field_Char(array(
                'empty'  => TRUE, // Default FALSE
            ))
        );
    }

    public function create()
    {
        $return = parent::create(); // Pass the create function to the parent

        //-------------------
        // Add the related Component to the Autoloader-paths, so it can be found by Kohana
        //-------------------   

        // Get component path
        $componentpath = Wi3::inst()->pathof->pagefiller("default") . "components/";
        // Loop over component-modules and add the one for this specific field
        $dir = new DirectoryIterator( $componentpath );
        foreach($dir as $file) 
        {
            if ($file->isDir() AND !$file->isDot() AND $file->getFilename() == $this->type ) 
            {
                Kohana::modules(Kohana::modules() + Array($file->getPathname()));
                continue;
            }
        }

        // Create component 
        $componentname = "Pagefiller_default_component_" . $this->type;
        $component = $componentname::inst();

        // Send the component of this field an event notice
        $component->fieldevent("create", $this);

        return $return;
    }

    public function delete(Database_Query_Builder_Delete $query = NULL)
    {

        //-------------------
        // Add the related Component to the Autoloader-paths, so it can be found by Kohana
        //-------------------   

        // Get component path
        $componentpath = Wi3::inst()->pathof->pagefiller("default") . "components/";
        // Loop over component-modules and add the one for this specific field
        $dir = new DirectoryIterator( $componentpath );
        foreach($dir as $file) 
        {
            if ($file->isDir() AND !$file->isDot() AND $file->getFilename() == $this->type ) 
            {
                Kohana::modules(Kohana::modules() + Array($file->getPathname()));
                continue;
            }
        }

        // For robustness, do not assume a field-type
        if (!empty($this->type)) {
            // Create component 
            $componentname = "Pagefiller_default_component_" . $this->type;
            $component = $componentname::inst();

            // Send the component of this field an event notice
            $component->fieldevent("delete", $this);
        }

        // Finally delete the field
        return parent::delete($query); // Pass the create function to the parent
    }   

    public function setname($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function setref($object)
    {
        $this->_refclass = get_class($object);
        $this->_refid = $object->{$object->pk()}; // It is assumed that any object has a primary key available through $obj->pk()
        return $this;
    }
    
    public function render($editmode = null)
    {
        if (!empty($this->type))
        {
            //-------------------
            // Add the related Component to the Autoloader-paths, so it can be found by Kohana
            //-------------------  
               
            // Get component path
            $componentpath = Wi3::inst()->pathof->pagefiller("default") . "components/";
            // Loop over component-modules and add the one for this specific field
            $dir = new DirectoryIterator( $componentpath );
            foreach($dir as $file) 
            {
                if ($file->isDir() AND !$file->isDot() AND $file->getFilename() == $this->type ) 
                {
                    Kohana::modules(Kohana::modules() + Array($file->getPathname()));
                    continue;
                }
            }
            
            // Create component 
            $componentname = "Pagefiller_default_component_" . $this->type;
            $component = $componentname::inst();
            
            $fieldhtml = $component->render($this);
            
            ///-------------------
            // Process field html for <cms> tags
            //-------------------   
            $html = phpQuery::newDocument($fieldhtml); // Give PHPQuery a context to work with
            $editableblocks = pq("cms[type=editableblock]");
            foreach($editableblocks as $editableblock)
            {
                $name = pq($editableblock)->attr("name");
                $id = pq($editableblock)->attr("id");
                $refname = pq($editableblock)->attr("reference");
                if (empty($refname)) { $refname = "field"; }
                if ($refname == "field")
                {
                    $ref = $this;
                }
                else if ($refname == "page")
                {
                    $ref = Wi3::inst()->sitearea->page;
                }
                // Try to load up to date content for this block, otherwise show the default content 
                $data = $data = Wi3::inst()->model->factory("site_data")->setref($ref)->set("name",$name)->load();
                if ($data->loaded())
                {
                    $content = $data->data;
                }
                else
                {  
                    $content = pq($editableblock)->html(); // Get the default content
                }
                // Ensure that inner CMS blocks have the same display (i.e. block or inline) as its parent
                $style = "style='display: inherit'";
                // Replace the <cms type='editableblock'> blocks into DOM tags
                if ($editmode === null) {
                    // Determine from controller. Note: this implies that component-controllers will *not* automatically turn into edit-mode
                    $editmode = Wi3::inst()->routing->controller == "adminarea";
                }
                if ($editmode)
                {
                    // edit-mode
                    $blockcontent = "<div " . $style ." type='editableblock' ref='" . $refname . "' name='" . $name . "' contenteditable='true'>" . $content . "</div>";
                }
                else
                {
                    // display-mode
                    if (!empty($id))
                    {
                        $blockcontent = "<div " . $style . " id='" . $id . "' type='contentblock' ref='" . $refname . "' name='" . $name . "'>" . $content . "</div>";
                    }
                    else
                    {
                        $blockcontent = "<div " . $style . " type='contentblock' ref='" . $refname . "' name='" . $name . "'>" . $content . "</div>";
                    }
                }
                pq($editableblock)->replaceWith($blockcontent);
            }
            
            return $html;
        
        }
        else
        {
            return "this field has no type.";
        }
    }
    
    // fieldactions
    public function fieldactions()
    {
        //-------------------
        // Add the related Component to the Autoloader-paths, so it can be found by Kohana
        //-------------------   
           
        // Get component path
        $componentpath = Wi3::inst()->pathof->pagefiller("default") . "components/";
        // Loop over component-modules and add the one for this specific field
        $dir = new DirectoryIterator( $componentpath );
        foreach($dir as $file) 
        {
            if ($file->isDir() AND !$file->isDot() AND $file->getFilename() == $this->type ) 
            {
                Kohana::modules(Kohana::modules() + Array($file->getPathname()));
                continue;
            }
        }
        
        // Create component 
        $componentname = "Pagefiller_default_component_" . $this->type;
        $component = $componentname::inst();
        
        return $component->fieldactions($this);
    }
    
    // Returns whether a given user is allowed to 
    public function isuserallowedto($user, $action) {
        //echo Kohana::debug($this);
        return true;// TODO: security implementation
    }
    
}
