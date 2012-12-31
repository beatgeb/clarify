<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

define('ACTIVITY_VERB_PICK', 'pick');
define('ACTIVITY_VERB_CREATE', 'create');
define('ACTIVITY_VERB_JOIN', 'join');
define('ACTIVITY_VERB_COMMENT', 'comment');
define('ACTIVITY_VERB_DEFINE', 'define');
define('ACTIVITY_VERB_ADD', 'add');
define('ACTIVITY_VERB_MEASURE', 'measure');

define('OBJECT_TYPE_USER', 'user');
define('OBJECT_TYPE_SCREEN', 'screen');
define('OBJECT_TYPE_COLOR', 'color');
define('OBJECT_TYPE_COMMENT', 'comment');
define('OBJECT_TYPE_MODULE', 'module');
define('OBJECT_TYPE_PROJECT', 'project');
define('OBJECT_TYPE_MEASURE', 'measure');
define('OBJECT_TYPE_FONT', 'font');

define('VIEW_API', 'api');
define('VIEW_BROWSER', 'browser');
define('VIEW_SCREEN', 'screen');
define('VIEW_AUTH', 'auth');
define('VIEW_GUIDE', 'guide');
define('VIEW_LIBRARY', 'library');
define('VIEW_EMBED', 'embed');
define('VIEW_PROJECT', 'project');
define('VIEW_REGISTER', 'register');
define('VIEW_SHOWCASE', 'showcase');

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
        if (is_file(CONFIG . 'config-private.php')) {
            require CONFIG . 'config-private.php';
        }
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

function login() {
    global $db;
    global $cache;

    // load project permissions
    $projects = $cache->get('projects-' . userid());
    if (!$projects) {
        $owned_projects = $db->data("SELECT id FROM project WHERE creator = '" . userid() . "'");
        $projects = array();
        foreach ($owned_projects as $project) {
            $projects[$project['id']] = 'ADMIN';
        }
        $collaboration_projects = $db->data("SELECT project, permission FROM project_permission WHERE user = '" . userid() . "'");
        foreach ($collaboration_projects as $project) {
            $projects[$project['project']] = $project['permission'];
        }
        $cache->set('projects-' . userid(), $projects);
    }
    $_SESSION['user']['permissions']['project'] = $projects;
}

/**
 * Returns current users ID. If anonymous, null will be returned.
 */
function userid() {
    return user('id');
}

/**
 * Returns current users name. If anonymous, null will be returned.
 */
function user($field) {
    return $_SESSION['user'][$field];
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
        header('Location: ' . R . 'auth/?referer=' . $_SERVER['REQUEST_URI']);
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

function hexToRgbArray($hex) {
    return array(
        'r' => hexdec(substr($hex,1,2)), 
        'g' => hexdec(substr($hex,3,2)), 
        'b' => hexdec(substr($hex,5,2))
    );
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

function gen_uuid($salt, $len = 8) {
    $hex = md5($salt . uniqid("", true));
    $pack = pack('H*', $hex);
    $tmp =  base64_encode($pack);
    $uid = preg_replace("#(*UTF8)[^A-Za-z0-9]#", "", $tmp);
    $len = max(4, min(128, $len));
    while (strlen($uid) < $len)
        $uid .= gen_uuid(22);
    return substr($uid, 0, $len);
}

function slug($string) {
    $string = strtolower($string);
    $string = str_replace(array(".","ü","ä","ö"), array("-","ue","ae","oe"), $string);
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    $string = preg_replace("/[\s-]+/", " ", $string);
    $string = preg_replace("/[\s_]/", "-", $string);
    return $string;
}

function gravatar($email, $size) {
    $grav_url = "https://secure.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?d=mm&s=" . $size;
    return $grav_url;
}

function json($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
}

?>