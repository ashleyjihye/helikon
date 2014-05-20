<!-- Ashley Thomas and Sasha Levy
  Helikon
  header.php
  5/19/14

This file includes the functions regarding sessions and logging in/out, as well
as all the functions the other files use. We wanted to store them all in one file
because many other files use the same functions
 -->

 <?php

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");

$dbh = db_connect($athomas2_dsn);

//logs someone in by checking the username and password pair and then setting a session key
function logIn() {
  global $dbh;
  if(isset($_POST['loginusername'])) {
    $username = htmlspecialchars($_POST['loginusername']);
    if( loginCredentialsAreOkay($dbh,$username,htmlspecialchars($_POST['loginpassword'])) ) {
      session_start();
      $_SESSION['username'] = $username;
      $_SESSION['loggedin'] = true;
      header('Location: home.php');
    }
    else {
      echo "<p>Sorry, that's incorrect.  Please try again\n";
    }
  }
}

//log out a user
function logOut() {
  session_start();
  $_SESSION['loggedin'] = false;
  header('Location: index.php');
}

//sign in a user
function signIn(){
  global $dbh;
  if (isset($_REQUEST['username'])) {
    $name = htmlspecialchars($_REQUEST['name']);
    $username = htmlspecialchars($_REQUEST['username']);
    $email = htmlspecialchars($_REQUEST['email']);
    $sql = "select * from user where username = ?";
    $resultset = prepared_query($dbh,$sql,$username);
    $numRows = $resultset->numRows();
    if ($numRows == 1){ //someone already has the username the user wanted
      echo "Sorry, that username has already been taken. Pleae choose another.";
      exit();
    }
    else if (htmlspecialchars($_REQUEST['password']) == htmlspecialchars($_REQUEST['password1'])){ //everything checks out, so add them to database
      $values = array($name,$username,htmlspecialchars($_REQUEST['password']),$email,);
      $sql = "insert into user (name,username,password,email) values (?,?,?,?)";
      $resultset = prepared_query($dbh,$sql,$values);
    }
    else { //passwords didn't match
      echo "Your passwords did not match. Please try again.";
      exit();
    }
  }
}

//see if user is logged in or logged out, and reroute them accordingly
function checkLogInStatus() {
  session_start();
  if (isset($_SESSION['loggedin']) and $_SESSION['loggedin'] == true){
 //   echo $_SERVER['PHP_SELF'];
    if (basename($_SERVER['PHP_SELF']) == "index.php"){
      header('Location: home.php');
    }
    return true;
  }
  else {
    if (basename($_SERVER['PHP_SELF']) == "index.php"){
      return false;
    }
    header('Location: index.php');
    exit();
  }
}

//see if the username and password pair match
function loginCredentialsAreOkay($dbh,$username,$password) {
    $check = "SELECT count(*) AS n FROM user WHERE username=? AND password=?";
    $resultset = prepared_query($dbh, $check, array($username,$password));
    $row = $resultset->fetchRow();
    return( $row[0] == 1 );
}

//print the top of the page depending on the actual page you're on
function printPageTop($title) {
    print <<<EOT
<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>$title</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="raty-2.5.2/lib/jquery.raty.js"></script>
<script src="raty-2.5.2/lib/jquery.raty.min.js"></script>
</head>
<body>

EOT;
  }

//create the navbar to be displayed on all pages
function createNavBar($page) {

echo '<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
     <a class="brand" href="' . $page . '"><img src="images/logofinal.png"></a> 
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="media.php">Media</a></li>
      </ul>

      <form method="post" action="' . $page . '" class="navbar-nav navbar-form" role="search">
      <div class="form-group">
        <select class="form-control" name="tables">
        <option value="All">All</option>
        <option value="Users">Users</option>
        <option value="People">People</option>
        <option value="Movies">Movies</option>
      <option value="Albums">Albums</option>
      <option value="Songs">Songs</option>
      <option value="TVShows">TV Shows</option>
      </select>

          <input type="text" class="form-control" name="sought" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Account<b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="user.php">My Profile</a></li>
            <li><a href="adddata.php">Add to Database</a></li>
            <li class="divider"></li>
            <li><a href="' . $page . '?logout">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>';
}

//creates stars with rating of a piece of media (used specifically on media pages)
function createActualRating($rating, $numReviews){
  echo "Current Rating: <div class='star' id='star1'></div>
  <script>$('#star1').raty({ score: " . $rating . ", readOnly: true});</script>
   (based on " . $numReviews . " reviews)<br>";
}

//create a second set of stars that user can directly manipulate. results of user actions will be
//dynamically sent to database using ajax, and page will be updated to reflect the user's new rating
function createYourRating($mid, $yourRating, $uid){
  echo 'Your Rating: <div class="star" id="star2"></div>
  <script>
    $("#star2").raty({ score: ' . strval($yourRating) . ',
    click: function(score, evt) {
      data = "mid=' . $mid . '&uid=' . $uid . '&rating=" + score;
      $.ajax({
        type: "POST",
        url: "ratings.php",
        data: data,
        success: function(data){
          console.log(data);
          $("#currentRating").html(data);
        },
        error: function(jqXHR, textStatus, errorThrown){
          alert(textStatus);
        }
      });
    }
    });
  </script>';
}

//get the number of ratings this media has
function getNumRatings($dbh,$mid){
  $sql = "select count(uid) as count from ratings where mid = ?";
  $values = array($mid);
  $resultset = prepared_query($dbh,$sql,$values);
  $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
  return $row['count'];
}

//get uid given username
function getUid($dbh, $username) {
  $sql = "select uid from user where username = ?";
  $resultset = prepared_query($dbh, $sql, $username);
  $detailrow = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
  $uid = $detailrow['uid'];
  return $uid;
}

//add a person to the database (used when user has submitted a form)
function addPerson($dbh, $name, $uid, $description, $findperson, $person){
  $values = array($name);
  $getperson = prepared_query($dbh,$findperson,$values);
  $numrows = $getperson->numRows();
  if ($numrows != 0){ //person already exists in database
    echo "<p>There's already someone named " . $name .".<br>";
    while($row = $getperson->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $personid = $row['pid'];
    }
  }
  else {
    $addperson = prepared_query($dbh,$person,array($name,$uid,$description));
    echo "<p>Successfully added " . $name . " to the database.<br>";
    $getpersonid = query($dbh,"select last_insert_id()");
    while($row = $getpersonid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $personid = $row['last_insert_id()'];
    }
  }
  return $personid;
}

//find a piece of media in database given specific information and queries
function findMedia($dbh, $mediatitle, $mediatype, $findmedia, $findmediawithtype){
  if ($mediatype == ""){ //user didn't specify a type
    $checkmedia = prepared_query($dbh,$findmedia,array($mediatitle));
  }
  else{ //user specified a type, so narrow down the query
    $checkmedia = prepared_query($dbh,$findmediawithtype,array($mediatitle,$mediatype));
  }
  $numrows = $checkmedia->numRows();
  if ($numrows == 0){
    echo "Sorry, the media \"" . $mediatitle . "\" doesn't exist. Please add the media separately first.<br>";
    return null;
  }
  else if ($numrows != 1){
    echo "Sorry, searching for \"" . $mediatitle . "\" returned more than one result. Please enter more information about the piece of media.<br>";
    return null;
  }
  else {
    $row = $checkmedia->fetchRow(MDB2_FETCHMODE_ASSOC);
    $mediaid = $row['mid'];
    $genre = $row['genre'];
    return $mediaid;
  }
}

//the following three functions do similar things, but since they are
//slightly different, I decided to make them different functions
//instead of having a large amount of parameters

//get the user's profile picture given their uid
function getUserPicture($uid, $dbh){
  $sql = "select picture from user where uid = ?";
  $resultset = prepared_query($dbh,$sql,$uid);
  $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
  if ($row['picture'] == 'y'){
    $destfile = "userimages/$uid.jpg";
  }
  else{ //generic picture
    $destfile = "userimages/0.jpg";
  }
  return $destfile;
}

//get a person's picture given their pid
function getPersonPicture($pid,$dbh){
  $sql = "select picture from person where pid = ?";
  $resultset = prepared_query($dbh,$sql,$pid);
  $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
  if ($row['picture'] == 'y'){
    $destfile = "personimages/$pid.jpg";
  }
  else{
    $destfile = "personimages/0.jpg";
  }
  return $destfile;
}

//get a piece of media's picture given their mid
function getMediaPicture($mid,$dbh){
  $destfile = "";
  $sql = "select picture from media where mid = ?";
  $resultset = prepared_query($dbh,$sql,$mid);
  $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
  if ($row['picture'] != "" and $row['picture'] != null){
    $destfile = $row['picture']; //for media, picture link is stored directly in database
  }
  else{
    $destfile = "mediaimages/0.png";
  }
  return $destfile;
}

//add a contribution between a piece of media and a person to the database
function addContribution($dbh, $mediaid, $personid, $mediatitle, $personname, $findcontribution, $contribution){
  $contributionexists = prepared_query($dbh,$findcontribution,array($personid,$mediaid));
  $numrows = $contributionexists->numRows();
  if ($numrows == 0){
    $addactorcontribution = prepared_query($dbh,$contribution,array($personid,$mediaid));
    echo "<p>Successfully added \"" . $mediatitle . "\" and " . $personname . " together to the database.<p>";
  }
}

//add a piece of media to the database
function addMedia($dbh, $title, $type, $genre, $length, $uid, $albumid, $description, $findmediaquery, $media, $values){
  $mediaexists = prepared_query($dbh,$findmediaquery,$values);
  $numrows = $mediaexists->numRows();

  if ($numrows == 0){
    $datetime = query($dbh,"Select now()");
    while ($row = $datetime->fetchRow(MDB2_FETCHMODE_ASSOC)){
      $thetime = $row['now()'];
    }
    $values = array($title,$genre,$length,$type, $albumid,$thetime,$uid, $description);
    $addmedia = prepared_query($dbh,$media,$values);
    echo "<p>Successfully added \"" . $title . "\" to the database.";
    $getmediaid = query($dbh,"select last_insert_id()");
    while($row = $getmediaid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $mediaid = $row['last_insert_id()'];
    }
  }
  else {
    while($row = $mediaexists->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $mediaid = $row['mid'];
    }
    if ($type == "song"){ //give a special message for a song
      echo "<p>The song \"" . $title . "\" by the artist you inputted already existed.";
    }
    else {
      echo "<p>The media \"" . $title . "\" already existed.";
    }
  }
  return $mediaid;
}

//handles all editing media cases, with new artist, genre, length, title, new contributions, etc...
function editMedia($uid, $mid, $title, $type, $genre, $length, $artist, $albumname, $deleteactorarry, $addactorarray, $description, $deletesongarray, $addsongarray){
  global $dbh;
  echo '<div id="results" style="display: none;">'; //some of these methods below print out, and we don't want to display these to the user

  $person = "Insert into person (name,addedby, description) values (?,?, ?)";
  $media = "Insert into media (title, genre, length, type, albumid, dateadded,addedby, rating, description) values (?,?,?,?,?,?,?,0,?)";
  $contribution = "Insert into contribution values (?,?)";
  $findmedia = "Select * from media where title = ?";
  $findmediawithtype = "select * from media where title = ? and type = ?";
  $findperson = "select * from person where name = ?";
  $findcontribution = "select * from contribution where pid = ? and mid = ?";
  $findmediausingpid = "select mid, pid, genre from media inner join contribution using (mid) where title = ? and type = ? and pid = ?";
  $changealbumid = "update media set albumid = ? where mid = ?";
  $deletecontribution1 = "delete from contribution where mid = ? and pid = ?";
  $update = "update media set title = ?, type = ?, genre = ?, length = ?, description = ? where mid = ?";
  $values = array($title, $type, $genre, $length, $description, $mid,);
  $updatemedia = prepared_statement($dbh, $update, $values);

  //artist might have been switched
  if ($type == "song" or $type == "album"){
    $deletecontribution = "delete from contribution where mid = ?";
    $delete = prepared_statement($dbh, $deletecontribution, $mid);

    $artistid = addPerson($dbh, $artist, $uid, null, $findperson, $person);
    addContribution($dbh, $mid, $artistid, $title, $artist, $findcontribution, $contribution);
  }

  if ($type == "song"){ //album might have been switched
    $newalbumid = findMedia($dbh, $albumname, "album", $findmedia, $findmediawithtype);
    if ($newalbumid != null){ //either 0 results or more than 1 result
      $updatealbum = "update media set albumid = ? where mid = ?";
      $values = array($newalbumid, $mid);
      $updatemediaalbum = prepared_statement($dbh,$updatealbum,$values);
    }
    else {
      echo "Sorry, that album does not exist. Please enter it separately first.";
    }
  }

  if ($type == "tv" or $type == "movie"){ //delete and add actors
    foreach ($deleteactorarry as $key => $value) {
      $delete = prepared_statement($dbh, $deletecontribution1, array($mid,$value));
    }
    foreach ($addactorarray as $key => $value) {
      $artistid = addPerson($dbh, $value, $uid, null, $findperson, $person);
      addContribution($dbh, $mid, $artistid, $title, $value, $findcontribution, $contribution);
    }
  }
  if ($type == "album"){
    foreach ($deletesongarray as $key => $value){
      prepared_statement($dbh,$changealbumid,array(null,$value));
    }
    foreach ($addsongarray as $key => $value){
      $findthesong = prepared_query($dbh,$findmediawithtype,array($value,"song"));
      $numRows = $findthesong -> numRows();
      if ($numRows == 1){
        $row = $findthesong->fetchRow(MDB2_FETCHMODE_ASSOC);
        $songmid = $row['mid'];
        $findthecontribution = prepared_query($dbh,$findcontribution,array($artistid,$songmid));
        $numRows = $findthecontribution -> numRows();
        if ($numRows == 1){
          echo "woohoo!";
          $changetheid = prepared_query($dbh,$changealbumid,array($mid,$songmid));
        }
      }
    }
  }
  echo "</div>";
}

//same as above, but for people
function editPerson($pid, $name, $description, $deletecontributionarray, $addcontributionarray, $addcontributiontypearray){

  global $dbh;
  echo '<div id="results" style="display: none;">';

  $contribution = "Insert into contribution values (?,?)";
  $findmedia = "Select * from media where title = ?";
  $findmediawithtype = "select * from media where title = ? and type = ?";
  $findcontribution = "select * from contribution where pid = ? and mid = ?";

  $update = "update person set name = ?, description = ? where pid = ?";
  $values = array($name, $description, $pid,);
  $updateperson = prepared_statement($dbh, $update, $values);

  foreach ($deletecontributionarray as $key => $value) { //delete movies/tv shows
    $deletecontribution1 = "delete from contribution where pid = ? and mid = ?";
    $delete = prepared_statement($dbh, $deletecontribution1, array($pid,$value));
  }

  foreach ($addcontributionarray as $key => $value) { //add movies/tv shows
    $newmediaid = findMedia($dbh, $value, $addcontributiontypearray[$key], $findmedia, $findmediawithtype);
    if ($newmediaid != null){
      addContribution($dbh, $newmediaid, $pid, $name, $value, $findcontribution, $contribution);
    }
    else {
      echo "Sorry, that media does not exist. Please enter it separately first.";
    }
  }

  echo "</div>";
}

//get the person who sang a song/album
function getAlbumSongContributions($dbh, $mid){
  $sql = "select pid, name from person inner join contribution using (pid) where mid = ?";
  $resultset = prepared_query($dbh,$sql,$mid);
  while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $pid = $row['pid'];
    $name = $row['name'];
    return array('pid'=>$pid, 'name'=>$name);
  }
}



?>
