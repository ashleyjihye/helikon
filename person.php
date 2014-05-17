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
  $previoustype = "";
  $mediacontributions = array();
  $counter = 0;
  $numRows = $resultset->numRows();
  if ($numRows == 0){
    echo "Not known for anything.";
    return;
  }
  echo "Known For<ul>";
  while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $mid = $row['mid'];
    $title = $row['title'];
    $genre = $row['genre'];
    $type = $row['type'];
    $mediacontributions[$counter] = array('title' => $title, 'mid' => $mid);
    $counter++;
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
  return array('numRows'=>$numRows,'mediacontributions'=>$mediacontributions);
}

function editPersonPage($pagepid, $name){
  global $page, $dbh;
  $contributionarray = getPersonContributions($pagepid, $dbh, $page);
  $mediacontributions = $contributionarray['mediacontributions'];
  echo '<form method="post" action="' . $page . '">
  <input type="hidden" name="pid" value="' . $pagepid . '">
  <input type="hidden" name="edited">

  Name: <input type="text" name="name" value="' . $name . '"><br>';

  echo '<br>Delete Contributions: <br>';
  $counter = 0;

  if ($mediacontributions != null){
    foreach ($mediacontributions as $key => $value) {
      echo $value['title'] . ' <input type="checkbox" name="media' . $counter . '" value="' . $value['mid'] . '"><br>';
          $counter++;
    }
  }

  echo '<br>Add Contributions: <br>';
  for ($counter = 0; $counter < 5; $counter++){
    echo '<input type="text" name="newmedia' . $counter . '">
    <select name="newmediatype' . $counter . '" id="newmediatype">
      <option selected="selected" value="">None
      <option value="movie">Movie
      <option value="tv">TV
      </select><br>';
  }

  echo '<br><input type="submit" value="Make Changes"></form>';
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

  if (isset($_REQUEST['edited'])){
    $name = $_REQUEST['name'];
    echo '<div id="results" style="display: none;">';
    $contributionarray = getPersonContributions($pagepid, $dbh, $page);
    echo '</div>';
    $numContributions = $contributionarray['numRows'];
    $counter = 0;
    $deletecontributionarray = array();
    for ($i=0; $i < $numContributions; $i++) { 
      if (isset($_REQUEST['media' . $counter])){
       $deletecontributionarray[$counter] = $_REQUEST['media' . $counter];
     }
      $counter++;
    }

    $addcontributionarray = array();
    $addcontributiontypearray = array();
    for ($counter = 0; $counter < 5; $counter++){
      if (isset($_REQUEST['newmedia' . $counter]) and $_REQUEST['newmedia' . $counter] != ""){
        $addcontributionarray[$counter] = $_REQUEST['newmedia' . $counter];
        $addcontributiontypearray[$counter] = $_REQUEST['newmediatype' . $counter];
      }
      else {
        break;
      }
    }

    editPerson($pagepid, $name, $deletecontributionarray, $addcontributionarray, $addcontributiontypearray);
    $name = getPerson($pagepid,$dbh);
  }



  if (isset($_REQUEST['edit'])){
    echo '<h1>' . $name . '</h1>';
    editPersonPage($pagepid, $name);
  }

  else{
    echo '<h1>' . $name . ' <button onclick="location.href=\'' . $page . '?pid=' . $pagepid . '&edit\'">
     edit</button></h1>';
    getPersonContributions($pagepid,$dbh,$page);
  }
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