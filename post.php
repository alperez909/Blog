<?php
require("Token.php");
require("config.php");
// Add your posting code here.
// 
// To send a user to a different page (after possibly executing some code,
// you can use the statement:
//
//     header('Location: view.php');
//
// This will send the user tp view.php. To use this mechanism, the
// statement must be executed before any of the document is output.
session_start();
session_regenerate_id();

// Database Parameters
$host = 'localhost';
$port = '5432';
$dbname = 'chattr';
$dbuser = 'chattr';
$dbpassword = 'toomanysecrets';

// Connect to database
$dbconn = pg_connect("host=$host port=$port dbname=$dbname user=$dbuser password=$dbpassword") 
		  or die('Could not connect: ' . pg_last_error());

$session_user_id = $_SESSION["sess_user_id"];
$text = isset($_POST["TEXT"]) ? $_POST["TEXT"] : "";
$token = isset($_POST["token"]) ? $_POST["token"] : "" ;

if(isset($session_user_id) && Token::check($token)) {
	$result = pg_prepare("insert", "INSERT INTO comments(text,user_id) VALUES($1,$2)");
	$result = pg_execute("insert", array($text, $session_user_id) );
}

header('Location: view.php');
?>
