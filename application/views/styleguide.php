<?php

lock();

$project_id = intval($route[2]);

// check project permissions
permission($project_id, 'VIEW');

// load project metadata
$project = $db->single("SELECT id, creator, slug, name FROM project WHERE id = '" . $project_id . "'");
if (!$project) { die(); }

// load screens
$screens = $db->data("SELECT id, title, description, code FROM screen WHERE project = '" . $project['id'] . "' ORDER BY title ASC");

// load colors
$colors = $db->data("SELECT id, hex, name, name_css, r, g, b, alpha FROM project_color WHERE project = '" . $project_id . "' ORDER BY hue ASC", "id");

// load fonts 
$fonts = $db->data("SELECT pf.* FROM project_font pf LEFT JOIN font f ON f.font = pf.id WHERE pf.project = " . $project_id . " AND f.font IS NOT NULL");

// load modules
$modules = $db->data("SELECT * FROM project_module WHERE project = " . $project_id);

// sample text
$sample = "ABCDEFGHIJKLMNOPQRSTUVWXYZ\nabcdefghijklmn@0123456789%&äöü";

?>
<!DOCTYPE html>
<html class="mod modLayout">
<head>
    <title>Styleguide for <?php print $project['name'] ?> - Clarify</title>
    <?php require 'partials/head.php' ?>
</head>
<body>
    <?php require TERRIFIC . 'modules/Styleguide/styleguide.phtml' ?>
    <?php require 'partials/foot.php'; ?>
</body>
</html>