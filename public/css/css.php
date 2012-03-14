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
    require LIBRARY . 'thirdparty/cssmin.php';
    $output = CssMin::minify($output);
    file_put_contents(CACHE . 'app.css', $output);
}
header("Content-Type: text/css");
echo $output;

?>
