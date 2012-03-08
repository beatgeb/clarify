<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

$project = $db->single("SELECT * FROM project WHERE id = '" . intval($_REQUEST['project']) . "'");
$screens = $db->data("SELECT * FROM screen WHERE project = '" . intval($_REQUEST['project']) . "'");
$colors = $db->data("SELECT * FROM project_color WHERE project = '" . intval($_REQUEST['project']) . "'");
$definitions = $db->data("SELECT d.id, d.content, d.nr, d.layer, d.screen FROM comment d LEFT JOIN screen s ON s.id = d.screen WHERE s.project = '" . intval($_REQUEST['project']) . "'");
$layers = array();
foreach ($definitions as $definition) {
    $layers[$definition['screen']][$definition['layer']][] = $definition;
}
?>
<!DOCTYPE html>
<html class="mod modLayout skinLayoutGuide">
<head>
    <title>Styleguide - Clarify</title>
    <link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css' />
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
                <? foreach ($layers[$screen['id']][1] as $definition) { ?>
                <li>
                    <div class="dot">
                        <div class="nr"><?= $definition['nr'] ?></div>
                    </div>
                    <p><?= $definition['content'] ?>&nbsp;</p>
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