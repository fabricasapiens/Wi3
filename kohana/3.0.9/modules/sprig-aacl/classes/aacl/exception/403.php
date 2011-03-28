<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * 403 "Permission denied" exception
 * 
 * @see			http://github.com/banks/aacl
 * @package		AACL
 * @uses		Auth
 * @uses		Sprig
 * @author		Paul Banks
 * @copyright	(c) Paul Banks 2010
 * @license		MIT
 */
class AACL_Exception_403 extends AACL_Exception 
{
	public function __construct()
	{
		parent::__construct('Permission Denied');
	}
}