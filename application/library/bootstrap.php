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
require LIBRARY . 'db.php';
require LIBRARY . 'session.php';
require LIBRARY . 'auth.php';

// define additional constants
define('R', config('application.baseurl'));
define('S', R . 'static/');

// create database connection instance
global $db;
$db = new Database(
    config('database.server.host'), 
    config('database.server.username'), 
    config('database.server.password'), 
    config('database.name')
);

/*
global $cache;
$cache = new Memcache;
$cache->connect(
    config('memcached.server.name'),
    config('memcached.server.port')
);

// set session save handler
session_set_save_handler(
    'sess_open', 
    'sess_close', 
    'sess_read', 
    'sess_write', 
    'sess_destroy', 
    'sess_gc'
);
*/

// register shutdown functions
register_shutdown_function('shutdown');
// register_shutdown_function('session_write_close');

?>