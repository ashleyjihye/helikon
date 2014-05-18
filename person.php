<?php
   
require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");

$page = $_SERVER['PHP_SELF'];
$dbh = db_connect($athomas2_dsn);

function getPerson($values, $dbh) {
   $sql = "select * from person where pid=?";
   $resultset = prepared_query($dbh, $sql, $values);
   $numRows = $resultset->numRows();
   if ($numRows != 0){
     $detailrow = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
     $name = $detailrow['name'];
     $description = $detailrow['description'];
     $picture = $detailrow['picture'];
     return array('name'=>$name, 'description'=>$description, 'picture' =>$picture);
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

function editPersonPage($pagepid, $name, $description){
  global $page, $dbh;
  echo "<div id='hidden' style='display: none;'>";
  $contributionarray = getPersonContributions($pagepid, $dbh, $page);
  echo "</div>";
  $mediacontributions = $contributionarray['mediacontributions'];
  echo '<form method="post" action="' . $page . '" enctype="multipart/form-data">
  <input type="hidden" name="pid" value="' . $pagepid . '">
  <input type="hidden" name="edited">
  <p>Upload Picture: <input type="file" name="imagefile" size="50"><p>

  Name: <input type="text" name="name" value="' . $name . '"><br>
  Description: <textarea rows="4" cols="50" name="description">' . $description . '</textarea><br>';

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

function processPicture($pid, $dbh){

  $destfile = "";

  if (isset($_FILES['imagefile'])){
    if( $_FILES['imagefile']['error'] != UPLOAD_ERR_OK ) {
        print "<P>Upload error: " . $_FILES['imagefile']['error'];
    } 
    else {

      // image was successfully uploaded.  
      $name = $_FILES['imagefile']['name'];
      $type = $_FILES['imagefile']['type'];
      $tmp  = $_FILES['imagefile']['tmp_name'];

      $destdir = "personimages/";
      $destfilename = "$pid.jpg";
      $destfile = $destdir . $destfilename;

      $sql = "UPDATE person SET picture = 'y' WHERE pid = ?";

      if(move_uploaded_file($tmp, $destfile)) {
        prepared_statement($dbh,$sql,$pid);
      } 
      else {
        print "<p>Error moving $tmp\n";
      }
    }
  }

  else {
    $destfile = getPersonPicture($pid,$dbh);
  }

  return $destfile;
}


checkLogInStatus();   

if (isset($_REQUEST['pid'])){
  $pagepid = htmlspecialchars($_REQUEST['pid']);
  $personarray = getPerson($pagepid,$dbh);
  $name = $personarray['name'];
  $description = $personarray['description'];
  if ($name == null){
    header("Location: person.php");
    exit();
  }

  printPageTop("$name");
  createNavBar("home.php");

  if (isset($_REQUEST['edited'])){
    $name = htmlspecialchars($_REQUEST['name']);
    $description = htmlspecialchars($_REQUEST['description']);
    echo '<div id="results" style="display: none;">';
    $contributionarray = getPersonContributions($pagepid, $dbh, $page);
    echo '</div>';
    $numContributions = $contributionarray['numRows'];
    $counter = 0;
    $deletecontributionarray = array();
    for ($i=0; $i < $numContributions; $i++) { 
      if (isset($_REQUEST['media' . $counter])){
       $deletecontributionarray[$counter] = htmlspecialchars($_REQUEST['media' . $counter]);
     }
      $counter++;
    }

    $addcontributionarray = array();
    $addcontributiontypearray = array();
    for ($counter = 0; $counter < 5; $counter++){
      if (isset($_REQUEST['newmedia' . $counter]) and htmlspecialchars($_REQUEST['newmedia' . $counter]) != ""){
        $addcontributionarray[$counter] = htmlspecialchars($_REQUEST['newmedia' . $counter]);
        $addcontributiontypearray[$counter] = htmlspecialchars($_REQUEST['newmediatype' . $counter]);
      }
      else {
        break;
      }
    }

    editPerson($pagepid, $name, $description, $deletecontributionarray, $addcontributionarray, $addcontributiontypearray);
    $personarray = getPerson($pagepid,$dbh);
    $name = $personarray['name'];
    $description = $personarray['description'];
  }



  if (isset($_REQUEST['edit'])){
    echo '<h1>' . $name . '</h1>';
    editPersonPage($pagepid, $name,$description);
  }

  else{
    $picture = processPicture($pagepid,$dbh);
    echo '<h1>' . $name . ' <button onclick="location.href=\'' . $page . '?pid=' . $pagepid . '&edit\'">
     edit</button></h1>';
    if ($picture != ""){
      echo "<p><img width=200 height=200 src='$picture'><p>\n";
    }
     echo "Description: $description<br><br>";
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