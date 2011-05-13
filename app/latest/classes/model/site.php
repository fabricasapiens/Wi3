<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Global Site Model
 * This model contains all settings for a site that should not be exported (i.e. that are locally-bound, like whether the site is active or not) and are thus kept outside the user-database
 * @package
 * @author	Willem Mulder
 */
class Model_Site extends Sprig
{
    public $_db = "global";
    
	protected $_title_key = 'name';

	protected $_sorting = array('name' => 'asc');

	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,
			'name' => new Sprig_Field_Char(array(
				'empty'  => FALSE,
				'unique' => TRUE,
			)),
			'databasesafename' => new Sprig_Field_Char(array(
			    'empty'  => TRUE,
                'in_db' => FALSE, // Not te be found in Database. Is inserted from the global site's name at runtime
            )),
            'title' => new Sprig_Field_Char(array(
				'empty'  => FALSE,
				'unique' => TRUE,
			)),
			'active' => new Sprig_Field_Boolean(
			),
            'urls' => new Sprig_Field_HasMany(array(
				'model' => 'Url',
				'editable' => FALSE,
			))
		);
	}
    
    public function delete(Database_Query_Builder_Delete $query = NULL) 
    {
        // If not loaded, then load
        if (!$this->loaded()) {
            $this->load();
        }
        // Delete all coupled urls
        foreach($this->urls as $url) {
            $url->delete();
        }
        // Go ahead with primary deletion
        parent::delete();
    }
}
    
?>
