<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Sprig_MPTT test model
 *
 * @package MPTT
 * @author Mathew Davies
 * @author Kiall Mac Innes
 * @author Paul Banks
 */
class Model_MPTT_Test extends Sprig_MPTT {
	
	protected $_table = 'sprig_mptt_test';
	
	protected function _init()
	{
		// Notice how the MPTT fields are added automatically
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,
			'name' => new Sprig_Field_Char,
		);
	}
	
	public function create_table()
	{
		$this->delete_table();
		Database::instance()->query(NULL, 'CREATE TABLE `sprig_mptt_test` (`id` INT( 255 ) UNSIGNED NOT NULL AUTO_INCREMENT ,`lvl` INT( 255 ) NOT NULL ,`lft` INT( 255 ) NOT NULL ,`rgt` INT( 255 ) NOT NULL ,`scope` INT( 255 ) NOT NULL ,`name` VARCHAR( 255 ) NOT NULL ,PRIMARY KEY ( `id` )) ENGINE = MYISAM ', TRUE);
		$this->reset_table();
	}
	
	public function reset_table()
	{
		Database::instance()->query(NULL, 'TRUNCATE TABLE `sprig_mptt_test`', TRUE);
		DB::insert('sprig_mptt_test')->values(array('id' => 1,'lvl' => 0,'lft' => 1, 'rgt' => 22, 'scope' => 1, 'name' => 'Root Node'))->execute();

		DB::insert('sprig_mptt_test')->values(array('id' => 2,'lvl' => 1,'lft' => 2, 'rgt' => 3, 'scope' => 1, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 3,'lvl' => 1,'lft' => 4, 'rgt' => 7, 'scope' => 1, 'name' => 'Normal Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 4,'lvl' => 2,'lft' => 5, 'rgt' => 6, 'scope' => 1, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 5,'lvl' => 1,'lft' => 8, 'rgt' => 9, 'scope' => 1, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 6,'lvl' => 1,'lft' => 10, 'rgt' => 21, 'scope' => 1, 'name' => 'Normal Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 7,'lvl' => 2,'lft' => 11, 'rgt' => 12, 'scope' => 1, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 8,'lvl' => 2,'lft' => 13, 'rgt' => 18, 'scope' => 1, 'name' => 'Normal Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 9,'lvl' => 3,'lft' => 14, 'rgt' => 15, 'scope' => 1, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 10,'lvl' => 3,'lft' => 16, 'rgt' => 17, 'scope' => 1, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 11,'lvl' => 2,'lft' => 19, 'rgt' => 20, 'scope' => 1, 'name' => 'Leaf Node'))->execute();
		
		DB::insert('sprig_mptt_test')->values(array('id' => 12,'lvl' => 0,'lft' => 1, 'rgt' => 22, 'scope' => 2, 'name' => 'Root Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 13,'lvl' => 1,'lft' => 2, 'rgt' => 3, 'scope' => 2, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 14,'lvl' => 1,'lft' => 4, 'rgt' => 7, 'scope' => 2, 'name' => 'Normal Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 15,'lvl' => 2,'lft' => 5, 'rgt' => 6, 'scope' => 2, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 16,'lvl' => 1,'lft' => 8, 'rgt' => 9, 'scope' => 2, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 17,'lvl' => 1,'lft' => 10, 'rgt' => 21, 'scope' => 2, 'name' => 'Normal Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 18,'lvl' => 2,'lft' => 11, 'rgt' => 12, 'scope' => 2, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 19,'lvl' => 2,'lft' => 13, 'rgt' => 18, 'scope' => 2, 'name' => 'Normal Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 20,'lvl' => 3,'lft' => 14, 'rgt' => 15, 'scope' => 2, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 21,'lvl' => 3,'lft' => 16, 'rgt' => 17, 'scope' => 2, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 22,'lvl' => 2,'lft' => 19, 'rgt' => 20, 'scope' => 2, 'name' => 'Leaf Node'))->execute();
		
		DB::insert('sprig_mptt_test')->values(array('id' => 23,'lvl' => 0,'lft' => 1, 'rgt' => 22, 'scope' => 3, 'name' => 'Root Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 24,'lvl' => 1,'lft' => 2, 'rgt' => 3, 'scope' => 3, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 25,'lvl' => 1,'lft' => 4, 'rgt' => 7, 'scope' => 3, 'name' => 'Normal Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 26,'lvl' => 2,'lft' => 5, 'rgt' => 6, 'scope' => 3, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 27,'lvl' => 1,'lft' => 8, 'rgt' => 9, 'scope' => 3, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 28,'lvl' => 1,'lft' => 10, 'rgt' => 21, 'scope' => 3, 'name' => 'Normal Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 29,'lvl' => 2,'lft' => 11, 'rgt' => 12, 'scope' => 3, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 30,'lvl' => 2,'lft' => 13, 'rgt' => 18, 'scope' => 3, 'name' => 'Normal Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 31,'lvl' => 3,'lft' => 14, 'rgt' => 15, 'scope' => 3, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 32,'lvl' => 3,'lft' => 16, 'rgt' => 17, 'scope' => 3, 'name' => 'Leaf Node'))->execute();
		DB::insert('sprig_mptt_test')->values(array('id' => 33,'lvl' => 2,'lft' => 19, 'rgt' => 20, 'scope' => 3, 'name' => 'Leaf Node'))->execute();
	}
	
	public function delete_table()
	{
		Database::instance()->query(NULL, 'DROP TABLE IF EXISTS `sprig_mptt_test`', TRUE);
	}
}