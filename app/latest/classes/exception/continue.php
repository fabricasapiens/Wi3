<?php defined('SYSPATH') or die ('No direct script access.');

// Throwing this exception will cause Wi3 to stop other execution, and just push out the current Request::instance()->response and headers
class Exception_Continue extends Exception 
{
	public function __construct()
	{
		parent::__construct('Permission Denied');
	}
}