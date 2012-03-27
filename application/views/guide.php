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

$project = $db->single("SELECT id, name FROM project WHERE id = '" . intval($_REQUEST['project']) . "'");
$screens = $db->data("SELECT id, title, description FROM screen WHERE project = '" . $project['id'] . "'");
$colors = $db->data("SELECT id, hex, name, r, g, b, alpha FROM project_color WHERE project = '" . $project['id'] . "'");
$comments = $db->data("SELECT d.id, d.content, d.nr, d.layer, d.screen FROM comment d LEFT JOIN screen s ON s.id = d.screen WHERE s.project = '" . $project['id'] . "'");
$layers = array();
foreach ($comments as $comment) {
    $layers[$comment['screen']][$comment['layer']][] = $comment;
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
        <p class="meta">
            Styleguide vom <?= date('d.m.Y') ?><br />
            Version 1.0
        </p>
        <div class="chapter">
            <h2>1. Screens</h2>
            <? foreach ($screens as $index => $screen) { ?>
            <h3>1.<?= $index + 1 ?>. <?= $screen['title'] ?></h3>
            <div class="screen"><script type="text/javascript" src="<?= R ?>?view=embed&screen=<?= $screen['id'] ?>&width=580"></script></div>
            <? if (isset($layers[$screen['id']][1])) { ?>
            <ul class="definitions">
                <? foreach ($layers[$screen['id']][1] as $comment) { ?>
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
            <? foreach ($colors as $color) { ?>
            <div class="color">
                <div class="box" style="background: #<?= $color['hex'] ?>;<?= $color['hex'] == 'ffffff' ? 'border: 1px solid #666;' : '' ?>"></div>
                <div class="meta">
                    <p>
                        <span class="hex">#<?= strtoupper($color['hex']) ?></span><br />
                        <? if ($color['name'] != '') { ?>
                        <?= $color['name'] ?><br />
                        <? } ?>
                        R<?= $color['r'] ?> G<?= $color['g'] ?> B<?= $color['b'] ?><br />
                        <? if ($color['alpha'] < 255) { ?>
                        Alpha: <?= $color['alpha'] ?>
                        <? } ?>
                    </p>
                </div>
            </div>
            <? } ?>
        </div>
    </div>
    <? require 'partials/foot.php'; ?>
</body>
</html>