<?php

// Auth API
define('API_AUTH_AUTHENTICATE', 'auth.authenticate');
define('API_AUTH_PASSWORD', 'auth.password');

switch ($action) {

	case API_AUTH_PASSWORD:
		lock();
		$password = md5($_REQUEST['password'] . config('security.password.hash'));
		$db->update('user', array('password' => $password), array('id' => userid()));
		$result = array('success' => true);
		header('Content-Type: application/json');
		echo json_encode($result);
		break;

	case API_AUTH_AUTHENTICATE:

		$result = array('success' => false);

		// ldap authentication
		if (config('auth.ldap.enabled')) {
			$username = $_REQUEST['username'];
			$password = $_REQUEST['password'];
			$ldap = ldap_connect(config('auth.ldap.server'), config('auth.ldap.server.port')) or die("Can't connect to LDAP server");
			$user = "uid=" . $username . "," . config('auth.ldap.userbase');
			ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
			if (ldap_bind($ldap, config('auth.ldap.server.username'), config('auth.ldap.server.password'))) {
				$attributes = array(
					'dn','uid',
					config('auth.ldap.user.attribute.email'),
					config('auth.ldap.user.attribute.firstname'),
					config('auth.ldap.user.attribute.surname')
				);
				$r = ldap_search($ldap, config('auth.ldap.user.basedn'), 'uid=' . $username, $attributes);
				if ($r) {
		            $result = @ldap_get_entries($ldap, $r);
		            if ($result[0]) {
		                if (@ldap_bind($ldap, $result[0]['dn'], $password)) {
		                    $entry = $result[0];
		                    $user = $db->single("SELECT id, name, email FROM user WHERE username = '" . $username . "' LIMIT 1");
							if (!$user) {
								$name = $entry[config('auth.ldap.user.attribute.firstname')][0] . ' ' . $entry[config('auth.ldap.user.attribute.surname')][0];
								$user = array(
									'creator' => 1,
									'created' => gmdate('Y-m-d H:i:s'),
									'username' => $username,
									'name' => $name,
									'email' => $entry[config('auth.ldap.user.attribute.email')][0]
								);
								$id = $db->insert('user', $user);
								$user['id'] = $id;
							}
							$_SESSION['user']['id'] = $user['id'];
					        $_SESSION['user']['name'] = $user['name'];
					        $_SESSION['user']['email'] = $user['email'];
					        $_SESSION['auth'] = md5(config('security.password.hash') . $_SESSION['user']['id']);
							$result['success'] = true;
		                }
		            }
		        }
				ldap_unbind($ldap);
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
				$user = $db->single("SELECT id, name, email FROM user WHERE email = '" . $email . "' AND password = '" . $password . "' LIMIT 1");
				if ($user) {
					$_SESSION['user']['id'] = $user['id'];
			        $_SESSION['user']['name'] = $user['name'];
			        $_SESSION['user']['email'] = $user['email'];
			        $_SESSION['auth'] = md5(config('security.password.hash') . $_SESSION['user']['id']);
					$result['success'] = true;
				}
			}

		}
		header('Content-Type: application/json');
		echo json_encode($result);
		break;

}
