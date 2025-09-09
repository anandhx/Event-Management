<?php
session_start();

// Only proceed if someone is logged in
if (isset($_SESSION['user_id'])) {
	// Destroy session
	$_SESSION = [];
	if (ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	}
	session_destroy();
}

// Redirect to login
header('Location: ../login.php');
exit();
