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
if ($user_id != userid()) {
    die('Permission denied.');
}
$project_slug = addslashes($route[3]);
$project = $db->single("SELECT * FROM project WHERE slug = '" . $project_slug . "' AND creator = " . $user_id . " LIMIT 1");
$project_id = $project['id'];
?>
<!DOCTYPE html>
<html class="mod modLayout skinLayoutBrowser">
<head>
    <title><?= $project['name'] ?> - Clarify</title>
    <? require 'partials/head.php' ?>
</head>
<body>
    <div class="mod modBetaNotice">
        <? require TERRIFIC . 'modules/BetaNotice/betanotice.phtml'; ?>
    </div>
    <div class="mod modProjectBrowser" data-project="<?= $project_id ?>">
        <? require TERRIFIC . 'modules/ProjectBrowser/projectbrowser.phtml'; ?>
    </div>
    <div class="mod modScreenBrowser" data-project="<?= $project_id ?>">
        <? require TERRIFIC . 'modules/ScreenBrowser/screenbrowser.phtml'; ?>
    </div>
    <? require 'partials/foot.php'; ?>
</body>
</html>