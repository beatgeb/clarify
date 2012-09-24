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

// Authentication
$config['ldap.auth.enabled'] = false;
$config['ldap.auth.server'] = 'ldaps://auth.yourdomain.com';
$config['ldap.auth.server.port'] = 636;
$config['ldap.auth.server.username'] = '';
$config['ldap.auth.server.password'] = '';
$config['ldap.auth.base'] = 'dc=company,dc=ag';
$config['ldap.auth.userbase'] = 'ou=People,ou=ch,' . $config['ldap.base'];

// Twitter Authentication
$config['twitter.auth.consumerkey'] = '';
$config['twitter.auth.consumersecret'] = '';

// Mailservice
$config['postmark.api.key'] = '';
$config['postmark.from.address'] = '';
$config['postmark.from.name'] = '';
 
?>