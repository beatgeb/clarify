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

?>
<!DOCTYPE html>
<html class="mod modLayout skinLayoutBrowser">
<head>
    <title>Projects - Clarify</title>
    <?php require 'partials/head.php' ?>
</head>
<body>
    <div class="mod modProjectBrowser">
        <?php require TERRIFIC . 'modules/ProjectBrowser/projectbrowser.phtml'; ?>
    </div>
    <div class="mod modScreenBrowser" data-project="<?php print $project_id ?>">
        <?php require TERRIFIC . 'modules/ScreenBrowser/screenbrowser.phtml'; ?>
    </div>
    <?php require 'partials/foot.php'; ?>
</body>
</html>
