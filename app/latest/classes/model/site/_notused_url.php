<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Auth User Model
 * @package Sprig Auth
 * @author	Paul Banks
 */
class Model_Site_Url extends Sprig
{
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
			'site' => new Sprig_Field_BelongsTo(array(
				'model' => 'Site',
                'column' => 'site_id', // Column in the site_url-table
                // 'foreign_key' => 'id' // Is assumed
			)),
		);
	}
}
    
?>