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

$screen_id = intval($route[2]);
$screen = $db->single("
    SELECT 
        s.id, 
        s.title, 
        s.width, 
        s.height, 
        s.project, 
        s.ext, 
        s.code,
        s.embeddable,
        p.slug as project_slug, 
        p.creator as project_creator,
        s.count_comment,
        s.count_measure,
        s.count_color,
        s.count_module,
        s.count_font
    FROM screen s 
        LEFT JOIN project p ON (p.id = s.project)
    WHERE 
        s.id = '" . $screen_id . "'
    LIMIT 1
");
if (!$screen) { die(); }

// check for view permissions
permission($screen['project'], 'VIEW');

$screen['image'] = R . 'upload/screens/' . $screen['project'] . '/' . md5($screen['id'] . config('security.general.hash')) . '.' . $screen['ext'];
$colors = $db->data("SELECT id, hex, name FROM project_color WHERE project = '" . $screen['project'] . "' ORDER BY hue ASC");
$modules = $db->data("SELECT id, name FROM project_module WHERE project = '" . $screen['project'] . "' ORDER BY name ASC");

?>
<!DOCTYPE html>
<html class="mod modLayout skinLayoutScreen">
<head>
    <title><?php print $screen['title'] ?> - Clarify</title>
    <?php require 'partials/head.php'; ?>
</head>
<body>
    <div class="ajax"><div class="message">Loading...</div><div class="overlay"></div></div>
    <div class="mod modScreen" 
         data-screen="<?php print $screen['id'] ?>" 
         data-layer="1" 
         data-width="<?php print $screen['width'] ?>"
         data-height="<?php print $screen['height'] ?>"
         data-image="<?php print $screen['image'] ?>">
        
        <div class="screen"></div>
        <div class="mod modLayerComment"></div>
        <div class="mod modLayerMeasure"></div>
        <div class="mod modLayerColor"></div>
        <div class="mod modLayerTypography"></div>
        <div class="mod modLayerModule"></div>
    </div>
    <?php require TERRIFIC . 'modules/Sidebar/sidebar.phtml' ?>
    <div class="mod modModuleLibrary">
    <?php require TERRIFIC . 'modules/ModuleLibrary/modulelibrary.phtml' ?>
    </div>
    <div class="mod modToolbar">
    <?php require TERRIFIC . 'modules/Toolbar/toolbar.phtml' ?>
    </div>
    <div class="mod modEyedropper">
    <?php require TERRIFIC . 'modules/Eyedropper/eyedropper.phtml' ?>
    </div>
    <?php require TERRIFIC . 'modules/LayerTypography/layertypography.phtml' ?>
    <?php require 'partials/foot.php'; ?>
</body>
</html>
