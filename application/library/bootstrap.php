<?php 

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

// initialize benchmarking
global $start;
$start = microtime(true);

// configure error reporting level
error_reporting(E_ALL ^ E_NOTICE);

// set library and class location
define('APP', dirname(__FILE__) . '/../');
define('LIBRARY', APP . 'library/');
define('ACTIONS', APP . 'actions/');
define('VIEWS', APP . 'views/');
define('CONFIG', APP . 'config/');
define('DATA', APP . 'data/');
define('CACHE', APP . 'cache/');
define('TERRIFIC', APP . '../public/');
define('UPLOAD', APP . '../public/upload/');

// load core function library
require LIBRARY . 'core.php';
require LIBRARY . 'api.php';
require LIBRARY . 'db.php';
require LIBRARY . 'session.php';
require LIBRARY . 'auth.php';
require LIBRARY . 'cache.php';
require LIBRARY . 'thirdparty/postmark/Postmark.php';

// define additional constants
define('R', config('application.baseurl'));
define('S', R . 'static/');

// postmark constants
define('POSTMARKAPP_API_KEY', config('postmark.api.key'));
define('POSTMARKAPP_MAIL_FROM_ADDRESS', config('postmark.from.address'));
define('POSTMARKAPP_MAIL_FROM_NAME', config('postmark.from.name'));

// create database connection instance
global $db;
$db = new Database(
    config('database.server.host'), 
    config('database.server.username'), 
    config('database.server.password'), 
    config('database.name')
);

global $cache;
if (class_exists('Memcache')) {
	$cache = new Memcache;
	$cache->connect(
	    config('memcached.server.name'),
	    config('memcached.server.port')
	);
} else {
	$cache = new Cache;
}

// set timezone to UTC
date_default_timezone_set('UTC');

// register shutdown function
register_shutdown_function('shutdown');

?>