<?php

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");
$dbh = db_connect($athomas2_dsn);

  checkLogInStatus();
  printPageTop("Home");
  createNavBar($_SERVER['PHP_SELF']);

  $page = "http://cs.wellesley.edu/~athomas2/helikon/";

function displayNames($values, $dbh) {
  global $page;
  $sql = "select * from person where name like concat ('%', ?, '%')";
  $resultset = prepared_query($dbh, $sql, $values);
  $numpeople = $resultset->numRows();
  
  if ($numpeople == 1) {
    echo "<h3>1 name found</h3>";
    
    $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
    $name = $row['name'];
    $pid = $row['pid'];
    echo "<a href= \"" . $page . "person.php?pid=" . $pid . "\">$name</a><br><br>";

  }
  else {
    echo "<h3>$numpeople names found</h3>";
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $name = $row['name'];
        $pid = $row['pid'];
        echo "<a href= \"" . $page . "person.php?pid=" . $pid . "\">$name</a><br><br>";
    }
  }
}

function displayUsers($values,$dbh){
  global $page;
  $sql = "select * from user where name like concat ('%', ?, '%')";
  $resultset = prepared_query($dbh, $sql, $values);
  $numpeople = $resultset->numRows();
  
  if ($numpeople == 1) {
    echo "<h3>1 user found</h3>";
    
    $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
    $name = $row['name'];
    $uid = $row['uid'];
    echo "<a href= \"" . $page . "user.php?uid=" . $uid . "\">$name</a><br><br>";

  }
  else {
    echo "<h3>$numpeople users found</h3>";
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $name = $row['name'];
        $uid = $row['uid'];
        echo "<a href= \"" . $page . "user.php?uid=" . $uid . "\">$name</a><br><br>";
    }
  }
}

function displayFriends($dbh){
  global $page;
  $sql = "select uid from user where username = ?";
  $resultset = prepared_query($dbh, $sql, $_SESSION['username']);
  $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
  $uid = $row['uid'];
  $sql = "select user.uid, name from user, friends where user.uid = friendid and friends.uid = ?";
  $resultset = prepared_query($dbh, $sql, $uid);
  $numpeople = $resultset->numRows();
  
  if ($numpeople == 1) {
    echo "<h3>1 friends found</h3>";
    
    $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
    $name = $row['name'];
    $uid = $row['uid'];
    echo "<a href= \"" . $page . "user.php?uid=" . $uid . "\">$name</a><br><br>";

  }
  else {
    echo "<h3>$numpeople friends found</h3>";
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $name = $row['name'];
        $uid = $row['uid'];
        echo "<a href= \"" . $page . "user.php?uid=" . $uid . "\">$name</a><br><br>";
    }
  }
}

function displayMovies($values, $dbh) {
  $sql = "select title, mid, rating, type, genre from media where title like concat('%', ?, '%') and type = 'movie'";
  $resultset = prepared_query($dbh, $sql, $values);
  $numMovies = $resultset->numRows();

  if($numMovies == 1) {
    echo "<h3>1 title found</h3>";

        $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
        $mid = $row['mid'];
        displayTitle($mid, $dbh);

      } else {
        echo "<h3>$numMovies titles found</h3>";
       
        //display list of movies
        while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
          $title= $row['title'];
          $mid = $row['mid'];
         displayTitle($mid, $dbh);
        }
  }
}



function displayTVshows($values, $dbh) {
  $sql = "select * from media where title like concat('%', ?, '%') and type = 'tv'";
  $resultset = prepared_query($dbh, $sql, $values);
  $numShows = $resultset->numRows();

  if($numShows == 1) {
    echo "<h3>1 title found</h3>";

        $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
        $mid = $row['mid'];
        displayTitle($mid, $dbh);

      } else {
        echo "<h3>$numShows titles found</h3>";
       
        //display list of movies
        while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
          $title= $row['title'];
          $mid = $row['mid'];
         displayTitle($mid, $dbh);
        }
  }
}


function displayAlbums($values, $dbh) {
  $sql = "select * from media where title like concat('%', ?, '%') and type = 'album'";
  $resultset = prepared_query($dbh, $sql, $values);
  $numAlbums = $resultset->numRows();

  if($numAlbums == 1) {
    echo "<h3>1 title found</h3>";

        $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
        $mid = $row['mid'];
        displayTitle($mid, $dbh);

      } else {
        echo "<h3>$numAlbums titles found</h3>";
       
        //display list of movies
        while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
          $title= $row['title'];
          $mid = $row['mid'];
         displayTitle($mid, $dbh);
        }
  }
}


function displaySongs($values, $dbh) {
  $sql = "select * from media where title like concat('%', ?, '%') and type = 'song'";
  $resultset = prepared_query($dbh, $sql, $values);
  $numSongs = $resultset->numRows();

  if($numSongs == 1) {
    echo "<h3>1 title found</h3>";

        $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
        $mid = $row['mid'];
        displayTitle($mid, $dbh);

      } else {
        echo "<h3>$numSongs titles found</h3>";
       
        //display list of movies
        while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
          $title= $row['title'];
          $mid = $row['mid'];
         displayTitle($mid, $dbh);
        }
  }
}

function displayTitle($mid, $dbh) {
    global $page;
    //query 1: movie stats
    //title, release, director from a given tt
    $sql1 = "select * from media where mid = ?";
    
    //execute first query
    $resultset1 = prepared_query($dbh, $sql1, $mid);

    //get title, release, and director
    $detailrow = $resultset1->fetchRow(MDB2_FETCHMODE_ASSOC);
    $title = $detailrow['title'];
    $rating = $detailrow['rating'];
    $genre = $detailrow['genre'];
    $length= $detailrow['length'];
    $type = $detailrow['type'];

    echo "<a href= \"" . $page . "media.php?mid=" . $mid . "\">$title ($genre)</a><br><br>";
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

  if (isset($_GET['sought'])) {
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

    }elseif ($table=="All") { //search within media and actors    
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

    }
  }



?>


</body>
</html>
