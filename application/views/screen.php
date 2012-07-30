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
        p.creator as project_creator
    FROM screen s 
        LEFT JOIN project p ON (p.id = s.project)
    WHERE 
        s.id = '" . $screen_id . "' AND s.creator = '" . userid() . "'
    LIMIT 1
");
if (!$screen) { die(); }
$screen['image'] = R . 'upload/screens/' . $screen['project'] . '/' . md5($screen['id'] . config('security.general.hash')) . '.' . $screen['ext'];
$colors = $db->data("SELECT id, hex, name FROM project_color WHERE project = '" . $screen['project'] . "' ORDER BY hue ASC");
$modules = $db->data("SELECT id, name FROM project_module WHERE project = '" . $screen['project'] . "' ORDER BY name ASC");

?>
<!DOCTYPE html>
<html class="mod modLayout skinLayoutScreen">
<head>
    <title><?= $screen['title'] ?> - Clarify</title>
    <? require 'partials/head.php'; ?>
</head>
<body>
    <div class="mod modScreen" 
         data-screen="<?= $screen['id'] ?>" 
         data-layer="1" 
         data-width="<?= $screen['width'] ?>"
         data-height="<?= $screen['height'] ?>"
         data-image="<?= $screen['image'] ?>">
        
        <div class="screen"></div>
        <div class="mod modLayerComment"></div>
        <div class="mod modLayerMeasure"></div>
        <div class="mod modLayerColor"></div>
        <div class="mod modLayerFont"></div>
        <div class="mod modLayerModule"></div>
    </div>
    <div class="mod modColorLibrary">
    <? require TERRIFIC . 'modules/ColorLibrary/colorlibrary.phtml' ?>
    </div>
    <div class="mod modModuleLibrary">
    <? require TERRIFIC . 'modules/ModuleLibrary/modulelibrary.phtml' ?>
    </div>
    <div class="mod modToolbar">
    <? require TERRIFIC . 'modules/Toolbar/toolbar.phtml' ?>
    </div>
    <div class="mod modEyedropper">
    <? require TERRIFIC . 'modules/Eyedropper/eyedropper.phtml' ?>
    </div>
    <? require 'partials/foot.php'; ?>
</body>
</html>