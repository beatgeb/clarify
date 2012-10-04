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
$project = $db->single("SELECT id, creator, slug, name FROM project WHERE slug = '" . $project_slug . "' AND creator = " . $user_id . " LIMIT 1");
if (!$project) { die(); }
$project_id = $project['id'];

permission($project_id, 'VIEW');

$owner = $db->single("SELECT id, email, name FROM user WHERE id = '" . $project['creator'] . "'");
$collaborators = $db->data("SELECT u.id, u.name, u.email FROM project_permission pp LEFT JOIN user u ON u.id = pp.user WHERE pp.project = '" . $project['id'] . "'");
$screens = $db->data("SELECT id, title, description, code FROM screen WHERE project = '" . $project['id'] . "' ORDER BY title ASC");
$colors = $db->data("SELECT id, hex, name, name_css, r, g, b, alpha FROM project_color WHERE project = '" . $project['id'] . "' ORDER BY hue ASC", "id");
$comments = $db->data("SELECT d.id, d.content, d.nr, d.layer, d.screen FROM comment d LEFT JOIN screen s ON s.id = d.screen WHERE s.project = '" . $project['id'] . "'");
$fonts = $db->data("SELECT * FROM project_font WHERE project = " . $project_id);
$modules = $db->data("SELECT * FROM project_module WHERE project = " . $project_id);
$layers = array();
foreach ($comments as $comment) {
    $layers[$comment['screen']][] = $comment;
}

// screen highlight
$highlight = current($screens);

?>
<!DOCTYPE html>
<html class="mod modLayout skinLayoutShowcase" style="background-color: #3B5998;">
<head>
    <title>Styleguide for <?php print $project['name'] ?> - Clarify</title>
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
        <div class="highlight">
            <img src="<?php print R ?>api/screen/thumbnail/<?php print $highlight['id'] ?>/580" width="580" alt="" />
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
