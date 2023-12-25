<html>
<head>
	<title>Modify Email Account - <?php echo $_SESSION['login'];?></title>
	<style type="text/css">
		* {
			FONT-SIZE: 8pt;
			FONT-FAMILY: verdana, arial, helvetica, sans-serif;
			line-height:16px;
		}
		.border {  border: 1px solid gray; }
		a {
			TEXT-DECORATION: none;
			COLOR: black;
		}

		a:hover {
			COLOR: #9999CC;
			TEXT-DECORATION: underline;
		}
	</style>

</head>
<body><center>
<table width="600" height=100% cellspacing=1 cellpadding=3>
<tr>
<td><a href="index.php">Home</a> - <b><a href="mailto:<?php echo $_SESSION['login'];?>"><?php echo $_SESSION['login'];?></a></b></td>
<td align=right><a href="logout.php">Logout</a><br></td>
<tr><td valign=top height=100% class=border colspan=2>
