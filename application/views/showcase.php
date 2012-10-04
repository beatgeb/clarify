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

$user_id = intval($route[2]);
$project_slug = addslashes($route[3]);
$project = $db->single("SELECT id, creator, slug, name, showcase_background_color FROM project WHERE slug = '" . $project_slug . "' AND creator = " . $user_id . " LIMIT 1");
if (!$project) { die(); }
$project_id = $project['id'];

permission($project_id, 'VIEW');

$owner = $db->single("SELECT id, email, name FROM user WHERE id = '" . $project['creator'] . "'");
$collaborators = $db->data("SELECT u.id, u.name, u.email FROM project_permission pp LEFT JOIN user u ON u.id = pp.user WHERE pp.project = '" . $project['id'] . "'");
$screens = $db->data("SELECT id, title, description, code FROM screen WHERE project = '" . $project['id'] . "' ORDER BY title ASC");
$colors = $db->data("SELECT id, hex, name, name_css, r, g, b, alpha FROM project_color WHERE project = '" . $project['id'] . "' ORDER BY hue ASC", "id");

// screen highlight
$highlight = current($screens);
?>
<!DOCTYPE html>
<html class="mod modLayout skinLayoutShowcase" style="background-color: <?php print $project['showcase_background_color'] ?>;">
<head>
    <title><?php print $project['name'] ?> Showcase - Clarify</title>
    <?php require 'partials/head.php' ?>
</head>
<body>
    <div class="mod modShowcase">
        <div class="mod modToolbar">
            <div class="section section-nav">
                <a class="button btn-screen" href="/project/<?php print $project['creator']; ?>/<?php print $project['slug']; ?>/"><i class="icon icon-arrow-left"></i><div class="tt">Back</div></a>
            </div>
        </div>
        <h1><?php print $project['name'] ?></h1>
        <div class="visual">
            <div class="highlights">
                <div class="highlight">
                    <img src="<?php print R ?>api/screen/thumbnail/<?php print $highlight['id'] ?>/600" width="600" alt="" />
                </div>
                <div class="highlight highlight-second">
                    <img src="<?php print R ?>api/screen/thumbnail/<?php print $highlight['id'] ?>/600" width="600" alt="" />
                </div>
                <a class="btn-previous" href="javascript:;">
                    <i class="icon icon-chevron-left"></i>
                </a>
                <a class="btn-next" href="javascript:;">
                    <i class="icon icon-chevron-right"></i>
                </a>
            </div>
            <?php if (sizeof($screens) > 1) { ?>
            <div class="screens">
                <?php foreach ($screens as $key => $screen) { ?>
                <?php if ($key == 0) { continue; } ?>
<<<<<<< HEAD
                <?php if ($key > 4) { break; } ?>
=======
                <?php if ($key > 6) { break; } ?>
>>>>>>> a1fbf92ddeeeec0749d906990607098f01d023cc
                <div class="screen screen-<?php print $key ?>">
                    <img src="<?php print R ?>api/screen/thumbnail/<?php print $screen['id'] ?>/<?php print round(500-$key*70) ?>" width="<?php print round(500-$key*70) ?>" alt="" />
                </div>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
        <!-- Feedback -->
        <div class="feedback">
            <a class="btn btn-like" href="#"><i class="icon icon-heart-empty"></i>&nbsp;&nbsp;Like</a>
        </div>
        <!-- Collaborators -->
        <ul class="collaborators">
            <li><img src="<?php print gravatar($owner['email'], 128) ?>" width="64" height="64" /></li>
            <?php foreach ($collaborators as $c) { ?>
            <li><img src="<?php print gravatar($c['email'], 128) ?>" width="64" height="64" /></li>
            <?php } ?>
        </ul>
        
        <div style="clear: both;"></div>
    </div>
    <?php require 'partials/foot.php'; ?>
</body>
</html>
