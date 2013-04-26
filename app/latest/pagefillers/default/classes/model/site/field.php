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

        $component = $this->getComponent();

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
            $component = $this->getComponent();
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

    public function render($renderedinadminarea, $pqfield)
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

            $component = $this->getComponent();

            return $component->render($this, $renderedinadminarea, $pqfield);

        }
        else
        {
            return "this field has no type.";
        }
    }

    public function loadEditableBlockContent($editableblock, $blockname) {
        // Check if component wants to determine where the data comes from
        $component = $this->getComponent();
        if (method_exists($component, "loadEditableBlockData")) {
            $blockContent = $component->loadEditableBlockData($this,$blockname);
            if ($blockContent !== false) {
                return $blockContent;
            }
        }
        // Load data the standard way or use a fallback otherwise
        $data = Wi3::inst()->model->factory("site_data")->setref($this)->set("name",$blockname)->load();
        if ($data->loaded()) {
            return $data->data;
        } else {
            return pq($editableblock)->html(); // Get the default content
        }
    }

    public function saveEditableBlockContent($editableblock, $blockname, $content) {
        // Check if component wants to determine where the data should go
        $component = $this->getComponent();
        if (method_exists($component, "saveEditableBlockData")) {
            $blockContent = $component->saveEditableBlockData($this,$blockname,$content);
            if ($blockContent !== false) {
                return true;
            }
        }
        // Store data the standard way
        $data = Wi3::inst()->model->factory("site_data")->setref($this)->set("name",$blockname)->load();
        $data->data = $content;
        // Save the data
        $data->updateorcreate();
    }

    public function getComponent() {
        // Create component
        $componentname = "Pagefiller_default_component_" . $this->type;
        return $componentname::inst();
    }

    /*
    * Retrieves the parent page by bubbling up in the ref structure until a page is found
    * @returns page
    */
    public function getParentPage() {
        $ref = $this;
        while(!$ref instanceof Model_Site_Page) {
            if(!isset($ref->_refclass) || empty($ref->_refclass) || !isset($ref->_refid) || empty($ref->_refid)) {
                return false;
            }
            if (strpos($ref->_refclass, "Model_") === 0) {
                $refclass = substr($ref->_refclass, 6);
            } else {
                $refclass = $ref->_refclass;
            }
            $ref = Wi3::inst()->model->factory($refclass)->values(Array("id"=>$ref->_refid))->load();
        }
        return $ref;
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
