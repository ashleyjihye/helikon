<?php

/*Ashley Thomas and Sasha Levy
  Helikon
  check2.php
  5/19/14

This file helps handle the dynamic checking of the username that user has entered
when trying to sign in to see if it already exists in the database
*/
require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");

$dbh = db_connect($athomas2_dsn);

$username = trim(strtolower($_POST['username']));
$username = mysql_escape_string($username);

$sql = "SELECT username FROM user WHERE username = '$username' LIMIT 1";
$result = query($dbh, $sql);
$num = $result->numRows();

echo $num;
?>