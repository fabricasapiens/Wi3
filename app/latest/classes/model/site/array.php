<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Auth User
 * @package Wi3
 * @author	Willem Mulder
 */
class Model_Site_Array extends Sprig Implements Iterator
{
    public $_db = "site";
    public $_originalarray= NULL;
    public $_updatedarray = array();
    
    protected function _init()
	{
        // Overrule the names of the Roles and User_Token model to the Site_... version
		$this->_fields = array(
            '_id' => new Sprig_Field_Auto,
            
           '_refclass' => new Sprig_Field_Char(array(
				'empty'  => TRUE, // Default FALSE
			)),
            '_refid' => new Sprig_Field_Char(array(
				'empty'  => TRUE, // Default FALSE
			)),
            
            '_name' => new Sprig_Field_Char(array(
				'empty'  => TRUE, // Default FALSE
			)),
            
            '_arraydatas' => new Sprig_Field_HasMany(array(
                'model' => 'Site_Arraydata',
                'foreign_key' => 'site_array_id' // Tells which column in the "model" should have the same value as $this->pk(). 
            )),
            
		);
	}
    
    public function __SET($key, $val)
    {
        if (substr($key, 0,1) == "_")
        {
            // Internal value
            parent::__SET($key,$val);
        }
        else
        {
            $this->loadarraydata();
            $this->_updatedarray[$key] = $val;
        }
    }
    
    public function __GET($key)
    {   
        if (substr($key, 0,1) == "_")
        {
            // Internal value
            return parent::__GET($key);
        }
        else
        {
            $this->loadarraydata();
            return $this->_updatedarray[$key];
        }
    }
    
    public function load(Database_Query_Builder_Select $query = NULL, $limit = 1)
    {
        parent::load($query, $limit); // Load the array as a Sprig object 
        $this->loadarraydata(); // Load the associated arraydata
        return $this;
    }
    
    public function loadarraydata()
    {
        // We can only load arraydata if we are loaded ourselves
        if ($this->loaded())
        {
            if ($this->_originalarray == NULL)
            {
                $this->_originalarray = array();
                // Load the data
                foreach($this->__GET("_arraydatas") as $result)
                {
                    $this->_originalarray[$result->key] = $this->_updatedarray[$result->key] = $result->val;
                }
            }
        }
    }
    
    public function create()
    {
        // first, simply create the array as usual
        parent::create();
        // check if there are already values set, and save them
        if (!empty($this->_updatedarray))
        {
            $this->update();
        }
        return $this;
    }
    
    public function update()
    {
        $this->loadarraydata(); // Make sure that existing arraydata is loaded
        foreach($this->_updatedarray as $key => $val)
        {
            // Search for an existing arraydata with this key, if it exists
            if (isset($this->_originalarray[$key]))
            {
                $arraydata = Wi3::inst()->model->factory("site_arraydata")->set("array", $this)->set("key", $key)->load();
                $arraydata->val = $val;
                $arraydata->update();
            }
            // If the arraydata does not already exist, then create it
            else
            {
                // Create new arraydata and fill it
                $arraydata = Wi3::inst()->model->factory("site_arraydata");
                $arraydata->key = $key;
                $arraydata->val = $val;
                $arraydata->array = $this;
                $arraydata->create();
                // Add it to the _related list
                $this->add("_arraydatas", $arraydata);
                // Now, the array has this arraydata as 'original'
                $this->_originalarray[$key] = $val;
            }
        }
        return $this;
    }
    
    public function delete(Database_Query_Builder_Delete $query = NULL) 
    {
        
        if (!$query)
		{
			// remove all the arraydata that belongs to this object
            $this->loadarraydata(); // Make sure that existing arraydata is loaded
            foreach($this->__GET("_arraydatas") as $result)
            {
                $result->delete();
            }
            parent::delete();
		}
		else
		{
			parent::delete($query);
		}
        
    }
    
    public function setname($name)
    {
        $this->__SET("_name", $name);
        return $this;
    }
    
    public function setref($object)
    {
        $this->__SET("_refclass",get_class($object));
        $this->__SET("_refid",$object->{$object->pk()}); // It is assumed that any object has an its primary key available through $obj->pk()
        return $this;
    }
    
    
    // Iterator functions
    public function __UNSET($key) {
        //unset the key-value pair
        unset($this->_updatedarray[$key]);
    }
    
    public function __ISSET($key) {
        //return the isset of the key-value pair
       return isset($this->_updatedarray[$key]);
    }
    
    //Iterator functions
    public function rewind() {
        $this->loadarraydata(); // Ensure that array data is loaded, so that there is an array to loop over
        reset($this->_updatedarray);
    }

    public function current() {
        //$key = key($this->_updatedarray);
        $var = current($this->_updatedarray);
        return $var;
    }

    public function key() {
        $var = key($this->_updatedarray);
        return $var;
    }

    public function next() {
        $var = next($this->_updatedarray);
        return $var;
    }

    public function valid() {
        $var = $this->current() !== false;
        return $var;
    }
    
}
