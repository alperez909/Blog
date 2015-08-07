<?php
require('config.php');
require('Token.php');

// Your logout code goes here.
session_start();
//session_regenerate_id();

$session_user_id = $_SESSION["sess_user_id"];
$token = isset($_GET['token']) ? $_GET['token'] : "";

if(isset($session_user_id) && Token::check($token)) {
	unset($_SESSION['sess_user_id']);
	unset($_SESSION['sess_username']);

	if(ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(),'',time()-3600,
		$params["path"],
		$params["domain"],
		$params["secure"],
		$params["httponly"]);
	}

	session_destroy();
	header('Location: index.php');
} else {
	header('Location: view.php');
}
?>
