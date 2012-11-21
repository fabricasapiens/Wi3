<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Auth User Model
 * @package Sprig Auth
 * @author	Paul Banks
 */
class Model_Site_Page extends Sprig
{
    public $_db = 'site'; // This database instance will be defined in the Wi3 setup, via Wi3_Database::instance("site");
    
	protected $_title_key = 'slug';

	protected $_sorting = array('slug' => 'asc');
    
    // To allow write access to undeclared properties, override the __SET() method
    // return the parent::_SET($key, $val) if the $key is a normal column-name (i.e. it is in the field-list)
    // Otherwise, just return $this->{$key} = $val. A local call $this->something will never be redirected to __SET() so this will not end in a loop
    public function __SET($key, $val)
    {
        $fields = (array) $this->fields();
        if (array_key_exists($key, $fields))
        {
            return parent::__SET($key, $val);
        }
        else
        {
            return $this->{$key} = $val;
        }
    }
    
    public function render($renderedinadminarea=false)
    {
        $pagefillername = "Pagefiller_" . $this->filler;
        $pagefiller = new $pagefillername;
        $content = $pagefiller->render($this,$renderedinadminarea);
        // Now remove all the <cms> blocks that were left over (i.e. not dealt with by the pagefiller)
        // TODO: do this with PHPQuery, if that does not cost too much performance-wise
        return preg_replace("@<cms[^>]*>[^<]*</cms>@","",$content);
    }

    // Funtion to retrieve editableBlockContent when editableBlocks have their reference set to a page
    public function loadEditableBlockContent($editableblock, $blockname) {
        $data = $data = Wi3::inst()->model->factory("site_data")->setref($this)->set("name",$blockname)->load();
        if ($data->loaded())
        {
            $content = $data->data;
        }
        else
        {  
            $content = pq($editableblock)->html(); // Get the default content
        }
        return $content;
    }

    public function saveEditableBlockContent($editableblock, $blockname, $content) {
        $data = Wi3::inst()->model->factory("site_data")->setref($this)->set("name",$blockname)->load();
        $data->data = $content;
        // Save the data
        $data->updateorcreate();
    }

	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,
            
            'pageposition' => new Sprig_Field_BelongsTo(array(
				'model' => 'Site_Pageposition',
                'column' => 'site_pageposition_id', // Column to be used in the database table for this model
                // 'foreign_key' => 'id' // Is assumed
			)),
            
			'longtitle' => new Sprig_Field_Char(array(
				'empty'  => TRUE, // Default FALSE
				'unique' => FALSE, // Default FALSE
			)),
            'shorttitle' => new Sprig_Field_Char(array(
				'empty'  => TRUE,
				'unique' => FALSE,
			)),
            'slug' => new Sprig_Field_Char(array(
				'empty'  => FALSE,
				'unique' => TRUE,
			)),
            'keywords' => new Sprig_Field_Char(array(
				'empty'  => TRUE,
				'unique' => FALSE,
			)),
            'description' => new Sprig_Field_Char(array(
				'empty'  => TRUE,
				'unique' => FALSE,
			)),
            "created" => new Sprig_Field_Timestamp(array(
				'empty'  => TRUE,
			)),
            "lastupdated" => new Sprig_Field_Timestamp(array( // When this field was updated (use at will)
				'empty'  => TRUE,
			)), 
            
			'active' => new Sprig_Field_Boolean(array('default' => TRUE)), // To temporarely disable this page
            'visible' => new Sprig_Field_Boolean(array('default' => FALSE)),  // If set to false, it will not show up in listings (navigation etc), however will still be accesible (unless active is set to false)
            'deleted' => new Sprig_Field_Boolean(array('default' => FALSE)), // Flag to make it deleted
            
            "owner" => new Sprig_Field_BelongsTo(array( // Owner of this page (default is the creator of the page)
				'empty'  => FALSE,
                'model' => 'User',
                'column' => 'owner_id', // Column in the site_page-model
                // 'foreign_key' => 'id' // Is assumed
			)), 
            "viewright" => new Sprig_Field_Char(array(  // What right does a user/group need to view
				'empty'  => TRUE,
				'unique' => FALSE,
			)),
            "editright" => new Sprig_Field_Char(array(  // What right does a user/group need to edit this page
				'empty'  => TRUE,
				'unique' => FALSE,
			)),
            "adminright" => new Sprig_Field_Char(array(   // what right does a user/group need to delete the page or edit one of the other rights. Default is 'admin' 
				'empty'  => TRUE,
				'unique' => FALSE,
                'default' => 'admin'
			)), 
            
            "filler" => new Sprig_Field_Char(array(
				'empty'  => FALSE,
				'unique' => FALSE,
			)), 
            
            // The following part is a little special
            // It covers the template of the page. Of course, it is totally up to the pagefiller whether it uses the template or not. 
            // It is even the responsibility of the pagefiller to set the template for a specific page.
            // However, since a template is such a basic part of a page, it is to be expected that templates are used in any page, regardless of the pagefiller
            // Even more, if there is a switch of pagefiller, the template should be kept the same, and different pagefillers should be able to use the same template
            // Thus, the template is stored in this very page Class, instead of being saved by every pagefiller individually
            // For interoperability, there is "templatespecification.pdf" in the docs folder, which describes the basic format for templates, which all pagefillers should comply with.
            "templatename" => new Sprig_Field_Char(array(
				'empty'  => TRUE,
				'unique' => FALSE,
			)), //what template should be used to render this page
            "templatetype" => new Sprig_Field_Char(array(
				'empty'  => TRUE,
				'unique' => FALSE,
			)), //Either 'wi3' or 'user', indicating where the page_template is stored
            
            "redirecttype" => new Sprig_Field_Char(array(
				'empty'  => TRUE,
				'unique' => FALSE,
			)), //Whether there is a redirect. Possible values include 'none', 'wi3', 'external'
            "redirect_wi3" => new Sprig_Field_Char(array(
				'empty'  => TRUE,
				'unique' => FALSE,
			)), //Pageid where page is redirected to. 
            "redirect_external" => new Sprig_Field_Char(array(
				'empty'  => TRUE,
				'unique' => FALSE,
			)), //External URL where page is to be redirected to
            
            // TODO: other metadata can be stored through the 'array' models (work like meadow)
		);
	}
    
}
    
?>
