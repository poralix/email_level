<?php
require_once("login.php");

$startstamp=time();
$endstamp=time();
$text="";


$time_of_day = array( "morning" => "Morning", "afternoon" => "Afternoon", "evening" => "Evening");
$months = array();
for ($i=1; $i<=12; $i++)
{
	$months[$i] = date("M", mktime(0,0,0,$i,1,0));
}
$days = array();
for ($i=1; $i<=31; $i++)
{
	$days[$i] = $i;
}

$start_year = 2007;
$end_year = 2029;
$years = array();
for ($i=$start_year; $i<=$end_year; $i++)
{
        $years[$i] = $i;
}

$custom_reply_headers = 0;
$reply_subject='';
$reply_charset = '';
$reply_encodings='';
$reply_content_types='';
$reply_once_select='';


if (isset($_POST['action']) && ($_POST['action'] == "create" || $_POST['action'] == "modify" || $_POST['action'] == "delete"))
{
	include("include/header.php");

	$post = array(
				'user' => $_SESSION['user'],
				'domain'=>$_SESSION['domain'],
				'password' => $_SESSION['password'],
				'action' => $_POST['action'],
				'text' => stripslashes($_POST['text']),
				'starttime' => $_POST['starttime'],
				'startmonth' => $_POST['startmonth'],
				'startday' => $_POST['startday'],
				'startyear' => $_POST['startyear'],
				'endtime' => $_POST['endtime'],
				'endmonth' => $_POST['endmonth'],
				'endday' => $_POST['endday'],
				'endyear' => $_POST['endyear']
			);
	if (isset($_POST['custom_reply_headers']) && $_POST['custom_reply_headers']==1)
	{
		$post['subject'] = $_POST['subject'];
		$post['reply_encoding'] = $_POST['reply_encoding'];
		$post['reply_content_type'] = $_POST['reply_content_type'];
		$post['reply_once_time'] = $_POST['reply_once_time'];
	}


	$sock = newSock();
	$sock->query('/CMD_EMAIL_ACCOUNT_VACATION', $post);

	$result = $sock->fetch_parsed_body();

	if ( $result['error'] != "0" )
	{
        	no_go("Unable to set vacation message:<br><b>".$result['text']."</b>");
	}

	echo "Vacation message ";
	switch ($_POST['action'])
	{	case "create" : echo "created"; break;
		case "modify" : echo "updated"; break;
		case "delete" : echo "deleted"; break;
	}
	echo ".<br><br><a href='index.php'>Click here</a> to go back.";

	include("include/footer.php");
	exit(0);
}

//grab any vacation message if there is one.
$sock1 = newSock();
$sock1->query('/CMD_EMAIL_ACCOUNT_VACATION',
        array(
                'user' => $_SESSION['user'],
                'domain'=>$_SESSION['domain'],
                'password' => $_SESSION['password'],
        ));

$result1 = $sock1->fetch_parsed_body();

$exists=0;

if ($result1 == 0)
{
        no_go("socket retuned a zero result");
}

/*
echo "<textarea cols=120 rows=20>";
echo $sock1->fetch_body();
echo "\n\n";
//print_r($result1);
echo "</textarea><br>\n";
*/

if ( $result1['error'] == "0" )
{
        if (!isset($result1['startyear']))
        {
        		include("include/header.php");
                no_go("socket returned no error, but there is data missing.  Try reloading this page.");
        }

        $startstamp=getTimeFromVars($result1['startyear'], $result1['startmonth'], $result1['startday'], $result1['starttime']);
        $endstamp=getTimeFromVars($result1['endyear'], $result1['endmonth'], $result1['endday'], $result1['endtime']);
        $text=$result1['text'];
        $exists=1;

		load_header_variables($result1);
}
else
{
        //echo, I guess it doesn't exist yet.
        load_header_variables($result1);
}

include("include/header.php");

?>

<br>
<h1>Set Vacation Messsage</h1>

<table cellpadding=3 cellspacing=1>
<form action="?" method="POST">
<input type=hidden name="action" value="<?php if ($exists) { echo "modify";}else{echo "create";}?>">

<?php
	if ($custom_reply_headers)
	{
		echo "<input type=hidden name=custom_reply_headers value=1>\n";
		echo "<tr><td>Sujbect Prefix</td><td class=list><input type=text name=subject value='$reply_subject' size=12>: <i>orignial subject</i></td></tr>\n";
		echo "<tr><td>Reply Encoding</td><td class=list>$reply_encodings (browser: $reply_charset)</td></tr>\n";
		if (!$exists)
			echo "<tr><td></td><td class=list>Be sure the browser encoding is set correctly before you enter your message.  Set and save first, to change it.</td></tr>\n";
		echo "<tr><td>Content-Type</td><td class=list>$reply_content_types</td></tr>\n";
		echo "<tr><td>Reply Frequency</td><td class=list>$reply_once_select Minimum time before a repeated reply</td></tr>\n";
	}
?>

<tr><td>Vacation Message:</td><td class=list align=center><textarea rows=15 cols=60 name=text><?php echo $text;?></textarea></td></tr>
<tr><td>Vacation Start: </td><td><?php showTime("start", $startstamp); ?></td></tr>
<tr><td>Vacation End: </td><td><?php showTime("end", $endstamp); ?></td></tr>
<tr><td>Current Server Time:</td><td><?php echo get_tod(time())." of ".date("M j, Y"); ?></td></tr>
<tr><td colspan=2 align=center><input type=submit value="<?php if($exists){echo "Update";}else{echo "Set";}?> Vacation Message"></td></tr>

</form>
</table>

<?php if ($exists) { ?>
<br><br>
<form action="?" method="POST">
<input type=hidden name="action" value="delete">
<input type=submit value="Delete current Vacation Message">
</form>
<?php
}


include("include/footer.php");

function show_select($name, $arr, $selected='')
{

	echo "<select name='$name'>";
	foreach($arr as $v => $t)
	{
		if ($v == $selected)
		{
			echo "\t<option selected value='$v'>$t</option>\n";
		}
		else
		{
			echo "\t<option value='$v'>$t</option>\n";
		}
	}
	echo "</select>\n";

}

function get_tod($stamp)
{
	$hour = (int)date("H", $stamp);

	if (0 <= $hour && $hour < 12) { return "morning"; }
	if (12 <= $hour && $hour < 18) { return "afternoon"; }
	return "evening";
}

function getTimeFromVars($year, $month, $day, $tod)
{
	switch($tod)
	{
		case "morning" : $hour = 6; break;
		case "afternoon" : $hour = 12; break;
		case "evening" : $hour = 18; break;
		default : $hour = 0; break;
	}

	return mktime($hour, 0, 0, $month, $day, $year);
}

function showTime($prefix, $stamp=0)
{
	global $time_of_day, $months, $days, $years;

	show_select("${prefix}time", $time_of_day, get_tod($stamp));

	show_select("${prefix}month", $months, date("m", $stamp));

	show_select("${prefix}day", $days, date("j", $stamp));

	show_select("${prefix}year", $years, date("Y", $stamp));


}

function load_header_variables($result1)
{
	global $custom_reply_headers, $reply_subject, $reply_charset, $reply_encodings, $reply_content_types, $reply_once_select;

	if (!isset($result1['custom_reply_headers'])) return;
	if ($result1['custom_reply_headers'] != '1') return;

	$custom_reply_headers = 1;

	$reply_subject=$result1['reply_subject'];
	$reply_charset=$result1['reply_charset'];
	$reply_encodings=$result1['reply_encodings'];
	$reply_content_types=$result1['reply_content_types'];
	$reply_once_select=$result1['reply_once_select'];

	if ($reply_charset == '')
		$reply_charset = 'iso-8859-1';

	header("Content-Type: text/html; charset=$reply_charset");
}

?>
