<?php
require_once("login.php");


include("include/header.php");


if (isset($_POST['action']) && $_POST['action'] == "change")
{
	//oldpassword
	//password1
	//password2

	if (!is_pass($_POST['oldpassword']))	{ no_go("Old password is not syntactically correct."); }
	if ($_POST['oldpassword'] != $_SESSION['password']) { no_go("Old password is not correct."); }
	if (!is_pass($_POST['password1']))     { no_go("New password is not syntactically correct."); }
	if (!is_pass($_POST['password2']))     { no_go("New password2 is not syntactically correct."); }
	if ($_POST['password1'] != $_POST['password2']) { no_go("New passwords do not match."); }



	$sock = newSock();
	$sock->query('/CMD_CHANGE_EMAIL_PASSWORD',
        array(
                'email' => $_SESSION['login'],
                'oldpassword' => $_POST['oldpassword'],
		'password1'   => $_POST['password1'],
		'password2'   => $_POST['password2'],
                'api'	      => '1',
         ));

	$result = $sock->fetch_parsed_body();

	if ( $result['error'] != "0" )
	{
        	no_go("Unable to change password:<br>".$result['text']);
	}

	
	echo "Password changed.<br><br><a href='index.php'>Click here</a> to go back.";

	//since we don't want to get booted.
	$_SESSION['password'] = $_POST['password1'];


	include("include/footer.php");
	exit(0);
}




?>

<br>
<h1>Change Password</h1>

		<table cellpadding=5 cellspacing=1>
		<form action="?" method="POST">
		<input type=hidden name="action" value="change">
		<tr><td class=listtitle colspan=2>Enter the required information below</td></tr>
		<tr><td class=list align=right>Old Password:</td><td class=list><input type=password name=oldpassword size=32></td></tr>
		<tr><td class=list align=right>New Password:</td><td class=list><input type=password name=password1 size=32></td></tr>
		<tr><td class=list align=right>Re-Type Password:</td><td class=list><input type=password name=password2 size=32></td></tr>
		<tr><td class=listtitle colspan=2 align=right><input type=submit value="Change Password"></td></tr>
		</form>
		</table>

<?php
include("include/footer.php");

?>
