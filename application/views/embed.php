<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

$screen_code = $route[2];
$width = intval($route[3]);

if (strlen($screen_code) != 8) {
    die('please provide a valid code');
}
if ($width <= 0) {
    die('please provide the width (px) (> 0)');
}

// Load comments for this screen and layer
$screen = $db->single("SELECT id, width, project, embeddable FROM screen WHERE code = '" . $screen_code . "' LIMIT 1");
if (!$screen) { die(); }

// check project permissions
if (!$screen['embeddable'] && !has_permission($screen['project'], 'VIEW')) {
	die();
}

// check if embeddable
$screen_id = $screen['id'];
$comments = $db->data("SELECT x, y, nr FROM comment WHERE screen = '" . $screen_id . "'");
$factor = $width / $screen['width'];
$class = 'st-widget-large';
$offset = 0;
if ($factor < 0.6) {
    $class = 'st-widget-medium';
    $offset = 0;
}
$css = trim(str_replace(array("\n","'"), array("","\'"), file_get_contents(TERRIFIC . 'css/embed/comments.css')));
        
header('Content-Type: text/javascript');
?>
document.write('<style type="text/css">');
document.write('<?php print $css ?>');
document.write('</style>');
document.write('<div class="st-widget <?php print $class ?>">');
document.write('<img src="<?php print config('application.domain') ?><?php print config('application.baseurl') ?>api/screen/thumbnail/<?php print $screen_id ?>/<?php print $width ?>" width="<?php print $width ?>" />');
<?php foreach ($comments as $comment) { ?>
document.write('<div class="st-def" style="top:<?php print round($comment['y']*$factor+$offset) ?>px;left:<?php print round($comment['x']*$factor+$offset) ?>px;"><a href="javascript:;" class="dot"><span class="nr"><?php print $comment['nr'] ?></span></a></div>');
<?php } ?>
document.write('</div>');
