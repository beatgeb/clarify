<?php



require getcwd() . '/../../application/library/bootstrap.php';

if (config('cache.css.enabled') && is_file(TERRIFIC . 'css/app.css')) {
    $last_modified_time = filemtime(TERRIFIC . 'css/app.css'); 
    $etag = md5_file(TERRIFIC . 'css/app.css'); 
    header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT"); 
    header("Etag: $etag");
    if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time ||
        @trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
        header("HTTP/1.1 304 Not Modified");
        exit;
    }
    header('Content-Type: text/css');
    readfile(TERRIFIC . 'css/app.css');
    exit();
}

$output = '';

// load reset css
$output .= file_get_contents(TERRIFIC . 'css/core/reset.css');

// load plugin css
foreach (glob(TERRIFIC . 'css/elements/*.css') as $entry) {
    if (is_file($entry)) {
        $output .= file_get_contents($entry);
    }
}

// load module css including skins
foreach (glob(TERRIFIC . 'modules/*', GLOB_ONLYDIR) as $dir) {
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
    file_put_contents(TERRIFIC . 'css/app.css', $output);
}
header("Content-Type: text/css");
echo $output;

?>
