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

if (authenticated()) {
    header('Location: ' . R);
}

?>
<!DOCTYPE html>
<html class="mod modLayout skinLayoutAuth">
<head>
    <title>Sign Up - Clarify</title>
    <? require 'partials/head.php' ?>
</head>
<body>
    <div class="mod modAuth">
        <? require TERRIFIC . 'modules/Auth/auth.phtml'; ?>
    </div>
    <? require 'partials/foot.php'; ?>
</body>
</html>