<?php
require_once("login.php");

session_unset();
session_destroy();

header("Location: index.php");
exit(0);
?>
