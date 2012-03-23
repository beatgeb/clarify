<?php
/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
//$code = $route[2];
//$user = $db->single("SELECT id, email FROM user WHERE code = '" . $code . "' LIMIT 1");
?>
<!DOCTYPE html>
<html class="mod modLayout skinLayoutRegister">
<head>
    <title>Register - Clarify</title>
    <? require 'partials/head.php'; ?>
</head>
<body>
    <div class="mod modRegister">
    <? require TERRIFIC . 'modules/Register/register.phtml' ?>
    </div>
    <? require 'partials/foot.php'; ?>
</body>
</html>	