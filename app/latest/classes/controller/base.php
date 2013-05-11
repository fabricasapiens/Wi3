<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Base extends Controller_Template {

	public function before()
    {
        // By default, don't cache pages
        Wi3::inst()->cache->doNotCache();
    }

} // End Base controller
