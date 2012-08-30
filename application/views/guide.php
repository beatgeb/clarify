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

// check project permissions
permission($project_id, 'VIEW');

$project = $db->single("SELECT id, creator, slug, name FROM project WHERE id = '" . $project_id . "'");
if (!$project) { die(); }
$screens = $db->data("SELECT id, title, description, code FROM screen WHERE project = '" . $project['id'] . "' ORDER BY title ASC");
$colors = $db->data("SELECT id, hex, name, name_css, r, g, b, alpha FROM project_color WHERE project = '" . $project['id'] . "' ORDER BY hue ASC", "id");
$comments = $db->data("SELECT d.id, d.content, d.nr, d.layer, d.screen FROM comment d LEFT JOIN screen s ON s.id = d.screen WHERE s.project = '" . $project['id'] . "'");
$fonts = $db->data("SELECT * FROM project_font WHERE project = " . $project_id);
$modules = $db->data("SELECT * FROM project_module WHERE project = " . $project_id);
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
        <div class="mod modToolbar">
            <div class="section section-nav">
                <a class="button btn-screen" href="/project/<?= $project['creator']; ?>/<?= $project['slug']; ?>/"><i></i><div class="tt">Back</div></a>
            </div>
        </div>
        <h1><?= $project['name'] ?></h1>
        <div class="chapter">
            <h2>Screens</h2>
            <? foreach ($screens as $index => $screen) { ?>
            <h3><?= $index + 1 ?>. <?= $screen['title'] ?></h3>
            <div class="screen"><script type="text/javascript" src="<?= R ?>embed/<?= $screen['code'] ?>/580"></script></div>
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

        <? if (sizeof($modules) > 0) { ?>
        <div class="chapter pagebreak">
            <h2>Modules</h2>
            <div class="modules">
                <? foreach ($modules as $index => $module) { ?>
                <h3><?= $index + 1 ?>. <?= $module['name'] ?></h3>
                <div class="module">
                    <div class="screenshot"><img src="<?= R .'upload/modules/' . $project_id .'/'. md5($module['id'] . config('security.general.hash')) ?>.png" /></div>
                    <div class="meta"><div class="code">&lt;div class="mod mod-<?= slug($module['name']) ?>"&gt;&lt;/div&gt;</div></div>
                </div>
                <? } ?>
            </div>
        </div>
        <? } ?>

        <div class="chapter pagebreak">
            <h2>Colors</h2>
            <h3>Palette</h3>
            <div class="colors">
            <? foreach ($colors as $color) { ?>
            <div class="color">
                <div class="box" style="background: #<?= $color['hex'] ?>;<?= $color['hex'] == 'ffffff' ? 'border: 1px solid #666;' : '' ?>"></div>
                <div class="meta meta-hex">#<?= strtoupper($color['hex']) ?></div>
                <div class="meta meta-rgb"><?= $color['r'] ?>, <?= $color['g'] ?>, <?= $color['b'] ?></div>
                <div class="meta meta-less">@<?= $color['name_css'] ?></div>
                <div class="meta meta-name">
                <? if ($color['name'] != '') { ?>
                <?= $color['name'] ?><br />
                <? } ?>
                </div>
            </div>
            <? } ?>
            </div>
        </div>

        <? if (sizeof($fonts) > 0) { ?>
        <div class="chapter pagebreak">
            <h2>Fonts</h2>
            <? foreach ($fonts as $font) { ?>
            <? $color = $colors[$font['color']]; ?>
            <h3><?= $font['name'] ?> - .<?= $font['name_css'] ?></h3>
            <div class="fonts">
                <div class="font">
                    <div class="preview" style="font-family: <?= $font['family'] ?>; font-size: <?= $font['size'] ?>px; font-weight: <?= $font['weight'] ?>; font-style: <?= $font['style'] ?>; color: #<?= $color['hex'] ?>; <?= $font['decoration'] != null ? 'text-decoration:' . $font['decoration'] .';' : '' ?>">
                    <?= $font['name'] ?>, <?= $font['family'] ?>, <?= $font['size'] ?>px
                    </div>
                </div>
                <div class="font-meta">
                    <div>Line Height: <strong><?= $font['line_height'] ?></strong></div>
                    <div>Weight: <strong><?= $font['weight'] ?></strong></div>
                    <div>Transform: <strong><?= $font['transform'] ?></strong></div>
                </div>
                <!-- NORMAL -->
                <div class="color">
                    <div class="state">Normal</div>
                    <div class="box" style="background: #<?= $color['hex'] ?>;<?= $color['hex'] == 'ffffff' ? 'border: 1px solid #666;' : '' ?>"></div>
                    <div class="meta meta-hex">#<?= strtoupper($color['hex']) ?></div>
                    <div class="meta meta-rgb"><?= $color['r'] ?>, <?= $color['g'] ?>, <?= $color['b'] ?></div>
                    <div class="meta meta-less">@<?= $color['name_css'] ?></div>
                    <div class="meta meta-name">
                    <? if ($color['name'] != '') { ?>
                    <?= $color['name'] ?><br />
                    <? } ?>
                    </div>
                </div>
                <!-- HOVER -->
                <? if ($colors[$font['color_hover']]) { ?>
                <? $color = $colors[$font['color_hover']]; ?>
                <div class="color">
                    <div class="state">Hover</div>
                    <div class="box" style="background: #<?= $color['hex'] ?>;<?= $color['hex'] == 'ffffff' ? 'border: 1px solid #666;' : '' ?>"></div>
                    <div class="meta meta-hex">#<?= strtoupper($color['hex']) ?></div>
                    <div class="meta meta-rgb"><?= $color['r'] ?>, <?= $color['g'] ?>, <?= $color['b'] ?></div>
                    <div class="meta meta-less">@<?= $color['name_css'] ?></div>
                    <div class="meta meta-name">
                    <? if ($color['name'] != '') { ?>
                    <?= $color['name'] ?><br />
                    <? } ?>
                    </div>
                </div>
                <? } ?>
                <!-- ACTIVE -->
                <? if ($colors[$font['color_active']]) { ?>
                <? $color = $colors[$font['color_active']]; ?>
                <div class="color">
                    <div class="state">Active</div>
                    <div class="box" style="background: #<?= $color['hex'] ?>;<?= $color['hex'] == 'ffffff' ? 'border: 1px solid #666;' : '' ?>"></div>
                    <div class="meta meta-hex">#<?= strtoupper($color['hex']) ?></div>
                    <div class="meta meta-rgb"><?= $color['r'] ?>, <?= $color['g'] ?>, <?= $color['b'] ?></div>
                    <div class="meta meta-less">@<?= $color['name_css'] ?></div>
                    <div class="meta meta-name">
                    <? if ($color['name'] != '') { ?>
                    <?= $color['name'] ?><br />
                    <? } ?>
                    </div>
                </div>
                <? } ?>
                <!-- VISITED -->
                <? if ($colors[$font['color_visited']]) { ?>
                <? $color = $colors[$font['color_visited']]; ?>
                <div class="color">
                    <div class="state">Visited</div>
                    <div class="box" style="background: #<?= $color['hex'] ?>;<?= $color['hex'] == 'ffffff' ? 'border: 1px solid #666;' : '' ?>"></div>
                    <div class="meta meta-hex">#<?= strtoupper($color['hex']) ?></div>
                    <div class="meta meta-rgb"><?= $color['r'] ?>, <?= $color['g'] ?>, <?= $color['b'] ?></div>
                    <div class="meta meta-less">@<?= $color['name_css'] ?></div>
                    <div class="meta meta-name">
                    <? if ($color['name'] != '') { ?>
                    <?= $color['name'] ?><br />
                    <? } ?>
                    </div>
                </div>
                <? } ?>
            </div>
            <? } ?>
        </div>
        <? } ?>
    </div>
    <? require 'partials/foot.php'; ?>
</body>
</html>