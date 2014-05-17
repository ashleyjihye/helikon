<?php

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");

$dbh = db_connect($athomas2_dsn);


function logIn() {
    global $dbh;
    if(isset($_POST['loginusername'])) {
        $username = $_POST['loginusername'];
        if( loginCredentialsAreOkay($dbh,$username,$_POST['loginpassword']) ) {
          session_start();
            $_SESSION['username'] = $username;
            header('Location: home.php');
        }
        else {
            echo "<p>Sorry, that's incorrect.  Please try again\n";
        }
      }
}

function logOut() {
    session_destroy();
    header('Location: index.php');
}

function signIn(){
  global $dbh;
  if (isset($_REQUEST['username'])) {
    $name = $_REQUEST['name'];
    $username = $_REQUEST['username'];
    $email = $_REQUEST['email'];
    $sql = "select * from user where username = ?";
    $resultset = prepared_query($dbh,$sql,$username);
    $numRows = $resultset->numRows();
    if ($numRows == 1){
      echo "Sorry, that username has already been taken. Pleae choose another.";
      exit();
    }
    else if ($_REQUEST['password'] == $_REQUEST['password1']){
      $values = array($name,$username,$_REQUEST['password'],$email,);
      $sql = "insert into user (name,username,password,email) values (?,?,?,?)";
      $resultset = prepared_query($dbh,$sql,$values);
    }
    else {
      echo "Your passwords did not match. Please try again.";
      exit();
    }

  }

}

function checkLogInStatus() {
  session_start();
  if (isset($_SESSION['username'])){

    return true;
  }
  else {
    header('Location: index.php');
    exit();
  }

}

function loginCredentialsAreOkay($dbh,$username,$password) {
    $check = "SELECT count(*) AS n FROM user WHERE username=? AND password=?";
    $resultset = prepared_query($dbh, $check, array($username,$password));
    $row = $resultset->fetchRow();
    return( $row[0] == 1 );
}

function printLoggedInNavBar() {
    $script = $_SERVER['PHP_SELF'];
    print <<<EOT
<form method="post" action="$script">
  <input type="submit" value="logout">
</form>
EOT;
}

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
     <a class="brand" href="' . $page . '"><img src="logofinal.png"></a> 
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="media.php">Media</a></li>
      </ul>

      <form method="get" action="' . $page . '" class="navbar-nav navbar-form" role="search">
      <div class="form-group">
        <select class="form-control" name="tables">
        <option value="All">All</option>
        <option value="Users">Users</option>
        <option value="People">People</option>
        <option value="Movies">Movies</option>
      <option value="Albums">Albums</option>
      <option value="Songs">Songs</option>
      <option value="TVShows">TV Shows</option>
      <option value="Genres">Genres</option>
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
            <li><a href="home.php?friends">Friends</a></li>
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

function createActualRating($rating, $numReviews){
  echo "Current Rating: <div class='star' id='star1'></div>
  <script>$('#star1').raty({ score: " . $rating . ", readOnly: true});</script>
   (based on " . $numReviews . " reviews)<br>";
}

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


function getUid($dbh, $username) {
  $sql = "select uid from user where username = ?";
  $resultset = prepared_query($dbh, $sql, $username);
  $detailrow = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
  $uid = $detailrow['uid'];
  return $uid;
}


function addPerson($dbh, $name, $uid, $findperson, $person){
  $values = array($name);
  $getperson = prepared_query($dbh,$findperson,$values);
  $numrows = $getperson->numRows();
  if ($numrows != 0){
    echo "<p>There's already someone named " . $name .".<br>";
    while($row = $getperson->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $personid = $row['pid'];
    }
  }
  else {
    $addperson = prepared_query($dbh,$person,array($name,$uid));
    echo "<p>Successfully added " . $name . " to the database.<br>";
    $getpersonid = query($dbh,"select last_insert_id()");
    while($row = $getpersonid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $personid = $row['last_insert_id()'];
    }
  }
  return $personid;
}


function findMedia($dbh, $mediatitle, $mediatype, $findmedia, $findmediawithtype){
  if ($mediatype == ""){
    $checkmedia = prepared_query($dbh,$findmedia,array($mediatitle));
  }
  else{
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


function addContribution($dbh, $mediaid, $personid, $mediatitle, $personname, $findcontribution, $contribution){
  $contributionexists = prepared_query($dbh,$findcontribution,array($personid,$mediaid));
  $numrows = $contributionexists->numRows();
  if ($numrows == 0){
    $addactorcontribution = prepared_query($dbh,$contribution,array($personid,$mediaid));
    echo "<p>Successfully added \"" . $mediatitle . "\" and " . $personname . " together to the database.<p>";
  }
}

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
    if ($type == "song"){
      echo "<p>The song \"" . $title . "\" by the artist you inputted already existed.";
    }
    else {
      echo "<p>The media \"" . $title . "\" already existed.";
    }
  }
  return $mediaid;
}


function editMedia($uid, $mid, $title, $type, $genre, $length, $artist, $albumname, $deleteactorarry, $addactorarray, $description){

  global $dbh;
  echo '<div id="results" style="display: none;">';

  $person = "Insert into person (name,addedby) values (?,?)";
  $media = "Insert into media (title, genre, length, type, albumid, dateadded,addedby, rating, description) values (?,?,?,?,?,?,?,0,?)";
  $contribution = "Insert into contribution values (?,?)";
  $findmedia = "Select * from media where title = ?";
  $findmediawithtype = "select * from media where title = ? and type = ?";
  $findperson = "select * from person where name = ?";
  $findcontribution = "select * from contribution where pid = ? and mid = ?";
  $findmediausingpid = "select mid, pid, genre from media inner join contribution using (mid) where title = ? and type = ? and pid = ?";


  $update = "update media set title = ?, type = ?, genre = ?, length = ?, description = ? where mid = ?";
  $values = array($title, $type, $genre, $length, $description, $mid,);
  $updatemedia = prepared_statement($dbh, $update, $values);


  if ($type == "song" or $type == "album"){
    $deletecontribution = "delete from contribution where mid = ?";
    $delete = prepared_statement($dbh, $deletecontribution, $mid);

    $artistid = addPerson($dbh, $artist, $uid, $findperson, $person);
    addContribution($dbh, $mid, $artistid, $title, $artist, $findcontribution, $contribution);
  }

  if ($type == "song"){
    $newalbumid = findMedia($dbh, $albumname, "album", $findmedia, $findmediawithtype);
    if ($newalbumid != null){
      $updatealbum = "update media set albumid = ? where mid = ?";
      $values = array($newalbumid, $mid);
      $updatemediaalbum = prepared_statement($dbh,$updatealbum,$values);
    }
    else {
      echo "Sorry, that album does not exist. Please enter it separately first.";
    }
  }

  if ($type == "tv" or $type == "movie"){
    foreach ($deleteactorarry as $key => $value) {
      echo "$value";
      $deletecontribution1 = "delete from contribution where mid = ? and pid = ?";
      $delete = prepared_statement($dbh, $deletecontribution1, array($mid,$value));
    }
    foreach ($addactorarray as $key => $value) {
      $artistid = addPerson($dbh, $value, $uid, $findperson, $person);
      addContribution($dbh, $mid, $artistid, $title, $value, $findcontribution, $contribution);
    }

  }



  echo "</div>";
}

function editPerson($pid, $name, $deletecontributionarray, $addcontributionarray, $addcontributiontypearray){

  global $dbh;
  echo '<div id="results" style="display: none;">';

  $contribution = "Insert into contribution values (?,?)";
  $findmedia = "Select * from media where title = ?";
  $findmediawithtype = "select * from media where title = ? and type = ?";
  $findcontribution = "select * from contribution where pid = ? and mid = ?";

  $update = "update person set name = ? where pid = ?";
  $values = array($name, $pid,);
  $updateperson = prepared_statement($dbh, $update, $values);

  foreach ($deletecontributionarray as $key => $value) {
    $deletecontribution1 = "delete from contribution where pid = ? and mid = ?";
    $delete = prepared_statement($dbh, $deletecontribution1, array($pid,$value));
  }

  foreach ($addcontributionarray as $key => $value) {
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



?>
