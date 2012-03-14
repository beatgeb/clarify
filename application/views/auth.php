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
  'consumer_key'    => config('twitter.auth.consumerkey'),
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

echo '<pre>';
print_r($_SESSION);
echo '</pre>';

?>

<p>
<?php if (isset($_SESSION['access_token'])) : ?>
  There appears to be some credentials already stored in this browser session.
  Do you want to <a href="?verify=1">verify the credentials?</a> or
  <a href="?wipe=1">wipe them and start again</a>.
<?php else : ?>
  <a href="?start=1">Authorize with OAuth</a>.
<?php endif; ?>
</p>