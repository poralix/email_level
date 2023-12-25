<?php
require_once("login.php");
include("include/header.php");
?>
<table width=100%><tr valign=top align=center><td>
		<h1>Current Usage</h1>
		<?php echo readable_byte($_SESSION['total']); ?> / <?php if ($_SESSION['quota'] == 0) { echo "unlimited"; }else{ echo readable_byte($_SESSION['quota']); } ?>
</td>
<td>
<h2>Breakdown</h2>
<table cellspacing=1 cellpadding=3>
<tr>
	<td align=right>Inbox:</td>
	<td><?php echo readable_byte($_SESSION['inbox']);?></td>
</tr>
<tr>
        <td align=right>Imap:</td>
        <td><?php echo readable_byte($_SESSION['imap']);?></td>
</tr>
<tr>
        <td align=right>Webmail:</td>
        <td><?php echo readable_byte($_SESSION['webmail']);?></td>
</tr>
<tr>
        <td align=right>Spam:</td>
        <td><?php echo readable_byte($_SESSION['spam']);?></td>
</tr>
<tr>
        <td align=right>Inbox:</td>
        <td><?php readable_byte($_SESSION['inbox']);?></td>
</tr>
</table>
</td></tr>
</table>

<hr>
<h1>Change Password</h1>
<a href="change_pass.php">Click here to change your password</a>
<br><br>
<hr>
<h1>Vacation Messages</h1>
<a href="vacation.php">Click here to set a vacation message</a>
<br><br>
<hr>
<h1>Webmail</h1>
<table width=100%><tr>

<?php showLinkIfExists("/roundcube", "Roundcube", "roundcube.gif", $show_roundcube); ?>
<?php showLinkIfExists("/squirrelmail", "SquirrelMail", "squirrel.gif", $show_squirrelmail); ?>
<?php showLinkIfExists("/webmail", "Uebimiau", "webmail.gif", $show_uebimiau); ?>
</tr></table>
<br>
<hr>













<?php
include("include/footer.php");

function showLinkIfExists($link, $name, $img, $show_it)
{
	if (!$show_it) { return; }

	//I was considering adding checks for the presence of the http://host/$link through apache
	//but I think that will be too slow of an operation.
	//it's easier to just add/remove the options above.

	$prot="http://";

	$url = $prot.$_SERVER["HTTP_HOST"].$link;

	echo "<td align=center>";

	echo "<a alt=\"$name\"href=\"$url\"><img alt='$name' src=\"images/$img\" border='0'>";
	echo "<br>$name</a>";
	echo "</td>";

}

?>
