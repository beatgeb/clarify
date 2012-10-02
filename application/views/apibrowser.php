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

if (!config('api.testing.enabled')) {
	exit('API Testing not enabled.');
}

?>
<!DOCTYPE html>
<html class="mod modLayout skinLayoutBrowser">
<head>
    <title><?php print $project['name'] ?> - Clarify</title>
    <?php require 'partials/head.php' ?>
</head>
<body>
    <div class="mod modApiBrowser">
        <?php require TERRIFIC . 'modules/ApiBrowser/apibrowser.phtml'; ?>
    </div>
    <?php require 'partials/foot.php'; ?>
</body>
</html>