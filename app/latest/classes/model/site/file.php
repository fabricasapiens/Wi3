<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Sprig Auth User Model
 * @package Sprig Auth
 * @author	Paul Banks
 */
class Model_Site_File extends Sprig_MPTT
{
    public $_db = 'site'; // This database instance will be defined in the Wi3 setup, via Wi3_Database::instance("site");

	protected $_title_key = 'id';

	protected $_sorting = array('lft' => 'asc');

	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,

            'type' => new Sprig_Field_Char(array( // can be folder or file
				'empty'  => FALSE, // can not be empty
				'unique' => FALSE,
			)),

            'title' => new Sprig_Field_Char(array(
				'empty'  => FALSE, // can not be empty
				'unique' => FALSE,
			)),
            'filename' => new Sprig_Field_Char(array(
				'empty'  => TRUE, // can be empty, if file is a folder
				'unique' => TRUE, // should be unique
			)),

            'keywords' => new Sprig_Field_Char(array(
				'empty'  => TRUE,
				'unique' => FALSE,
			)),
            "created" => new Sprig_Field_Timestamp(array(
				'empty'  => TRUE,
			)),
            "lastupdated" => new Sprig_Field_Timestamp(array( // When this field was updated (use at will)
				'empty'  => TRUE,
			)),

			"owner" => new Sprig_Field_BelongsTo(array( // Owner of this file (default is the creator of the file)
				'empty'  => FALSE,
                'model' => 'User',
                'column' => 'owner_id', // Column in the site_file-model
                // 'foreign_key' => 'id' // Is assumed
			)),
            "viewright" => new Sprig_Field_Char(array(  // What right does a user/group need to view
				'empty'  => TRUE,
				'unique' => FALSE,
			)),
            "editright" => new Sprig_Field_Char(array(  // What right does a user/group need to edit
				'empty'  => TRUE,
				'unique' => FALSE,
			)),
            "adminright" => new Sprig_Field_Char(array(   // what right does a user/group need to delete the file or edit one of the other rights. Default is 'admin'
				'empty'  => TRUE,
				'unique' => FALSE,
                'default' => 'admin'
			)),

            'lft' => new Sprig_Field_MPTT_Left,
            'rgt' => new Sprig_Field_MPTT_Right,
            'lvl' => new Sprig_Field_MPTT_Level,
            'scope' => new Sprig_Field_MPTT_Scope,
		);
	}

	public function isImage() {
		if ($this->type !== "file") {
			return false;
		}
		$extension = $this->getExtension();
        $imageExtensions = Array("jpg", "jpeg", "png", "bmp", "gif");
        return in_array($extension, $imageExtensions);
	}

	public function getExtension() {
        $lastdotpos = strrpos($this->filename, ".");
        return strtolower(substr($this->filename, $lastdotpos+1));
	}

}

?>
