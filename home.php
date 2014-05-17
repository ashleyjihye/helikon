<?php

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");
$dbh = db_connect($athomas2_dsn);

  checkLogInStatus();
  printPageTop("Home");
  createNavBar($_SERVER['PHP_SELF']);

function displayNames($values, $dbh) {
  $sql = "select * from person where name like concat ('%', ?, '%') order by name";
  $resultset = prepared_query($dbh, $sql, $values);
  $numpeople = $resultset->numRows();
 
  if ($numpeople == 0) {
    echo "0 people found";
  }
  else {
  echo "<table border='1'> <tr> <th>Name</th></tr>";
  
  if ($numpeople == 1) {
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $pid = $row['pid'];
      header ("Location: person.php?pid=" . $pid);
    }
   // echo "<h3>1 name found</h3>";
  }
  else {
    echo "<h3>$numpeople names found</h3>";
  }
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $name = $row['name'];
        $pid = $row['pid'];
        echo "<tr><td><a href= \"person.php?pid=" . $pid . "\">$name</a></td></tr>";
    
    }
    echo "</table>";
}
}

function displayUsers($values,$dbh){
  $sql = "select * from user where name like concat ('%', ?, '%') order by name";
  $resultset = prepared_query($dbh, $sql, $values);
 
 
  $numpeople = $resultset->numRows();
  if ($numpeople == 0) {
    echo "0 users found";
  }
  else {
    echo "<table border='1'><tr><th>Name</th></tr>";
  if($numpeople==1) {
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $uid = $row['uid'];
      header ("Location: user.php?uid=" . $uid);
    }
  }
  else {
    echo "<h3>$numpeople users found</h3>";
  }

  while($row= $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $name = $row['name'];
    $uid=$row['uid'];
    echo "<tr><td><a href= \"user.php?uid=" . $uid . "\">$name</a></td></tr>";
  }
  echo "</table>";
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
    echo "<h3>1 friends found</h3>";
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

function displayMovies($values, $dbh) {
  $sql = "select title, mid, rating, type, genre from media where title like concat('%', ?, '%') and type = 'movie' order by title";
  $resultset = prepared_query($dbh, $sql, $values);
  $numMovies = $resultset->numRows();
  if ($numMovies == 0) {
    echo "0 movies found";
  }
  else {
  echo "<table border='1'><tr><th>Title</th><th>Genre</th></tr>";
  if($numMovies == 1) {
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $mid = $row['mid'];
      header ("Location: media.php?mid=" . $mid);
    }
  }
  else {
    echo"<h3>$numMovies titles found</h3>";
  }
  while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $title = $row['title'];
    $genre = $row['genre'];
    $mid = $row['mid'];
    $type = $row['type'];
    
    echo "<tr><td><a href= \"media.php?mid=" . $mid . "\">$title</a></td><td>" . $genre . "</td></tr>";

  }
  echo "</table>";

}
}

function displayGenres($values, $dbh) {
  $sql = "select title, mid, rating, type, genre from media where genre like concat('%', ?, '%') order by genre";
  $resultset = prepared_query($dbh, $sql, $values);
  $numMedia = $resultset->numRows();
  if ($numMedia == 0) {
    echo "0 titles with that genre found";
  }
  else {
    echo "<table border='1'><tr><th>Title</th><th>Genre</th></tr>";
    if($numMedia == 1) {
     while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $mid = $row['mid'];
      header ("Location: media.php?mid=" . $mid);
    }
  }
  else {
    echo"<h3>$numMedia titles found</h3>";
  }
  while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $title = $row['title'];
    $genre = $row['genre'];
    $mid = $row['mid'];
    $type = $row['type'];
    
    echo "<tr><td><a href= \"media.php?mid=" . $mid . "\">$title</a></td><td>" . $genre . "</td></tr>";

  }
  echo "</table>";

  }
}




function displayTVshows($values, $dbh) {
  $sql = "select * from media where title like concat('%', ?, '%') and type = 'tv' order by title";
  $resultset = prepared_query($dbh, $sql, $values);
  $numShows = $resultset->numRows();
  if ($numShows == 0) {
    echo "0 TV shows found";
  }
  else {
  echo "<table border='1'><tr><th>Title</th><th>Genre</th></tr>";
  if($numShows == 1) {
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $mid = $row['mid'];
      header ("Location: media.php?mid=" . $mid);
    }
  }
  else {
    echo "<h3>$numShows titles found</h3>";
  }
      //display list of movies
  while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $title= $row['title'];
    $genre = $row['genre'];
    $mid = $row['mid'];
    echo "<tr><td><a href= \"media.php?mid=" . $mid . "\">$title</a></td><td>" . $genre . "</td></tr>";
  }
  echo "</table>";
}
}


function displayAlbums($values, $dbh) {
  $sql = "select * from media where title like concat('%', ?, '%') and type = 'album' order by title";
  $resultset = prepared_query($dbh, $sql, $values);
  $numAlbums = $resultset->numRows();
  if ($numAlbums == 0) {
    echo "0 albums found";
  }
  else {
  echo "<table border='1'><tr><th>Title</th><th>Genre</th></tr>";
 
  if($numAlbums == 1) {
     while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $mid = $row['mid'];
      header ("Location: media.php?mid=" . $mid);
    }
  } else {
    echo "<h3>$numAlbums titles found</h3>";
  }
  //display list of movies
  while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $title= $row['title'];
    $genre = $row['genre'];
    $mid = $row['mid'];
  echo "<tr><td><a href= \"media.php?mid=" . $mid . "\">$title</a></td><td>" . $genre . "</td></tr>";
  }
  echo "</table>";
}
}

function displaySongs($values, $dbh) {
  $sql = "select * from media where title like concat('%', ?, '%') and type = 'song' order by title";
  $resultset = prepared_query($dbh, $sql, $values);
  $numSongs = $resultset->numRows();
  if ($numSongs == 0) {
    echo "0 songs found";
  }
  else {
  echo "<table border='1'><tr><th>Title</th><th>Genre</th></tr>";
  if($numSongs == 1) {
     while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $mid = $row['mid'];
      header ("Location: media.php?mid=" . $mid);
    }
  } else {
    echo "<h3>$numSongs titles found</h3>";
  }
  //display list of movies
  while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $title= $row['title'];
    $genre = $row['genre'];
    $mid = $row['mid'];
  echo "<tr><td><a href= \"media.php?mid=" . $mid . "\">$title</a></td><td>" . $genre . "</td></tr>";
  }
  echo "</table>";
  }
}


function getTrendingMedia($dbh){
    echo "<h1>Trending Media</h1>";
    $sql = "select likes.mid as mid, count(mid) as count, title from likes inner join media using (mid) group by mid order by count(mid) desc limit 20";    
    $resultset = query($dbh,$sql);
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $title= $row['title'];
      $count = $row['count'];
      $mid = $row['mid'];
      if($count==1) {
	echo"<a href=\"media.php?mid=" . $mid . "\">$title (" . $count . " person likes this)</a><br><br>";
      }
      else {
      echo "<a href= \"media.php?mid=" . $mid . "\">$title (" . $count . " people like this)</a><br><br>";
      }
    }
}


 //--------MAIN--------

  // The following connects to the database, returning a database handle (dbh)
  $dbh = db_connect($athomas2_dsn);

  if (isset($_GET['logout'])){
    logOut();
  }

  else if (isset($_GET['friends'])){
    displayFriends($dbh);
  }

 else if (isset($_GET['sought'])) {
    $table = $_GET['tables']; //returns which table the user wants to search from 
    $sought = $_GET['sought'];
    if ($table=="People") { //search only within people
      displayNames($sought, $dbh);

    } elseif ($table=="Users") {
      displayUsers($sought,$dbh);
    }

    elseif ($table=="Movies") { //search only within media
      displayMovies($sought, $dbh);
    
    } elseif ($table=="Albums") { //search only within media
      displayAlbums($sought, $dbh);

    } elseif ($table=="TVShows") { //search only within media
      displayTVshows($sought, $dbh);

    } elseif ($table=="Songs") { //search only within media
      displaySongs($sought, $dbh);

    } elseif ($table=="Genres"){
      displayGenres($sought,$dbh);
    }
    elseif ($table=="All") { //search within media and actors    
      echo "<h1>Users:</h1><br>";
      displayUsers($sought, $dbh);
      echo "<h1>People:</h1><br>";
      displayNames($sought, $dbh);
      echo "<h1>Movies:</h1><br>";
      displayMovies($sought, $dbh);
      echo "<h1>TV Shows:</h1><br>";
      displayTVshows($sought, $dbh);
      echo "<h1>Songs:</h1><br>";
      displaySongs($sought, $dbh);
      echo "<h1>Albums:</h1><br>";
      displayAlbums($sought, $dbh);
      echo "<h1>Genres:</h1><br>";
      displayGenres($sought, $dbh);

    }
  }
  else{
    getTrendingMedia($dbh);
  }



?>

