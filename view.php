<?php
	require("Token.php");
	require("config.php");
	session_start();
	session_regenerate_id();
?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 3.2//EN">
<HEAD>
    <TITLE>Chattr</TITLE>
</HEAD>
<BODY BGCOLOR=WHITE>
<TABLE ALIGN="CENTER">
<TR><TD>
<H1>Chattr</H1>
</TD></TR>

<?php
	// The following <TR> element should only appear if the user is
	// logged in and viewing his own entry.
	// Database Parameters
	$host = 'localhost';
	$port = '5432';
	$dbname = 'chattr';
	$dbuser = 'chattr';
	$dbpassword = 'toomanysecrets';

	// Connect to database
	$dbconn = pg_connect("host=$host port=$port dbname=$dbname user=$dbuser password=$dbpassword") 
			  or die('Could not connect: ' . pg_last_error());

	$session_username = isset($_SESSION['sess_username']) ? $_SESSION['sess_username'] : '';
	$user_in_session = false;
	$result = null;

	if(!isset($_SESSION['sess_user_id']) ||
           (trim($_SESSION['sess_user_id']) == '')) {
		// No session user exists
		$user_in_session = false;
		// No user session exists and not searching any user
		if($_SERVER['QUERY_STRING'] == "")
			header('Location: index.php');
	} else if($_SERVER['QUERY_STRING'] == "user=$session_username" || 
                  $_SERVER['QUERY_STRING'] == "") {
		// Session exists and are viewing current session entry
		$user_in_session = true;
	} else {
		$user_in_sesssion = false;
	}

	if ($user_in_session) : ?>
	<TR><TD>
    <FORM ACTION="post.php" METHOD="POST">
    <TABLE CELLPADDING=5>
    <TR><TD>Message:</TD>
    	<TD><INPUT TYPE="TEXT" NAME="TEXT">
    		<INPUT TYPE="SUBMIT" VALUE="Submit">
    		<INPUT type="hidden" name="token" value="<?php echo Token::generate(); ?>">
    	</TD>
    </TR>
    </TABLE>
    </FORM>
    </TD></TR>  
	<?php endif; ?>
<?php
	// The following <TR> element should always appear if the user
	// exists.
	$search = $_SERVER['QUERY_STRING'];
	$user_tag = substr($search,0,5);
	$rest = substr($search,5);
	$user_exists = false;
	
	if ($session_username) 
		$session_exists = true; 
	else 
		$session_exists = false;
		
	// check correct query format
	if($user_tag == "user=" || $user_tag =="") {
		$result = pg_prepare("user", "SELECT * FROM users
			 					      WHERE username = $1");
		if($user_tag == "" && $session_exists) {
			// user has no query string
			$result = pg_execute("user",array($session_username));
		} else {
			// user has query string
			$result = pg_execute("user", array($rest));
		}
		$num = pg_num_rows($result);
		if ($num > 0) {
			// success found user
			$user_exists = true;
		} else {
	    	// did not find user
			$user_exists = false;
		}
	} else {
		// incorrect query string
		$user_exists = false;
		$rest = "";
	}
?>
<?php if ($user_exists) : ?>
    <TR><TD>
    <TABLE CELLPADDING=5>
    <TR><TH>When</TH><TH>Who</TH><TH>What</TH></TR>
<?php
		// Display user's posts here. The structure is:
		// Get all comments if user exists
		if ($user_exists) {
			$user_data = pg_fetch_row($result);
			$comment_user_id = $user_data[0];
			$comment_username = $user_data[1];
			$result = pg_prepare("comments", "SELECT * FROM comments
											  WHERE user_id = $1");
			$result = pg_execute("comments", array($comment_user_id));
			$num = pg_num_rows($result);
			
			//List all comments		
			for($i=0; $i < $num; $i++) {
				$comment_data = pg_fetch_row($result); 
				?>
				<TR>
				<TD> <?php echo htmlentities($comment_data[2],ENT_COMPAT,'UTF-8'); ?> </TD>
				<TD> <?php echo htmlentities($comment_username,ENT_COMPAT,'UTF-8'); ?> </TD>
				<TD> <?php echo htmlentities($comment_data[1],ENT_COMPAT,'UTF-8'); ?> </TD>
				</TR>
				<?php
			}
			
		}
    ?>
    </TABLE>
    </TD></TR>
<?php endif; 

	// The following <TR> element should be displayed if the user
	// name does not exist. Add code to display user name.
	$user_not_found = !$user_exists;

	if ($user_not_found) : ?>
    <TR><TD>
    <H2>User <?php echo htmlentities($rest,ENT_COMPAT,'UTF-8'); ?> does not exist!</H2>
    </TD></TR>
	<?php endif; 

	// The following <TR> element should only be shown if the user
	// is logged in.
	// Check if there is a session
	if ($session_username) 
		$session_exists = true; 
	else 
		$session_exists = false;


	if ($session_exists) : 
		$query_string = '?token=' . urlencode($_SESSION['token']);
?>
<TR><TD><A HREF=<?php echo "logout.php".htmlentities($query_string,ENT_COMPAT,'UTF-8') ?> >Logout</A></TR></TD>
<?php endif; 
	// Done!
	//Free resultset
	if(isset($result))
		pg_free_result($result);

	// Closing connection
	pg_close($dbconn);
?>
</TABLE>
</BODY>

