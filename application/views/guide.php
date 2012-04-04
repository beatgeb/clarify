<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

lock();

$project_id = intval($route[2]);
$project = $db->single("SELECT id, name FROM project WHERE id = '" . $project_id . "' AND creator = '" . userid() . "'");
if (!$project) { die(); }
$screens = $db->data("SELECT id, title, description FROM screen WHERE project = '" . $project['id'] . "'");
$colors = $db->data("SELECT id, hex, name, r, g, b, alpha FROM project_color WHERE project = '" . $project['id'] . "'");
$comments = $db->data("SELECT d.id, d.content, d.nr, d.layer, d.screen FROM comment d LEFT JOIN screen s ON s.id = d.screen WHERE s.project = '" . $project['id'] . "'");
$layers = array();
foreach ($comments as $comment) {
    $layers[$comment['screen']][] = $comment;
}
?>
<!DOCTYPE html>
<html class="mod modLayout skinLayoutGuide">
<head>
    <title>Styleguide - Clarify</title>
    <? require 'partials/head.php' ?>
</head>
<body>
    <div class="mod modGuide">
        <h1><?= $project['name'] ?></h1>
        <div class="chapter">
            <h2>1. Screens</h2>
            <? foreach ($screens as $index => $screen) { ?>
            <h3>1.<?= $index + 1 ?>. <?= $screen['title'] ?></h3>
            <div class="screen"><script type="text/javascript" src="<?= R ?>embed/<?= $screen['id'] ?>/580"></script></div>
            <? if (isset($layers[$screen['id']])) { ?>
            <ul class="definitions">
                <? foreach ($layers[$screen['id']] as $comment) { ?>
                <li>
                    <div class="dot">
                        <div class="nr"><?= $comment['nr'] ?></div>
                    </div>
                    <p><?= $comment['content'] ?>&nbsp;</p>
                </li>
                <? } ?>
            </ul>
            <? } ?>
            <p><?= $screen['description'] ?></p>
            <? } ?>
        </div>
        <div class="chapter pagebreak">
            <h2>2. Colors</h2>
            <div class="colors">
            <? foreach ($colors as $color) { ?>
            <div class="color">
                <div class="box" style="background: #<?= $color['hex'] ?>;<?= $color['hex'] == 'ffffff' ? 'border: 1px solid #666;' : '' ?>"></div>
                <div class="meta meta-hex">#<?= strtoupper($color['hex']) ?></div>
                <div class="meta meta-rgb"><?= $color['r'] ?>, <?= $color['g'] ?>, <?= $color['b'] ?></div>
                <div class="meta meta-less">@undefined</div>
                <div class="meta meta-name">
                <? if ($color['name'] != '') { ?>
                <?= $color['name'] ?><br />
                <? } ?>
                </div>
            </div>
            <? } ?>
            </div>
        </div>
    </div>
    <? require 'partials/foot.php'; ?>
</body>
</html>