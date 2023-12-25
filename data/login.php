<?php

$version = "1.8";

header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

session_name("setemail");
session_start();

include_once("include/config.php");
include_once("include/httpsocket.php");

if (isset($_POST['login']) && isset($_POST['password']))
{
	if (!is_email($_POST['login']))
	{
		showLogin("Please enter a valid email address");
		exit(0);
	}

	if (!is_pass($_POST['password']))
	{
		showLogin("Please enter a valid password");
		exit(0);
	}

        $_SESSION['login']    = $_POST['login'];
        $_SESSION['password'] = $_POST['password'];

	$email    = $_POST['login'];
        $pos      = strpos($email, "@");
        $domain   = substr($email, $pos+1);
        $user     = substr($email, 0, $pos);

        $pos                  = strpos($_SESSION['login'], '@');
        $_SESSION['domain']   = substr($_SESSION['login'], $pos+1);
        $_SESSION['user']     = substr($_SESSION['login'], 0, $pos);


        header("Location: ".$_SERVER["REQUEST_URI"]);
	exit(0); //this was added after... we'll do the login check on the next time round.
}

if (!isset($_SESSION['login']) || !is_email($_SESSION['login']) || !isset($_SESSION['password']) || !isset($_SESSION['domain']) || !isset($_SESSION['user']))
{
	showLogin("Please enter your login information");
        exit(0);
}

//if the data exists, they've already authenticated (we hope)
//but do this every time for index.php, cus it's good to see.
$names = explode("/", $_SERVER["SCRIPT_NAME"]);
$file = $names[count($names)-1];
if (!isset($_SESSION['inbox']) || !is_numeric($_SESSION['inbox']) || $file=="index.php")
{

	$sock = newSock();
	$sock->query('/CMD_EMAIL_ACCOUNT_QUOTA',
		array(
			'domain' => $_SESSION['domain'],
			'user' => $_SESSION['user'],
			'password' => $_SESSION['password'],
			'api' => '1',
			'quota' => 'yes'
		 ));

	$result = $sock->fetch_parsed_body();

	//this is for the no-header bug. Only needed for DA 1.31.1 and older.
	if (count($result) == 0)
	{
		parse_str($sock->fetch_result(), $result);
	}


	if ( $result['error'] != "0" )
	{
		showLogin("You have entered and invalid email or password<br>".$result['text']);
		exit(0);
	}

	//error=0&imap=20480&inbox=750&spam=0&total=21230&webmail=0
	$_SESSION['imap'] = $result['imap'];
	$_SESSION['inbox'] = $result['inbox'];
	$_SESSION['spam'] = $result['spam'];
	$_SESSION['total'] = $result['total'];
	$_SESSION['quota'] = $result['quota'];
	$_SESSION['webmail']=$result['webmail'];
}

function showLogin($message="")
{
?>
<html>
	<head>
		<title>E-Mail Account Login</title>
		<style>
			*{ FONT-SIZE: 8pt; FONT-FAMILY: verdana; } b { FONT-WEIGHT: bold; } .listtitle { BACKGROUND: #425984; COLOR: #EEEEEE; white-space: nowrap; } td.list { BACKGROUND: #EEEEEE; white-space: nowrap; } 
		</style>
	</head>
	<body onLoad="document.form.login.focus();">
	<center><br><br><br><br>
	<h1>Email Login Page</h1>
	<?php if ($message!="") { echo "<h1>$message</h1>\n"; } ?>
	<table cellspacing=1 cellpadding=5>
		<tr>
		<td class=listtitle colspan=2>Please enter your Username and Password</td></tr>
		<form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST" name="form">
		<tr><td class=list align=right>E-Mail:</td><td class=list><input type=text name=login></td></tr>
		<tr><td class=list align=right>Password:</td><td class=list><input type=password name=password></td></tr>
		<tr><td class=listtitle align=right colspan=2><input type=submit value='Login'></td></tr>
		</form>
	</table>
	</center></body></html>
<?php

}

function is_email($email)
{
        if (!preg_match('/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+(,\s?([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+)*$/', $email))
                return false;
        $len = strlen($email);
        if ($len<4 || $len > 60) return false;
        return true;
}

function is_pass($pass)
{
	return preg_match('/^([a-zA-Z0-9]|[~`!@#$%^&*(){}_+-=])+$/', $pass);
}

function newSock()
{
	global $host;
	global $port;
	global $ssl;

	$tsock = new HTTPSocket;
	$tsock->set_method('POST');
	#$tsock->set_login('username','password');
	$tsock->set_ssl_setting_message('DirectAdmin appears to be using SSL. Change your include/config.php and set $ssl=true;');

	if ($ssl)
	{
		$tsock->connect("ssl://$host", $port);
	}
	else
	{
		$tsock->connect("$host", $port);
	}

	return $tsock;
}

function readable_byte($bytes)
{
	if ($bytes<1024)
		return "$bytes Bytes";
	if ($bytes<1024*1024)
		return ($bytes/1024)." Kb";
	
	return ($bytes/(1024*1024))." Meg";
}

function no_go($msg)
{

        echo $msg;

        include("include/footer.php");
        exit(0);
}

