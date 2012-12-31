<?php



require getcwd() . '/../../application/library/bootstrap.php';

if (config('cache.js.enabled') && is_file(TERRIFIC . 'js/app.js')) {
    $last_modified_time = filemtime(TERRIFIC . 'js/app.js'); 
    $etag = md5_file(TERRIFIC . 'js/app.js'); 
    header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT"); 
    header("Etag: $etag");
    if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time ||
        @trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
        header("HTTP/1.1 304 Not Modified");
        exit;
    }
    header('Content-Type: text/javascript');
    readfile(TERRIFIC . 'js/app.js');
    exit();
}

// initialize
$core = file_get_contents(TERRIFIC . 'js/core/static/jquery.min.js');
$core .= file_get_contents(TERRIFIC . 'js/core/static/terrific.min.js');

$output = '';

// load libraries
foreach (glob(TERRIFIC . 'js/libraries/static/*.js') as $entry) {
    if (is_file($entry)) {
        $output .= file_get_contents($entry);
    }
}

// load plugins
foreach (glob(TERRIFIC . 'js/plugins/static/*.js') as $entry) {
    if (is_file($entry)) {
        $output .= file_get_contents($entry);
    }
}

// load modules
$module_output = '';
foreach (glob(TERRIFIC . 'modules/*', GLOB_ONLYDIR) as $dir) {
    $module = basename($dir);
    $js = $dir . '/js/Tc.Module.' . $module . '.js';
    if (is_file($js)) {
        $module_output .= file_get_contents($js);
    }
    foreach (glob($dir . '/js/skin/*.js') as $entry) {
        if (is_file($entry)) {
            $module_output .= file_get_contents($entry);
        }
    }
}
    
if (config('cache.js.enabled')) {
    require LIBRARY . 'thirdparty/jsmin/jsmin.php';
    $module_output = JSMin::minify($module_output);
    $output .= $module_output;
    file_put_contents(TERRIFIC . 'js/app.js', $core . $output);
} else {
    $output .= $module_output;
}
header("Content-Type: text/javascript; charset=utf-8");
echo $core . $output;

?>
