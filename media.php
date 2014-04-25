<?php
   
require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");

$page = $_SERVER['PHP_SELF'];
$dbh = db_connect($athomas2_dsn);

function getMedia($values, $dbh) {
   $sql = "select * from media where mid=?";
   $resultset = prepared_query($dbh, $sql, $values);
   $numRows = $resultset->numRows();
   if ($numRows != 0){
     $detailrow = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
     $rating = $detailrow['rating'];
     $title = $detailrow['title'];
     $dateadded = $detailrow['dateadded'];
     $genre = $detailrow['genre'];
     $length = $detailrow['length'];
     $type = $detailrow['type'];
     if ($type == "song"){
      $albumid = $detailrow['albumid'];
      $albumarray = getMedia($albumid, $dbh);
      $albumname = $albumarray['title'];
     }
     else {
      $albumid = 0;
      $albumname = "";
     }
     return array('rating' => $rating, 'title' => $title, 'dateadded' => $dateadded, 'genre' => $genre, 'length' => $length, 'type' => $type, 'albumid' => $albumid, 'albumname' => $albumname);
   }
   else{
    return null;
   }
}

function getTVMovieContributions($dbh,$values,$page){
  $sql = "select pid, name from person inner join contribution using (pid) where mid = ?";
  $resultset = prepared_query($dbh,$sql,$values);
  echo "Actors<ul>";
  while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $pid = $row['pid'];
    $name = $row['name'];
    echo "<li><a href= \"person.php?pid=" . $pid . "\">$name</a><br><br></li>";
  }
  echo "</ul>";
}

function getAlbumSongs($dbh,$values,$page){
  $sql = "select * from media where albumid = ?";
  $resultset = prepared_query($dbh,$sql,$values);
  echo "Songs<ul>";
  while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $mid = $row['mid'];
    $title = $row['title'];
    $length = $row['length'];
    if ($length != "" or $length != null){
      echo "<li><a href= \"media.php?mid=" . $mid . "\">$title ($length)</a><br><br></li>";
    }
    else{
      echo "<li><a href= \"media.php?mid=" . $mid . "\">$title</a><br><br></li>";
    }
  } 
  echo "</ul>";
}

function getAlbumSongContributions($dbh, $values){
  $sql = "select pid, name from person inner join contribution using (pid) where mid = ?";
  $resultset = prepared_query($dbh,$sql,$values);
  echo "Artist: ";
  while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $pid = $row['pid'];
    $name = $row['name'];
    echo "<a href= \"person.php?pid=" . $pid . "\">$name</a><br>";
  }
}

function showRecentMedia($dbh,$page){
  $sql = "select * from media order by dateadded desc limit 10";
  $resultset = query($dbh,$sql);
  echo "Most Recently Added Items<br>";
  while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $mid = $row['mid'];
    $title = $row['title'];
    $genre = $row['genre'];
     echo "<a href= \"" . $page . "?mid=" . $mid . "\">$title ($genre)</a><br><br>";
  }
}

function getUid($dbh, $username) {
  $sql = "select uid from user where username = ?";
  $resultset = prepared_query($dbh, $sql, $username);
  $detailrow = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
  $uid = $detailrow['uid'];
  return $uid;
}

function addMediaButton($dbh, $page, $mid, $uid){
  $sql = "select * from likes where uid = ? and mid = ?";
  $resultset = prepared_query($dbh, $sql, array($uid,$mid));
  $numResults = $resultset->numRows();
  if ($numResults == 1){
    echo "This media is in your Top Ten List.<br>";
  }
  else {
    echo '<form method="get" action="' . $page . '">
      <input type="hidden" name="mid" value="' . $mid . '">
    <input type="hidden" name="addmedia">
    <input type="submit" value="Add to Top Ten" class="btn btn-primary btn-lg">
  </form><br>';
  }
}

function addMedia($dbh, $mid, $uid){
  $datetime = query($dbh,"Select now()");
  while ($row1 = $datetime->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $thetime = $row1['now()'];
  }
   $values = array($uid,$mid,);
   $sql = "select * from likes where uid = ? and mid = ?";
   $resultset = prepared_query($dbh,$sql,$values);
   $numResults = $resultset->numRows();
   if ($numResults == 0){
     $values = array($uid,$mid,$thetime,);
     $sql = "insert into likes values (?,?,?)";
     prepared_statement($dbh, $sql, $values);
   }
   else{
    $values = array($thetime,$uid,$mid);
    $sql = "update likes set dateadded = ? where uid = ? and mid = ?";
    prepared_statement($dbh,$sql,$values);
   }
    $sql = "delete from likes where (uid,mid) not in (select uid,mid from (select uid,mid from likes order by dateadded desc limit 10) foo)";
    query($dbh,$sql);
}

function showComments($dbh, $mid){
  $sql = "select rid, uid, name, comment, initial, dateadded from reviews inner join user using (uid) where mid = ? order by initial";
  $resultset = prepared_query($dbh,$sql,$mid);
  $lastinitial = 0;
  $counter = 0;
  echo '<div class="auto" style="width:600px; height:500px; overflow: auto;">';
  while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $name = $row['name'];
    $comment = $row['comment'];
    $time = $row['dateadded'];
    $initial = $row['initial'];
    $rid = $row['rid'];
    $uid = $row['uid'];
    $counter++;
    if ($counter == 1){
      $lastinitial = $initial;
    }
    if ($rid == $initial){
      if ($lastinitial != $initial){
        echo '</div></div>';
        $lastinitial = $initial;
      }
      echo '<div class="media"><a class="pull-left" href="user.php?uid=' . $uid . '">
            <img class="media-object" src="adele.jpg" alt="Media Object"></a>
            <div class="media-body">
            <h4 class="media-heading">' . $name . '</h4> at ' . $time . '<br>' . $comment;
    }
    else{
      echo '<div class="media"><a class="pull-left" href="user.php?uid=$uid">
            <img class="media-object" src="adele.jpg" alt="Media Object"></a>
            <div class="media-body">
            <h4 class="media-heading">' . $name . '</h4> at ' . $time . '<br>' . $comment . '</div></div>';
    }

  }
  echo "</div><br><br>";
}

function commentForm($page, $mid){
  echo '<form method="post" action="' . $page . '">
      <input type="hidden" name="mid" value="' . $mid . '">
      <textarea rows="4" cols="50" name="comment"></textarea><br>
    <input type="hidden" name="addcomment">
    <input type="submit" value="Add Comment" class="btn btn-primary btn-lg">
  </form><br>';
}

function addComment($dbh, $mid, $uid, $comment){
  $datetime = query($dbh,"Select now()");
  while ($row1 = $datetime->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $thetime = $row1['now()'];
  }
 //  $values = array($uid,$mid,);
   //$sql = "select * from likes where uid = ? and mid = ?";
 //  $resultset = prepared_query($dbh,$sql,$values);
 //  $numResults = $resultset->numRows();
 //  if ($numResults == 0){
     $sql = "select rid from reviews order by rid desc limit 1";
     $resultset = query($dbh,$sql);
     $row1 = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
     $rid = $row1['rid'] + 1;
     $values = array($uid,$mid,$thetime,$rid,$comment);
     $sql = "insert into reviews (uid,mid,dateadded,initial,comment) values (?,?,?,?,?)";
     prepared_statement($dbh, $sql, $values);
 //}
}

function addRating($dbh,$uid,$mid,$rating){
  $sql = "select * from ratings where uid = ? and mid = ?";
  $values = array($uid,$mid,);
  $resultset = prepared_query($dbh,$sql,$values);
  $numRows = $resultset->numRows();
  if ($numRows == 0){
     $sql = "insert into ratings values (?,?,?)";
     $values = array($uid,$mid,$rating,);
     prepared_statement($dbh, $sql, $values);
   }
   else {
      $sql = "update ratings set rating = ? where uid = ? and mid = ?";
      $values = array($rating,$uid,$mid,);
      prepared_statement($dbh,$sql,$values);
   }
   $sql = "select mid, avg(rating) as avgrating from ratings where mid = ?";
   $resultset = prepared_query($dbh,$sql,$mid);
   $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
   $avgrating = $row['avgrating'];
   $sql = "update media set rating = ? where mid = ?";
   $resultset = prepared_statement($dbh,$sql,array($avgrating,$mid,));
}
  
checkLogInStatus();   
$username = $_SESSION['username'];
$uid = getUid($dbh,$username);

if (isset($_REQUEST['mid'])){
  $pagemid = $_REQUEST['mid'];
  $mediaarray = getMedia($pagemid,$dbh);
  if ($mediaarray == null){
    header ("Location: media.php");
    exit();
  }

  else{
    $title = $mediaarray['title'];
    $genre = $mediaarray['genre'];
    $length = $mediaarray['length'];
    $type = $mediaarray['type'];
    $albumname = $mediaarray['albumname'];
    $albumid = $mediaarray['albumid'];
    $rating = $mediaarray['rating'];

    printPageTop("$title");
    createNavBar("home.php");
    echo "<h1>$title</h1>";

    if (isset($_REQUEST['rating'])){
      addRating($dbh,$uid,$pagemid,$_REQUEST['rating']);
      $mediaarray = getMedia($pagemid,$dbh);
      $rating = $mediaarray['rating'];
    }

    createStars($pagemid);

    echo "Rating: $rating<br>";
    echo "Genre: $genre<br>";

    if ($length != ""){
      echo "Length: $length<br>";
    }

    if ($type == "song"){
      echo getAlbumSongContributions($dbh,$pagemid,$page);
      echo "From Album: <a href= \"media.php?mid=" . $albumid . "\">$albumname</a><br><br>";
    }
    if ($type == "album"){
      echo getAlbumSongContributions($dbh,$pagemid,$page);
    }

    echo "<br>";

    if (isset($_REQUEST['addmedia'])){
      addMedia($dbh, $pagemid, $uid);
    }

    addMediaButton($dbh,$page,$pagemid,$uid);

    if ($type == "tv" or $type == "movie"){
      getTVMovieContributions($dbh,$pagemid,$page);
    }
    else if ($type == 'album'){
      getAlbumSongs($dbh,$pagemid,$page);
    }

    if (isset($_REQUEST['addcomment'])){
      $comment = $_REQUEST['comment'];
      addComment($dbh, $pagemid, $uid, $comment);
    }
    showComments($dbh,$pagemid);
    commentForm($page, $pagemid);
  }
}

else{
  printPageTop("Media");
  createNavBar("home.php");
  echo "<h1>Media</h1>";
  showRecentMedia($dbh,$page);
}




?>

</body>
</html>