<?php

// Auth API
define('API_AUTH_AUTHENTICATE', 'auth.authenticate');

switch ($action) {

	case API_AUTH_AUTHENTICATE:

		$result = array('success' => false);

		// ldap authentication
		if (config('auth.ldap.enabled')) {
			$username = $_REQUEST['username'];
			$password = $_REQUEST['password'];
			$ldap = ldap_connect(config('auth.ldap.server'), config('auth.ldap.server.port')) or die("Can't connect to LDAP server");
			$user = "uid=" . $username . "," . config('auth.ldap.userbase');
			ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
			if (ldap_bind($ldap, $user, $password)) {
				ldap_unbind($ldap);
				// login is valid, check for user
				$user = $db->single("SELECT id, name FROM user WHERE username = '" . $username . "' LIMIT 1");
				if (!$user) {
					$user = array(
						'creator' => 1,
						'created' => gmdate('Y-m-d H:i:s'),
						'username' => $username,
						'name' => $username
					);
					$id = $db->insert('user', $user);
					$user['id'] = $id;
				}
				$_SESSION['user']['id'] = $user['id'];
		        $_SESSION['user']['name'] = $user['name'];
		        $_SESSION['auth'] = md5(config('security.password.hash') . $_SESSION['user']['id']);
				$result['success'] = true;
			} else {
				$result['message'] = ldap_error($ldap) . ' (' . ldap_errno($ldap) . ')';
			}
			ldap_close($ldap);
		} else {

			// default authentication
			$email = $_REQUEST['email'];
			$password = md5($_REQUEST['password'] . config('security.password.hash'));
			
			// validate authentication (only with valid email)
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$user = $db->single("SELECT id, name FROM user WHERE email = '" . $email . "' AND password = '" . $password . "' LIMIT 1");
				if ($user) {
					$_SESSION['user']['id'] = $user['id'];
			        $_SESSION['user']['name'] = $user['name'];
			        $_SESSION['auth'] = md5(config('security.password.hash') . $_SESSION['user']['id']);
					$result['success'] = true;
				}
			}

		}
		header('Content-Type: application/json');
		echo json_encode($result);
		break;

}
