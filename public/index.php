<?php 

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

require getcwd() . '/../application/library/bootstrap.php';

// start session
session_start();

// check for login credentials
login();

define('VIEW_API', 'api');
define('VIEW_BROWSER', 'browser');
define('VIEW_SCREEN', 'screen');
define('VIEW_AUTH', 'auth');
define('VIEW_GUIDE', 'guide');
define('VIEW_LIBRARY', 'library');
define('VIEW_EMBED', 'embed');
define('VIEW_PROJECT', 'project');
define('VIEW_REGISTER', 'register');

$route = str_replace('?' . $_SERVER['QUERY_STRING'], '', explode('/', $_SERVER['REQUEST_URI']));
if ($route[1] == "") { $route[1] = VIEW_BROWSER; }

// view whitelisting, the pragmatic way...
$view = $route[1];
switch ($view) {
    case VIEW_API:
    case VIEW_BROWSER:
    case VIEW_SCREEN:
    case VIEW_AUTH:
    case VIEW_GUIDE:
    case VIEW_LIBRARY:
    case VIEW_EMBED:
    case VIEW_PROJECT:
    case VIEW_REGISTER:
        break;
    default:
        lock();
        break;
}

if ($view != 'empty' && is_file(VIEWS . $view . '.php')) { 
    require VIEWS . $view . '.php';
}

?>