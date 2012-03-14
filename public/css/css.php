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

if (config('cache.css.enabled') && is_file(CACHE . 'app.css')) {
    $last_modified_time = filemtime(CACHE . 'app.css'); 
    $etag = md5_file(CACHE . 'app.css'); 
    header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT"); 
    header("Etag: $etag");
    if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time ||
        @trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
        header("HTTP/1.1 304 Not Modified");
        exit;
    }
    header('Content-Type: text/css');
    readfile(CACHE . 'app.css');
    exit();
}

$output = '';

// load reset css
$output .= file_get_contents(TERRIFIC_DIR . '/css/core/reset.css');

// load plugin css
foreach (glob(TERRIFIC_DIR . '/css/elements/*.css') as $entry) {
    if (is_file($entry)) {
        $output .= file_get_contents($entry);
    }
}

// load module css including skins
foreach (glob(TERRIFIC_DIR . '/modules/*', GLOB_ONLYDIR) as $dir) {
    $module = basename($dir);
    $css = $dir . '/css/' . strtolower($module) . '.css';
    if (is_file($css)) {
        $output .= file_get_contents($css);
    }
    foreach (glob($dir . '/css/skin/*') as $entry) {
        if (is_file($entry)) {
            $output .= file_get_contents($entry);
        }
    }
}

if (config('cache.css.enabled')) {
    require LIBRARY . 'thirdparty/cssmin/cssmin.php';
    $output = CssMin::minify($output);
    file_put_contents(CACHE . 'app.css', $output);
}
header("Content-Type: text/css");
echo $output;

?>
