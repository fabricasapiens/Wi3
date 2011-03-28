<?php defined('SYSPATH') or die('No direct script access.');

return array(
    'version' => "1.0",
	'pagefillers' => array(
		'default' => array(
            'path' => Wi3::inst()->pathof->app."pagefillers/default/"
        ),
	),
);
