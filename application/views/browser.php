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
    <? require 'partials/head.php' ?>
</head>
<body>
    <div class="mod modProjectBrowser">
        <? require TERRIFIC . 'modules/ProjectBrowser/projectbrowser.phtml'; ?>
    </div>
    <? require 'partials/foot.php'; ?>
</body>
</html>