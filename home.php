<?php

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");

$dbh = db_connect($athomas2_dsn);

  checkLogInStatus();
  printPageTop("Home");
  createNavBar($_SERVER['PHP_SELF']);
?>
<script src='jquery.tablesorter.js'></script>
<script>
$(document).ready(function() 
    { 
      $('#nameTable').tablesorter();
      $('#userTable').tablesorter();
      $('#movieTable').tablesorter();
      $('#tvTable').tablesorter();
      $('#genreTable').tablesorter();
      $('#albumTable').tablesorter();
      $('#songTable').tablesorter();
    } 
); 
    
</script>
<style>
.table {
  width:80%;
  }

th.headerSortUp { 
    background-image: url(desc.jpeg); 
  background-repeat: no-repeat;
     
} 
th.headerSortDown { 
    background-image: url(asc.jpeg); 
  background-repeat: no-repeat;
    
} 
small_desc.gif {
  padding: 30px;
margin: 30px;
align:right;
  background-color: #ffffff;
}
</style>
<?php
function displayNames($values, $dbh, $status) {
  $sql = "select * from person where name like concat ('%', ?, '%') order by name";
  $resultset = prepared_query($dbh, $sql, $values);
  $numpeople = $resultset->numRows();
 
  if ($numpeople == 0) {
    echo "0 people found";
    return 0;
  }
  else {
    $location;
  echo "<table id='nameTable' class = 'tablesorter table'><thead><tr><th>Name</th></tr></thead><tbody>";
  
  if ($numpeople == 1) {
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $pid = $row['pid'];
      $name = $row['name'];
      $location = "person.php?pid=" . $pid;
    }
    if ($status == "single"){
      header ("Location: person.php?pid=" . $pid);
    }
    else {
      echo "<h3>1 name found</h3>";
      echo "<tr><td><a href= \"person.php?pid=" . $pid . "\">$name</a></td></tr>";
    }
  }
  else {
    echo "<h3>$numpeople names found</h3>";
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $name = $row['name'];
        $pid = $row['pid'];
        echo "<tr><td><a href= \"person.php?pid=" . $pid . "\">$name</a></td></tr>";
    
    }
    $location = -1;
  }
    echo "</tbody></table>";
    return $location;
  }
}

function displayUsers($values,$dbh, $status){
  $sql = "select * from user where name like concat ('%', ?, '%') order by name";
  $resultset = prepared_query($dbh, $sql, $values);

 
  $numpeople = $resultset->numRows();
  if ($numpeople == 0) {
    echo "0 users found";
    return 0;
  }
  else {
    $location;
    echo "<table id='userTable' class='tablesorter table'><thead><tr><th>Name</th></tr></thead><tbody>";
  if($numpeople==1) {
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $uid = $row['uid'];
      $name = $row['name'];
      $location = "user.php?uid=" . $uid;
    }
    if ($status == "single"){
      header ("Location: user.php?uid=" . $uid);
    }
    else{
      echo "<h3>1 user found</h3>";
      echo "<tr><td><a href= \"user.php?uid=" . $uid . "\">$name</a></td></tr>";
    }
  }
  else {
    echo "<h3>$numpeople users found</h3>";
  while($row= $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $name = $row['name'];
    $uid=$row['uid'];
    echo "<tr><td><a href= \"user.php?uid=" . $uid . "\">$name</a></td></tr>";
  }
  $location = -1;
}
  echo "</tbody></table>";
  return $location;
}
}
  

function displayFriends($dbh){
  $sql = "select uid from user where username = ? order by name";
  $resultset = prepared_query($dbh, $sql, $_SESSION['username']);
  $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
  $useruid = $row['uid'];
  $sql = "select uid, friendid from friends where (uid = ? or friendid = ?) and state = '1'";
  $resultset = prepared_query($dbh, $sql, array($useruid,$useruid,));
  $numpeople = $resultset->numRows();
  $thefriend;
  if ($numpeople == 1) {
    echo "<h3>1 friend found</h3>";
  }
  else{    
    echo "<h3>$numpeople friends found</h3>";
  }
  while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $uid = $row['uid'];
    $friendid = $row['friendid'];
    if ($uid == $useruid){
      $thefriend = $friendid;
    }
    else{
      $thefriend = $uid;
    }
    $sql1 = "select name from user where uid = ?";
    $resultset1 = prepared_query($dbh, $sql1, $thefriend);
    $row1 = $resultset1->fetchRow(MDB2_FETCHMODE_ASSOC); 
    $name = $row1['name'];
    echo "<a href= \"user.php?uid=" . $thefriend . "\">$name</a><br><br>";
  }
}

function displayMovies($values, $dbh, $status) {
  $sql = "select title, mid, rating, type, genre from media where title like concat('%', ?, '%') and type = 'movie' order by title";
  $resultset = prepared_query($dbh, $sql, $values);
  $numMovies = $resultset->numRows();
  if ($numMovies == 0) {
    echo "0 movies found";
    return 0;
  }
  else {
    $location;
  echo "<table id='movieTable' class='tablesorter table'><thead><tr><th>Title</th><th>Genre</th><th>Rating</th></tr></thead><tbody>";
  if($numMovies == 1) {
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $mid = $row['mid'];
      $title = $row['title'];
      $genre = $row['genre'];
      $location = "media.php?mid=" . $mid;
      $rating = $row['rating'];
    }
    if ($status == "single"){
      header ("Location: media.php?mid=" . $mid);
    }
    else{
      echo "<h3>1 movie found</h3>";
    echo "<tr><td><a href= \"media.php?mid=" . $mid . "\">$title</a></td><td>" . $genre . "</td><td><div style='display:none'>" . $rating . "</div><div class='star' id='star" . $mid . "'></div>
  <script>$('#star" . $mid . "').raty({ score: " . $rating . ", readOnly: true});</script></td></tr>";
    }
  }
  else {
    echo"<h3>$numMovies titles found</h3>";
  while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $title = $row['title'];
    $genre = $row['genre'];
    $mid = $row['mid'];
    $type = $row['type'];
    $rating = $row['rating'];
     echo "<tr><td><a href= \"media.php?mid=" . $mid . "\">$title</a></td><td>" . $genre . "</td><td><div style='display:none'>" . $rating . "</div><div class='star' id='star" . $mid . "'></div>
  <script>$('#star" . $mid . "').raty({ score: " . $rating . ", readOnly: true});</script></td></tr>";
 
  }
  $location = -1;
}
  echo "</table></tbody>";
  return $location;


}
}

function displayTVshows($values, $dbh, $status) {
  $sql = "select * from media where title like concat('%', ?, '%') and type = 'tv' order by title";
   $resultset = prepared_query($dbh, $sql, $values);
  $numShows = $resultset->numRows();
  if ($numShows == 0) {
    echo "0 tv shows found";
    return 0;
  }
  else {
    $location;
  echo "<table id='tvTable' class='tablesorter table'><thead><tr><th>Title</th><th>Genre</th><th>Rating</th></tr></thead><tbody>";
  if($numShows == 1) {
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $mid = $row['mid'];
      $title = $row['title'];
      $genre = $row['genre'];
      $location = "media.php?mid=" . $mid;
      $rating = $row['rating'];
    }
    if ($status == "single"){
      header ("Location: media.php?mid=" . $mid);
    }
    else{
      echo "<h3>1 tv show found</h3>";
    echo "<tr><td><a href= \"media.php?mid=" . $mid . "\">$title</a></td><td>" . $genre . "</td><td><div style='display:none'>" . $rating . "</div><div class='star' id='star" . $mid . "'></div>
  <script>$('#star" . $mid . "').raty({ score: " . $rating . ", readOnly: true});</script></td></tr>";
    }
  }
  else {
    echo"<h3>$numShows titles found</h3>";
  while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $title = $row['title'];
    $genre = $row['genre'];
    $mid = $row['mid'];
    $type = $row['type'];
    $rating = $row['rating'];
    
    echo "<tr><td><a href= \"media.php?mid=" . $mid . "\">$title</a></td><td>" . $genre . "</td><td><div style='display:none'>" . $rating . "</div><div class='star' id='star" . $mid . "'></div>
  <script>$('#star" . $mid . "').raty({ score: " . $rating . ", readOnly: true});</script></td></tr>";

  }
  $location = -1;
}
  echo "</tbody></table>";
  return $location;

}
}


function displayAlbums($values, $dbh, $status) {
  $sql = "select * from media where title like concat('%', ?, '%') and type = 'album' order by title";
  $resultset = prepared_query($dbh, $sql, $values);
  $numAlbums = $resultset->numRows();
  if ($numAlbums == 0) {
    echo "0 albums found";
    return 0;
  }
  else {

    $location;
  echo "<table id='albumTable' class='tablesorter table'><thead><tr><th>Title</th><th>Artist</th><th>Genre</th><th>Rating</th></tr></thead><tbody>";

  if($numAlbums == 1) {
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $mid = $row['mid'];
      $title = $row['title'];
      $genre = $row['genre'];
      $location = "media.php?mid=" . $mid;
      $artistarray = getAlbumSongContributions($dbh,$mid);
      $rating = $row['rating'];
    }
    if ($status == "single"){
      header ("Location: media.php?mid=" . $mid);
    }
    else{
      echo "<h3>1 album found</h3>";
      $pid = $artistarray['pid'];
      $name = $artistarray['name'];
      echo "<tr><td><a href= \"media.php?mid=$mid\">$title</a></td><td><a href= \"person.php?pid=$pid\">$name</a></td><td>" . $genre . "</td><td><div style='display:none'>" . $rating . "</div><div class='star' id='star" . $mid . "'></div>
  <script>$('#star" . $mid . "').raty({ score: " . $rating . ", readOnly: true});</script></td></tr>";
    }
  }
  else {
    echo"<h3>$numAlbums albums found</h3>";
  while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $title = $row['title'];
    $genre = $row['genre'];
    $mid = $row['mid'];
    $type = $row['type'];
    $artistarray = getAlbumSongContributions($dbh,$mid); 
    $pid = $artistarray['pid'];
    $name = $artistarray['name'];
    $rating = $row['rating'];
    echo "<tr><td><a href= \"media.php?mid=$mid\">$title</a></td><td><a href= \"person.php?pid=$pid\">$name</a></td><td>" . $genre . "</td><td><div style='display:none'>" . $rating . "</div><div class='star' id='star" . $mid . "'></div>
  <script>$('#star" . $mid . "').raty({ score: " . $rating . ", readOnly: true});</script></td></tr>";
  }
  $location = -1;
}
  echo "</tbody></table>";
  return $location;
}
}

function displaySongs($values, $dbh, $status) {
  $sql = "select * from media where title like concat('%', ?, '%') and type = 'song' order by title";
  $resultset = prepared_query($dbh, $sql, $values);
  $numSongs = $resultset->numRows();
  if ($numSongs == 0) {
    echo "0 albums found";
    return 0;
  }
  else {

    $location;
  echo "<table id='songTable' class='tablesorter table'><thead><tr><th>Title</th><th>Artist</th><th>Genre</th><th>Rating</th></tr></thead><tbody>";
  if($numSongs == 1) {
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $mid = $row['mid'];
      $title = $row['title'];
      $genre = $row['genre'];
      $location = "media.php?mid=" . $mid;
      $rating = $row['rating'];
    }
    if ($status == "single"){
      header ("Location: media.php?mid=" . $mid);
    }
    else{
      echo "<h3>1 song found</h3>";
      $artistarray = getAlbumSongContributions($dbh,$mid); 
      $pid = $artistarray['pid'];
      $name = $artistarray['name'];
      echo "<tr><td><a href= \"media.php?mid=$mid\">$title</a></td><td><a href= \"person.php?pid=$pid\">$name</a></td><td>" . $genre . "</td><td><div style='display:none'>" . $rating . "</div><div class='star' id='star" . $mid . "'></div>
  <script>$('#star" . $mid . "').raty({ score: " . $rating . ", readOnly: true});</script></td></tr>";
    }
  }
  else {
    echo"<h3>$numSongs songs found</h3>";
  while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $title = $row['title'];
    $genre = $row['genre'];
    $mid = $row['mid'];
    $type = $row['type'];
    $artistarray = getAlbumSongContributions($dbh,$mid); 
    $pid = $artistarray['pid'];
    $name = $artistarray['name'];
    $rating = $row['rating'];
    echo "<tr><td><a href= \"media.php?mid=$mid\">$title</a></td><td><a href= \"person.php?pid=$pid\">$name</a></td><td>" . $genre . "</td><td><div style='display:none'>" . $rating . "</div><div class='star' id='star" . $mid . "'></div>
  <script>$('#star" . $mid . "').raty({ score: " . $rating . ", readOnly: true});</script></td></tr>";
  
  }
  $location = -1;
}
  echo "</tbody></table>";
  return $location;
}
}

function getTrendingMedia($dbh){
    echo "<h1>Suggestions</h1>";
    echo "<h3>Trending Media</h3>";
    $sql = "select likes.mid as mid, count(mid) as count, title from likes inner join media using (mid) group by mid order by count(mid) desc limit 20";    
    $resultset = query($dbh,$sql);
    echo "<table id='trendingTable' class='tablesorter table';><thead><tr><th>Title</th><th>Likes</th></tr></thead><tbody>";
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $title= $row['title'];
      $count = $row['count'];
      $mid = $row['mid'];
      if($count==1) {
	echo"<tr><td><a href=\"media.php?mid=" . $mid . "\">$title</a></td><td>" . $count . " person likes this</td></tr>";
      }
      else {
      echo "<tr><td><a href= \"media.php?mid=" . $mid . "\">$title</a></td><td>" . $count . " people like this</td></td></tr>";
      }
    }
    echo "</tbody></table><br>";
}

function getUsersInCommon($dbh, $uid){
  $sql = "select count(uid) as count, uid, name from likes inner join user using (uid) where mid in (select mid from likes where uid = ?) and uid != ? group by uid limit 20";
  $resultset = prepared_query($dbh,$sql,array($uid,$uid));
  $numRows = $resultset->numRows();
  if ($numRows == 0){
    return;
  }
  echo "<br><h3>Users You Might Like</h3><br>";
  echo "<table id='commonTable' class='table';><thead><tr><th>User</th><th># Likes in Common</th></tr></thead><tbody>";
  while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $count= $row['count'];
    $name = $row['name'];
    $altuid = $row['uid'];
    echo "<tr><td><a href= \"user.php?uid=" . $altuid . "\">$name</a></td><td>" . $count . "</td></td></tr>";
  }
  echo "</tbody></table>";
}


 //--------MAIN--------

  // The following connects to the database, returning a database handle (dbh)
  $dbh = db_connect($athomas2_dsn);

  if (isset($_GET['logout'])){
    logOut();
  }

 else if (isset($_GET['sought'])) {
    $table = $_GET['tables']; //returns which table the user wants to search from 
    $sought = $_GET['sought'];
    if ($table=="People") { //search only within people
      displayNames($sought, $dbh, "single");

    } elseif ($table=="Users") {
      displayUsers($sought,$dbh, "single");
    }

    elseif ($table=="Movies") { //search only within media
      displayMovies($sought, $dbh, "single");
    
    } elseif ($table=="Albums") { //search only within media
      displayAlbums($sought, $dbh, "single");

    } elseif ($table=="TVShows") { //search only within media
      displayTVshows($sought, $dbh, "single");

    } elseif ($table=="Songs") { //search only within media
      displaySongs($sought, $dbh, "single");

    } 
    elseif ($table=="All") { //search within media and actors  
      $resultset = array();  
      echo "<h1>Users:</h1>";
      $resultset['1'] = displayUsers($sought, $dbh, "multiple");
      echo "<br><h1>People:</h1>";
      $resultset['2'] = displayNames($sought, $dbh, "multiple");
      echo "<br><h1>Movies:</h1>";
      $resultset['3'] = displayMovies($sought, $dbh, "multiple");
      echo "<br><h1>TV Shows:</h1>";
      $resultset['4'] = displayTVshows($sought, $dbh, "multiple");
      echo "<br><h1>Songs:</h1>";
      $resultset['5'] = displaySongs($sought, $dbh, "multiple");
      echo "<br><h1>Albums:</h1>";
      $resultset['6'] = displayAlbums($sought, $dbh, "multiple");

      $num0s = 0;
      $nummultiples = 0;
      $final = 0;
      for ($i=1; $i <= 6; $i++) { 
        if (gettype($resultset[$i]) == "integer" and $resultset[$i] == 0){
          $num0s++;
        }
        else if (gettype($resultset[$i]) == "integer" and $resultset[$i] == -1){
          $nummultiples++;
        }
        else{
          $final = $resultset[$i];
        }
      }
      if ($num0s == 5 and $nummultiples == 0){
        header ("Location: " . $final);
      }
    }
  }
  else{
    getTrendingMedia($dbh);
    getUsersInCommon($dbh,getUid($dbh,$_SESSION['username']));
  }



?>

