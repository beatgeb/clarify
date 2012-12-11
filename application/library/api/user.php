<?php

// User API
define('API_USER_CREATE', 'user.create');
define('API_USER_SETTING', 'user.setting');

switch ($action) {

	case API_USER_SETTING:
		lock();
        $setting = $route[4];
        switch ($setting) {
            case 'name':
                $value = $route[5];
                $db->update('user', array($setting => urldecode($value)), array('id' => userid()));
                $_SESSION['user'][$setting] = urldecode($value);
                break;
        }
        break;

	case API_USER_CREATE:
		$name = $_REQUEST['name'];
		$email = $_REQUEST['email'];
		$password = md5($_REQUEST['password'] . config('security.password.hash'));
		$result = array('success' => false);

		// validate email)
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$user = $db->single("SELECT id FROM user WHERE invitation_code = '" . $_SESSION['user']['invitation_code'] . "' LIMIT 1");
			if ($user) {
				// create account
				$update = array(
					'email' => $email,
					'password' => $password,
					'invitation_code' => null,
					'name' => $name
				);
				$db->update('user', $update, array('id' => $user['id']));

				// login
				$_SESSION['user']['id'] = $user['id'];
		        $_SESSION['user']['name'] = $name;
		        $_SESSION['auth'] = md5(config('security.password.hash') . $_SESSION['user']['id']);
				$result['success'] = true;
			}
		}

		header('Content-Type: application/json');
		echo json_encode($result);
		break;

}
