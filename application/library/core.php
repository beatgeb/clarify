<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/**
 * Get a configuration variable by path.
 * 
 * Usage:
 * $title = config('application.title'); 
 *
 * @param var Name of the variable
 * @param value Default value to return if the variable is not set
 */
function config($var, $value = null) {
    global $config;
    if ($config == null) {
        require CONFIG . 'config.php';
        require CONFIG . 'config-private.php';
    }
    return isset($config[$var]) ? $config[$var] : $value;
}

/**
 * Perform application shutdown tasks.
 */
function shutdown() {
    global $start;
    //echo "\n<!-- " . round(microtime(true) - $start, 4) . "s -->";
}

/**
 * Returns current users ID. If anonymous, null will be returned.
 */
function userid() {
    //return $_SESSION['user']['id'];
    return 1;
}

/**
 * Returns the table name for a given identifier.
 * @param $id Table ID
 */
function table($id) {
    return $id;
}

/**
 * Perform a login.
 * 
 * @return 
 * @param $username string
 * @param $password string password in plain text
 */
function login() {
    if (!isset($_REQUEST['username']) || !isset($_REQUEST['password'])) {
        return;
    }
    global $db;
    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];
    $password = md5($password . config('security.password.hash'));
    $user = $db->single("
    	SELECT id, email, name, username 
    	FROM user 
    	WHERE 
    		email = '" . $username . "' AND 
    		password = '" . $password . "' 
    	LIMIT 1
    ");
    if ($user != null) {
        $_SESSION['auth'] = md5(config('security.password.hash') . $user['id']);
        $_SESSION['user']['username'] = $user['username'];
        $_SESSION['user']['email'] = $user['email'];
        $_SESSION['user']['id'] = $user['id'];
        $_SESSION['user']['name'] = $user['name'];
        return true;
    }
    global $error;
    $error['message'] = 'Invalid e-mail or password entered.';
    return false;
}

/**
 * Returns, whether the current user is authenticated or not
 * @return true if authenticated, false otherwise
 */
function authenticated() {
    return $_SESSION['auth'] == md5(config('security.password.hash') . $_SESSION['user']['id']);
}

/**
 * Locks a specific script for unauthenticated requests.
 */
function lock() {
    if (!authenticated()) {
        header('Location: ' . R . '?view=auth&referer=' . $_SERVER['REQUEST_URI']);
        exit();
    }
}

function d($str) {
	return htmlspecialchars($str);
}

function getRgb($category, $variable) {
    global $settings;
    return "rgb(" . $settings[$category][$variable] . ")";
}

function hexToRgb($hex) {
    return hexdec(substr($hex,1,2)) . ',' . hexdec(substr($hex,3,2)) . ',' . hexdec(substr($hex,5,2));
}

function getBoolean($category, $variable) {
    global $settings;
}

function truncate($string, $length, $add = '...') {
    if (strlen($string) > $length) {
        return substr($string, 0, $length) . $add;
    }
    return $string;
}

?>