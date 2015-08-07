<?php
require("password_hash.php");
require("config.php");
require("Token.php");

/*ob_start();*/
session_start();
//session_regenerate_id();


/*$randomtoken = md5(uniqid(rand(), true));
$_SESSION['csrfToken']=$randomtoken;*/

// The login.php is invoked when the user is either trying to create a new
// account or to login. If it's the former, the NEW parameter will be set.
// To send a user to a different page (after possibly executing some code,
// you can use the statement:
//
//     header('Location: view.php');
//
// This will send the user tp view.php. To use this mechanism, the
// statement must be executed before any of the document is output.

// Database Parameters
$host = 'localhost';
$port = '5432';
$dbname = 'chattr';
$dbuser = 'chattr';
$dbpassword = 'toomanysecrets';

// Error
$error ='';

// Login form
$user =  isset($_POST['user']) ? $_POST['user'] : '';
$password = isset($_POST['pass']) ? $_POST['pass'] : '';
$token =  isset($_POST['token']) ? $_POST['token'] : '';
$isNew = isset($_POST['new']);

// Query results
$result = null;
$num = null;
$user_data = null;

// Connect to database
$dbconn = pg_connect("host=$host port=$port dbname=$dbname user=$dbuser password=$dbpassword") 
		  or die('Could not connect: ' . pg_last_error());

// crfr check
if(Token::check($token)) {		
	// If new user checkbox is set
	if($isNew) {
		// Query database to check if username already exists
		$result = pg_prepare('user', "SELECT username FROM users WHERE 
				              username = $1");
		$result = pg_execute('user', array($user));
		$num = pg_num_rows($result);

		// Username is unique. Insert into database.
		if($num <= 0) {
			$result = pg_prepare('insert', 'INSERT INTO
	                              users(username,password) 
					     		  VALUES($1, $2)');	
			$result = pg_execute('insert',
			               array($user,create_hash($password)));

			// Log in new user and redirect to view.php 
			if(!$result) {
				$error = 'Invalid username or password';
			} else {
				$result = pg_prepare('all', 'SELECT * FROM users 
							    	WHERE username = $1');
				$result = pg_execute('all', array($user));
				$user_data = pg_fetch_row($result);

		
				$_SESSION['sess_user_id'] = $user_data[0];	// REMOVE TBD
				$_SESSION['sess_username'] = $user_data[1];
				session_write_close();
				header('Location: view.php');	
			}
		// User already exists error
		} else {
			$row = pg_fetch_row($result);
			$error = "User $row[0] already exists!";
		}
	// Try to login in user with an account
	} else {
		$result = pg_prepare('pass', 'SELECT * FROM users
					     	 WHERE username = $1');
		$result = pg_execute('pass', array($user));
		$num = pg_num_rows($result);

		// Username does not exist
		if ($num <= 0) {
			$error = 'Login Failed!';
		// User successfully logged in
		} else {
			$user_data = pg_fetch_row($result);

			if(validate_password($password,$user_data[2])){
				$_SESSION['sess_user_id'] = $user_data[0]; // REMOVE TBD
				$_SESSION['sess_username'] = $user_data[1]; 
				session_write_close();
				header('Location: view.php');
			// Passwrod incorrect
			} else {
				$error = 'Login Failed!';
			}
		}
	}
	//Free resultset
	pg_free_result($result);
} else {
	$error = "Login Failed!";
}

// Closing connection
pg_close($dbconn);
?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 3.2//EN">
<HEAD>
    <TITLE>Chattr</TITLE>
</HEAD>
<BODY BGCOLOR=WHITE>
	<TABLE ALIGN="CENTER">
		<TR>
			<TD>
				<H1>Chattr</H1>
			</TD>
		</TR>
		<TR>
			<TD>
				<H2><?php echo htmlentities($error,ENT_COMPAT,'UTF-8'); ?></H2>
				<a href="index.php">Back</a>
			</TD>
		</TR>
	</TABLE>
</BODY>
