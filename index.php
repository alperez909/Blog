<?php
	session_start();
	session_regenerate_id();
	require('Token.php');
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
				<FORM ACTION="login.php" METHOD="POST">
					<TABLE CELLPADDING=5>
						<TR>
							<TD>User name:</TD>
							<TD><INPUT TYPE="text" NAME="user"></TD>
						</TR>
						<TR>
							<TD>Password:</TD>
							<TD><INPUT TYPE="password" NAME="pass"></TD>
						</TR>
						<TR>
							<TD COLSPAN=2>
								<INPUT type="checkbox" name="new" value="yes">&nbsp;New user&nbsp;
								<INPUT type="submit" value="Submit">
								<INPUT type="hidden" name="token" value="<?php echo Token::generate(); ?>">
							</TD>
						</TR>
					</TABLE>
				</FORM>
			</TD>
		</TR>
	</TABLE>
</BODY>
