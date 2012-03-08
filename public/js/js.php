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

// load terrificjs
$output = '';
$output .= file_get_contents(TERRIFIC_DIR . '/js/core/static/jquery-1.7.1.min.js');
$output .= file_get_contents(TERRIFIC_DIR . '/js/core/static/terrific-1.0.0.min.js');

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

require_once '../../application/library/thirdparty/jsmin.php';
//$output = JSMin::minify($output);
header("Content-Type: text/javascript; charset=utf-8");
echo $output;

?>
