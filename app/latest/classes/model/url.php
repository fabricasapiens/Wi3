<?php defined('SYSPATH') or die ('No direct script access.');

class Model_Url extends Sprig
{
    public $_db = "global";

	protected $_title_key = 'url';

	protected $_sorting = array('url' => 'asc');

	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,
			'url' => new Sprig_Field_Char(array(
				'empty'  => FALSE,
				'unique' => TRUE,
			)),
			'domain' => new Sprig_Field_Char(array(
				'empty'  => FALSE,
				'unique' => FALSE,
			)),
			'folder' => new Sprig_Field_Char(array(
				'empty'  => TRUE,
				'unique' => FALSE,
			)),
			'site' => new Sprig_Field_BelongsTo(array(
				'model' => 'Site',
                'column' => 'site_id', // Column in the site_url-table
                // 'foreign_key' => 'id' // Is assumed
			)),
		);
	}
}
    
?>
