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
$project = $db->single("SELECT * FROM project WHERE id = " . $project_id . " AND creator = " . userid() . " LIMIT 1");
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