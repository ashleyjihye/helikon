<?php
   
require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");

$page = $_SERVER['PHP_SELF'];
$dbh = db_connect($athomas2_dsn);

function getPerson($values, $dbh) {
   $sql = "select name from person where pid=?";
   $resultset = prepared_query($dbh, $sql, $values);
   $numRows = $resultset->numRows();
   if ($numRows != 0){
     $detailrow = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
     $name = $detailrow['name'];
     return $name;
   }
   else{
    return null;
   }
}

function showRecentPeople($dbh,$page){
  $sql = "select * from person order by pid desc limit 10";
  $resultset = query($dbh,$sql);
  echo "Most Recently Added People<br>";
  while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $pid = $row['pid'];
    $name = $row['name'];
     echo "<a href= \"" . $page . "?pid=" . $pid . "\">$name</a><br><br>";
  }
}

function getPersonContributions($values,$dbh,$page){
  $sql = "select mid,title,genre,type from media inner join contribution using (mid) where pid = ?";
  $resultset = prepared_query($dbh,$sql,$values);
  echo "Known For<ul>";
  $previoustype = "";
  while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $mid = $row['mid'];
    $title = $row['title'];
    $genre = $row['genre'];
    $type = $row['type'];
    if ($type != $previoustype){
      $captype = ucfirst($type);
      if ($type == "tv"){
      echo "$captype Shows<br>";
    }
      else{
        echo "" . $captype . "s<br>";
      }
      $previoustype = $type;
    }
    echo "<li><a href= \"media.php?mid=" . $mid . "\">$title ($genre)</a><br><br></li>";
  }
  echo "</ul>";
}

checkLogInStatus();   

if (isset($_REQUEST['pid'])){
  $pagepid = $_REQUEST['pid'];
  $name = getPerson($pagepid,$dbh);
  if ($name == null){
    header("Location: person.php");
    exit();
  }
  printPageTop("$name");
  createNavBar("home.php");
  echo "<h1>$name</h1>";
  getPersonContributions($pagepid,$dbh,$page);
}
else{
  printPageTop("People");
  createNavBar("home.php");
  echo "<h1>People</h1>";
  showRecentPeople($dbh,$page);
}

?>

</body>
</html>