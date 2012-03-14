<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

define('TERRIFIC_DIR', dirname(__FILE__) . '/..');

require getcwd() . '/../../application/library/bootstrap.php';

if (config('cache.js.enabled') && is_file(CACHE . 'app.js')) {
    $last_modified_time = filemtime(CACHE . 'app.js'); 
    $etag = md5_file(CACHE . 'app.js'); 
    header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT"); 
    header("Etag: $etag");
    if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time ||
        @trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
        header("HTTP/1.1 304 Not Modified");
        exit;
    }
    header('Content-Type: text/javascript');
    readfile(CACHE . 'app.js');
    exit();
}

// initialize
$core = file_get_contents(TERRIFIC_DIR . '/js/core/static/jquery-1.7.1.min.js');
$core .= file_get_contents(TERRIFIC_DIR . '/js/core/static/terrific-1.0.0.min.js');

$output = '';

// load libraries
foreach (glob(TERRIFIC_DIR . '/js/libraries/static/*.js') as $entry) {
    if (is_file($entry)) {
        $output .= file_get_contents($entry);
    }
}

// load plugins
foreach (glob(TERRIFIC_DIR . '/js/plugins/static/*.js') as $entry) {
    if (is_file($entry)) {
        $output .= file_get_contents($entry);
    }
}

// load connectors
foreach (glob(TERRIFIC_DIR . '/js/connectors/static/*.js') as $entry) {
    if (is_file($entry)) {
        $output .= file_get_contents($entry);
    }
}

// load modules
foreach (glob(TERRIFIC_DIR . '/modules/*', GLOB_ONLYDIR) as $dir) {
    $module = basename($dir);
    $js = $dir . '/js/Tc.Module.' . $module . '.js';
    if (is_file($js)) {
        $output .= file_get_contents($js);
    }
    foreach (glob($dir . '/js/skin/*.js') as $entry) {
        if (is_file($entry)) {
            $output .= file_get_contents($entry);
        }
    }
}
    
if (config('cache.js.enabled')) {
    //require LIBRARY . 'thirdparty/jsmin/jsmin.php';
    //$output = JSMin::minify($output);
    file_put_contents(CACHE . 'app.js', $core . $output);
}
header("Content-Type: text/javascript; charset=utf-8");
echo $core . $output;

?>
