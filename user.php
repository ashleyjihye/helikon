<!doctype html>
<html lang='en'>
<head>
<meta charset='utf-8'>
<meta name=author content="Ashley Thomas and Sasha Levy">
<title> \'s homepage</title>
</head>
<body>

   <?php
   
require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");

$dbh = db_connect($athomas2_dsn);
   $values = $_REQUEST['uid'];

function getUser($values, $dbh) {
   $sql = "select name, uid from user where uid=?";

   
   $resultset = prepared_query($dbh, $sql, $values);
   $detailrow = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
   $name = $detailrow['name'];
   $uid = $detailrow['uid'];
   
   echo "<h2>Welcome to $name's page</h2>";
}

function getLikes($values, $dbh) {
     $sql = "select title, type, likes.dateadded from user inner join likes using (uid) inner join media using (mid) where user.uid=? order by likes.dateadded desc limit 10";
     $resultset = prepared_query($dbh, $sql, $values);
     
     while($detailrow = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
   
     $title = $detailrow['title'];
     $type = $detailrow['type'];
 
     echo "<ol><li>$title ($type) </li></ol>";
     }
}
     

getUser($values, $dbh);
getLikes($values, $dbh);
    
     

   ?>

</body>


</html>