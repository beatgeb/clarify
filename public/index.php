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

// enable output buffering
ob_start();

// check for login credentials
login();

define('VIEW_API', 'api');
define('VIEW_BROWSER', 'browser');
define('VIEW_SCREEN', 'screen');
define('VIEW_LOGIN', 'login');
define('VIEW_GUIDE', 'guide');
define('VIEW_LIBRARY', 'library');
define('VIEW_EMBED', 'embed');

// view whitelisting, the pragmatic way...

$view = isset($_REQUEST['view']) ? $_REQUEST['view'] : 'browser';
switch ($view) {
    case VIEW_API:
    case VIEW_BROWSER:
    case VIEW_SCREEN:
    case VIEW_LOGIN:
    case VIEW_GUIDE:
    case VIEW_LIBRARY:
    case VIEW_EMBED:
        break;
    default:
        lock();
        break;
}

if ($view != 'empty' && is_file(VIEWS . $view . '.php')) { 
    require VIEWS . $view . '.php';
}

?>