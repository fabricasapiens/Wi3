<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
	'site' => array
	(
		'type'       => 'mysql',
		'connection' => array(
			/**
			 * The following options are available for MySQL:
			 *
			 * string   hostname     server hostname, or socket
			 * string   database     database name
			 * string   username     database username
			 * string   password     database password
			 * boolean  persistent   use persistent connections?
			 *
			 * Ports and sockets may be appended to the hostname.
			 */
			'hostname'   => 'localhost',
			'database'   => 'eenwebsitemaken_'.Wi3::inst()->sitearea->site->databasesafename,
			'username'   => 'root',
			'password'   => 'athene87',
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
        'profiling'    => Kohana::$environment !== Kohana::PRODUCTION,
        'caching'    => Kohana::$environment === Kohana::PRODUCTION,
	)
);
