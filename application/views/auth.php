<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

$tmhOAuth = new tmhOAuth(array(
  'consumer_key' => config('twitter.auth.consumerkey'),
  'consumer_secret' => config('twitter.auth.consumersecret'),
));

if (isset($_REQUEST['start'])) :
  auth_request_token($tmhOAuth);
elseif (isset($_REQUEST['oauth_verifier'])) :
  auth_access_token($tmhOAuth);
elseif (isset($_REQUEST['verify'])) :
  auth_verify_credentials($tmhOAuth);
elseif (isset($_REQUEST['wipe'])) :
  auth_wipe();
endif;

// save referer
if (isset($_REQUEST['referer']) && substr($_REQUEST['referer'],0,1) == '/') {
    $_SESSION['referer'] = $_REQUEST['referer'];
}

if (authenticated()) {
    if (isset($_SESSION['referer'])) {
        $referer = $_SESSION['referer'];
        unset($_SESSION['referer']);
        header('Location: ' . $referer);
        exit();
    } else {
        header('Location: ' . R);
        exit();
    }
}

?>
<!DOCTYPE html>
<html class="mod modLayout skinLayoutAuth">
<head>
    <title>Sign In - Clarify</title>
    <?php require 'partials/head.php' ?>
</head>
<body>
    <div class="mod modAuth">
        <?php require TERRIFIC . 'modules/Auth/auth.phtml'; ?>
    </div>
    <?php require 'partials/foot.php'; ?>
</body>
</html>
