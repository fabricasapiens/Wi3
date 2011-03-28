<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'cookie' => array(
	    'name' => 'wi3',
		'encrypted' => FALSE, // ACL can't deal with encrypted sessions... :(
		'lifetime' => 31556926 // approximately one year
	),
);
