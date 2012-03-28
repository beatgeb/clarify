<?php
 
/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

global $config;
$config = array();
 
// Database
$config['database.name'] = 'clarify';
$config['database.server.type'] = 'mysql';
$config['database.server.host'] = '127.0.0.1';
$config['database.server.port'] = 3306;
$config['database.server.username'] = 'root';
$config['database.server.password'] = '';

// Memcached
$config['memcached.server.name'] = 'localhost';
$config['memcached.server.port'] = 30001;
 
// Application
$config['application.baseurl'] = '/';
$config['application.domain'] = 'http://clarify.yourdomain.com';

// Security
$config['security.password.hash'] = 'mH284Nks';
$config['security.channel.hash'] = 'fdq23o42';
$config['security.general.hash'] = 'jksuh4882';

// Cache
$config['cache.css.enabled'] = false;
$config['cache.js.enabled'] = false;

// Twitter Authentication
$config['twitter.auth.consumerkey'] = '';
$config['twitter.auth.consumersecret'] = '';
 
?>