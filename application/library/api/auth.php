<?php

// Auth API
define('API_AUTH_AUTHENTICATE', 'auth.authenticate');

switch ($action) {

	case API_AUTH_AUTHENTICATE:
		$email = $_REQUEST['email'];
		$password = md5($_REQUEST['password'] . config('security.password.hash'));
		$result = array('success' => false);

		// validate authentication (only with valid email)
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$user = $db->single("SELECT id, name FROM user WHERE email = '" . $email . "' AND password = '" . $password . "' LIMIT 1");
			if ($user) {
				$_SESSION['user']['id'] = $user['id'];
		        $_SESSION['user']['name'] = $user['name'];
		        $_SESSION['auth'] = md5(config('security.password.hash') . $_SESSION['user']['id']);
				$result = array('success' => true);
			}
		}

		header('Content-Type: application/json');
		echo json_encode($result);
		break;

}
