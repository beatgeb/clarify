<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

$screen_id = intval($route[2]);
$screen = $db->single("SELECT id, title, width, height, project, ext FROM screen WHERE id = '" . $screen_id . "' LIMIT 1");
$screen['image'] = R . 'upload/screens/' . $screen['project'] . '/' . $screen['id'] . '.' . $screen['ext'];
$colors = $db->data("SELECT id, hex FROM project_color WHERE project = '" . $screen['project'] . "'");

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
    </div>
    <div class="mod modColorLibrary">
    <? require TERRIFIC . 'modules/ColorLibrary/colorlibrary.phtml' ?>
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