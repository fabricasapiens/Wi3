<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Global Site Model
 * This model contains the site settings that are useful from within the sitearea. With an export, this information will be exported as well.
 * The name of the site is per definition not exportable (exports are anonymous), thus the name is not to be found in this model's database setup
 * It will however be injected from the global site model once the local (i.e. this) model is loaded. See wi3.php
 * @package
 * @author	Willem Mulder
 */
class Model_Site_Site extends Sprig
{
    public $_db = "site";
    
	protected $_title_key = 'name';

	protected $_sorting = array('id' => 'asc');

	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,
            'name' => new Sprig_Field_Char(array(
                'in_db' => FALSE, // Not te be found in Database. Is inserted from the global site's name. See notes above.
            )),
            'databasesafename' => new Sprig_Field_Char(array(
                'empty'  => TRUE,
                'in_db' => FALSE, // Not te be found in Database. Is inserted from the global site's name at runtime
            )),
			'landingpage' => new Sprig_Field_Integer(array( // Page that is loaded when no arguments are given to the site
				'empty'  => TRUE,
				'unique' => FALSE,
			)),
            'notfoundpage' => new Sprig_Field_Integer(array( // Page that is loaded when the requested page does not exist
				'empty'  => TRUE,
				'unique' => FALSE,
			)),
            'loginpage' => new Sprig_Field_Integer(array( // Page that is loaded when a user needs to be logged in to access certain content
				'empty'  => TRUE,
				'unique' => FALSE,
			))
		);
	}
    
}
    
?>
