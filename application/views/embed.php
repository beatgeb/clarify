<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

$screen_id = intval($_REQUEST['screen']);
$width = intval($_REQUEST['width']);

// Load comments for this screen and layer
$screen = $db->single("SELECT id, width FROM screen WHERE id = '" . $screen_id . "' LIMIT 1");
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
document.write('<?= $css ?>');
document.write('</style>');
document.write('<div class="st-widget <?= $class ?>">');
document.write('<img src="<?= config('application.domain') ?><?= config('application.baseurl') ?>?view=api&action=screen.thumbnail&screen=<?= $screen_id ?>&width=<?= $width ?>" width="<?= $width ?>" />');
<? foreach ($comments as $comment) { ?>
document.write('<div class="st-def" style="top:<?= $comment['y']*$factor+$offset ?>px;left:<?= $comment['x']*$factor+$offset ?>px;"><a href="javascript:;" class="dot"><span class="nr"><?= $comment['nr'] ?></span></a></div>');
<? } ?>
document.write('</div>');