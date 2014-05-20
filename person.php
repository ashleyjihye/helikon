<!-- Ashley Thomas and Sasha Levy
  Helikon
  person.php
  5/19/14

This file shows a person page (actor or singer). It also allows the user to edit a person page.
 -->

 <?php
   
require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");

$page = $_SERVER['PHP_SELF'];
$dbh = db_connect($athomas2_dsn);
?>


<style>
#description {
display:inline-block;
}

#contributionheader{
  margin-left:300px;
}
</style>

<?php

//get a person's name, description, and picture based on their pid
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

//show people most recently added (not actually a link to it, only used if user manually changes name of link)
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

//get a person's contributions and prints them out
function getPersonContributions($values,$dbh,$page){
  $sql = "select mid,title,genre,type from media inner join contribution using (mid) where pid = ? order by type desc, title";
  $resultset = prepared_query($dbh,$sql,$values);
  $previoustype = "";
  $mediacontributions = array();
  $counter = 0;
  $numRows = $resultset->numRows();
  if ($numRows == 0){
    echo "Not known for anything.";
    return;
  }
  echo "<h3>Known For</h3><ul>";
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
        if ($counter != 1){
          echo "</ul>";
        }
      echo "<h4>$captype Shows</h4><ul><br>";
    }
      else{
        if ($counter != 1){
          echo "</ul>";
        }
        echo "<h4>" . $captype . "s</h4><ul><br>";
      }
      $previoustype = $type;
    }
    echo "<li><a href= \"media.php?mid=" . $mid . "\">$title ($genre)</a><br><br></li>";
  }
  echo "</ul>";
  return array('numRows'=>$numRows,'mediacontributions'=>$mediacontributions);
}

//edit person form
function editPersonPage($pagepid, $name, $description){
  global $page, $dbh;
  echo "<div id='hidden' style='display: none;'>";
  $contributionarray = getPersonContributions($pagepid, $dbh, $page);
  echo "</div>";
  $mediacontributions = $contributionarray['mediacontributions'];
  echo '<form style="width:1000px;" class="form-horizontal" method="post" action="' . $page . '" enctype="multipart/form-data">
<div class="form-group">
  <input type="hidden" name="pid" value="' . $pagepid . '">
  <input type="hidden" name="edited">
<label for="uploadPic" class="col-sm-2 control-label">Upload Picture</label>
<div class="col-sm-10">

  <input type="file" class="form-control" name="imagefile" size="50">
</div>
<label for="name" class="col-sm-2 control-label">Name</label>
<div class="col-sm-10">

   <input type="text" name="name" class="form-control" value="' . $name . '"></div>
<label for="description" class="col-sm-2 control-label">Description</label>
<div class="col-sm-10">

  <textarea rows="4" cols="50" class="form-control" name="description">' . $description . '</textarea></div>';


  $counter = 0;

  if ($mediacontributions != null){
  echo '<label for="deletecontribution" class="col-sm-2 control-label">Delete Contributions</label><div class="col-sm-10">';
    foreach ($mediacontributions as $key => $value) {
      echo '<h4>' . $value['title'] . '<p><input type="checkbox" class="form-control" name="media' . $counter . '" value="' . $value['mid'] . '"></h4><br>';
          $counter++;
    }
    echo "</div>";
  }

  echo '<p><label for="addcontribution" class="col-sm-2 control-label">Add Contributions</label><div class="col-sm-10"><table id="mediaTable" style="width:60%;" class="table">
  <tr><th>Media</th><th>Type</th></tr>';

  for ($counter = 1; $counter <= 5; $counter++){
    
    echo '<tr><td><input type="text" style= "width: 80%;" class="form-control"name="newmedia' . $counter . '"></td><td>
    <select style="width:100px;" class="form-control" name="newmediatype' . $counter . '" id="newmediatype>
      <option selected="selected" value="">None
      <option value="movie">Movie
      <option value="tv">TV
      </select></td></tr>';
  }

  echo '</table></div>
<label for="addRowMedia" class="col-sm-2 control-label"></label>
<div class="col-10-sm">
  <button class="btn btn-default" id="add_row_media">Add Row</button><br><br>
</div><label for="submitting" class="col-sm-2 control-label"></label><div class="col-10-sm">';
echo '<input type="submit" value="Make Changes" class="btn btn-default"></div></div></form>';
}

//get a person picture
function processPicture($pid, $dbh){

  $destfile = "";

  //user uploaded new image
  if (isset($_FILES['imagefile']) and $_FILES["imagefile"]["error"] == 0){
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

  else { //check database
    $destfile = getPersonPicture($pid,$dbh);
  }

  return $destfile;
}

// ----------------------   main   ---------------------------
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

  ?>

  <!--This handles getting extra rows for the editing table -->
<script>
$(document).ready(function (){
  $("#add_row_media").click(function (e) {
    console.log("It got here!");
    e.preventDefault();
    var nextCount = $("#mediaTable tr").length;
    $("#mediaTable tr:last").after('<tr><td><input type="text" style= "width: 80%;" class="form-control"name="newmedia' + nextCount + '"></td><td><select style="width:100px;" class="form-control" name="newmediatype' + nextCount + '" id="newmediatype><option selected="selected" value="">None<option value="movie">Movie<option value="tv">TV</select></td></tr>');
  })
});
</script>

<?php

  if (isset($_REQUEST['edited'])){ //user submitted form
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
    $counter = 1;
    while (isset($_REQUEST['newmedia' . $counter]) and htmlspecialchars($_REQUEST['newmedia' . $counter]) != ""){
      $addcontributionarray[$counter] = htmlspecialchars($_REQUEST['newmedia' . $counter]);
      $addcontributiontypearray[$counter] = htmlspecialchars($_REQUEST['newmediatype' . $counter]);
      $counter++;
    }

    editPerson($pagepid, $name, $description, $deletecontributionarray, $addcontributionarray, $addcontributiontypearray);
    $personarray = getPerson($pagepid,$dbh);
    $name = $personarray['name'];
    $description = $personarray['description'];
  }

  if (isset($_REQUEST['edit'])){ //user wants to have form shown
    echo '<h1>' . $name . '</h1>';
    editPersonPage($pagepid, $name,$description);
  }

  else{ //normal page
    $picture = processPicture($pagepid,$dbh);
    echo "<h1>" . $name . '  <button class="btn btn-primary btn-large" onclick="location.href=\'' . $page . '?pid=' . $pagepid . '&edit\'">edit</button></h1>';
    echo "<p style='float:left; display:inline-block; margin: 20px; padding:20px;'>";
    if ($picture != ""){
      echo "<img width=200 height=200 src='$picture'><p><br><br>\n";
    }
    if ($description != null and $description != ""){     
      echo "<div id='description'>Description: $description</div><br><br>";
    }
    echo "<div id='contributionheader'>";
    getPersonContributions($pagepid,$dbh,$page);
    echo "</div>";
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