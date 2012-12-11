<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

require_once LIBRARY . 'thirdparty/tmhoauth/tmhOAuth.php';
require_once LIBRARY . 'thirdparty/tmhoauth/tmhUtilities.php';

function auth_wipe() {
    session_destroy();
    header('Location: ' . R . 'auth');
}

function auth_outputError($tmhOAuth) {
  echo 'There was an error: ' . $tmhOAuth->response['response'] . PHP_EOL;
}

// Step 1: Request a temporary token
function auth_request_token($tmhOAuth) {
    $code = $tmhOAuth->request(
        'POST', 
        $tmhOAuth->url('oauth/request_token', ''), 
        array('oauth_callback' => tmhUtilities::php_self())
    );

    if ($code == 200) {
        $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
        auth_authorize($tmhOAuth);
    } else {
        auth_outputError($tmhOAuth);
    }
}

// Step 2: Direct the user to the authorize web page
function auth_authorize($tmhOAuth) {
    $authurl = $tmhOAuth->url("oauth/authorize", '') . "?oauth_token={$_SESSION['oauth']['oauth_token']}";
    header("Location: {$authurl}");
}

// Step 3: This is the code that runs when Twitter redirects the user to the callback. Exchange the temporary token for a permanent access token
function auth_access_token($tmhOAuth) {
    $tmhOAuth->config['user_token'] = $_SESSION['oauth']['oauth_token'];
    $tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

    $code = $tmhOAuth->request(
        'POST', 
        $tmhOAuth->url('oauth/access_token', ''), 
        array('oauth_verifier' => $_REQUEST['oauth_verifier'])
    );

    if ($code == 200) {
        global $db;
        $_SESSION['access_token'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
        $userdata = json_decode(file_get_contents('https://api.twitter.com/1/users/show.json?screen_name=' . $_SESSION['access_token']['screen_name']));
        
        // user does not exist, check invitation code and create it
        if (!$db->exists('user', array('twitter_user_id' => $_SESSION['access_token']['user_id']))) {
            $user = $db->single("SELECT id, email, name FROM user WHERE invitation_code = '" . intval($_SESSION['user']['invitation_code']) . "' LIMIT 1");
            if (!$user) {
                die('invitation code invalid');
            }
            $id = $user['id'];
            $user = array(
                'created' => date('Y-m-d H:i:s'),
                'creator' => 1,
                'username' => $_SESSION['access_token']['screen_name'],
                'twitter_user_id' => $_SESSION['access_token']['user_id'],
                'twitter_screen_name' => $_SESSION['access_token']['screen_name'],
                'twitter_oauth_token' => $_SESSION['access_token']['oauth_token'],
                'twitter_oauth_secret' => $_SESSION['access_token']['oauth_token_secret'],
                'name' => $userdata->name,
                'invitation_code' => null
            );
            $db->update('user', $user, array('id' => $id));
        } else {
            $user = array(
                'twitter_oauth_token' => $_SESSION['access_token']['oauth_token'],
                'twitter_oauth_secret' => $_SESSION['access_token']['oauth_token_secret'],
                'name' => $userdata->name
            );
            $db->update('user', $user, array('twitter_user_id' => $_SESSION['access_token']['user_id']));
            $user = $db->single("SELECT id, name, email FROM user WHERE twitter_user_id = '" . $_SESSION['access_token']['user_id'] . "' LIMIT 1");
            $id = $user['id'];
        }

        $_SESSION['user']['twitter'] = $userdata;
        $_SESSION['user']['id'] = $id;
        $_SESSION['user']['name'] = $user['name'];
        $_SESSION['user']['email'] = $user['email'];
        $_SESSION['auth'] = md5(config('security.password.hash') . $_SESSION['user']['id']);
        unset($_SESSION['oauth']);
        header('Location: ' . tmhUtilities::php_self());
    } else {
        auth_outputError($tmhOAuth);
    }
}

// Step 4: Now the user has authenticated, do something with the permanent token and secret we received
function auth_verify_credentials($tmhOAuth) {
    $tmhOAuth->config['user_token'] = $_SESSION['access_token']['oauth_token'];
    $tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

    $code = $tmhOAuth->request(
        'GET', 
        $tmhOAuth->url('1/account/verify_credentials')
    );

    if ($code == 200) {
        $resp = json_decode($tmhOAuth->response['response']);
        echo '<h1>Hello ' . $resp->screen_name . '</h1>';
        echo '<p>The access level of this token is: ' . $tmhOAuth->response['headers']['x_access_level'] . '</p>';
    } else {
        auth_outputError($tmhOAuth);
    }
}