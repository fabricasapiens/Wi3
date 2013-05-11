<?php defined('SYSPATH') or die('No direct script access.');

class Controller_ACLBase extends Controller_ACL {

	public function before()
    {
        // By default, don't cache pages
        Wi3::inst()->cache->doNotCache();
    }

} // End ACL Base controller
