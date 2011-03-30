<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Auth User Model
 * @package Sprig Auth
 * @author	Paul Banks
 */
class Model_Site_Pageposition extends Sprig_MPTT
{
    public $_db = 'site'; // This database instance will be defined in the Wi3 setup, via Wi3_Database::instance("site");
    
	protected $_title_key = 'id';

	protected $_sorting = array('lft' => 'asc'); // Default to Ascending sorting

	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,
            
            'pages' => new Sprig_Field_HasMany(array(
				'model' => 'Site_Page',
				'editable' => FALSE,
                //'foreign_key' => 'site_pageposition_id'
                //'foreign_field' => 'pageposition'
			)),
            
            'lft' => new Sprig_Field_MPTT_Left,
            'rgt' => new Sprig_Field_MPTT_Right,
            'lvl' => new Sprig_Field_MPTT_Level,
            'scope' => new Sprig_Field_MPTT_Scope,
		);
	}
    
}
    
?>
