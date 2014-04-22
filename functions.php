

<!--Ashley Thomas and Cathy Bai
HW 4
wmdb-helper-functions.php
This php file contains helper functions that we use in wmdb-search.php
-->

<?php
 //all queries are ordered by name or title to make it easier for the user to find what they're looking for

function getHTMLHead(){
	return '
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Data Form</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

  </head>
  <body>
  
  <script src="https://code.jquery.com/jquery.js"></script>"';
}

function getHTMLFooter(){
	return '  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  </body>
  </html>';
}

function displayMoviePage($dbh,$mid){
	$htmlpage = "";

	$getmovie = "Select * from media where mid = ?";
	$resultset1 = prepared_query($dbh,$getmovie,array($mid));

  while($row = $resultset1->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $title = $row['title'];
    $rating = $row['rating'];
    $dateadded = $row['dateadded'];
    $genre = $row['genre'];
    $length = $row['length'];
  }

//    $htmlpage += getHTMLHead(); 

    $htmlpage += "<h1>$title</h1>";
    $htmlpage += "<p>Genre: $genre --- Length: $length";
    $htmlpage += "<p>Rating: $rating stars";


//    $htmlpage += getHTMLFooter();
    return $htmlpage;

}






 //findMovies returns a list of all the movies. $tables parameter specifies what table the user wants information from. If $table is just Movies, then accounts for the situation when a single movie is retrievd and gives back the full movie page
function findMovies($sought,$dbh,$page,$tables){
  //$movies query gets information on movies given a user's searchword
  $movies = "SELECT tt, title, `release` from movie where title like concat('%',?,'%') order by title";
  $values = array($sought);
  $resultset = prepared_query($dbh,$movies,$values);
  $numrows = $resultset->numRows();
  if ($numrows == 0 && $tables == "Movies") //return nothing if not matches are found
    echo "<b>0 Movies Matched</b><br><br>Sorry, no names match " . htmlspecialchars($sought,ENT_COMPAT,"UTF-8");
    else if ($numrows == 0 && $tables == "Both")
      return;
  else if ($tables == "Movies" && $numrows == 1){
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $tt = $row['tt'];
    }
    getMoviePage($tt,$dbh,$page);
  }
  else {
    echo "<b>$numrows Movies Matched</b><br>";
    echo "<ul>";
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $tt = $row['tt'];
      $title = $row['title'];
      $release = $row['release'];
      echo "<li><a href='$page?tt=$tt'>$title ($release)</a>\n"; //makes the hyperlink for that movie
    }
    echo "</ul>";
  }
 }

//findActors returns a list of all the actors. $tables parameter specifies what table the user wants information from. If $table is just Actors, then accounts for the situation when a single actor is retrieved and gives back the full actor page
function findActors($sought,$dbh,$page,$tables){
  //$actors query gets information on actors given a user's searchword
  $actors = "SELECT nm, name, birthdate from person where name like concat('%',?,'%') order by name";
  $values = array($sought);
  $resultset = prepared_query($dbh,$actors,$values);
  $numrows = $resultset->numRows();
  if ($numrows == 0 && $tables == "Actors")
    echo "<b>0 Actors Matched</b><br><br>Sorry, no names match " . htmlspecialchars($sought,ENT_COMPAT,"UTF-8");
    else if ($numrows == 0 && $tables == "Both")
      return;
  else if ($tables == "Actors" && $numrows == 1){
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $nm = $row['nm'];
    }
    getActorPage($nm,$dbh,$page);
  }
  else {
    echo "<b>$numrows Names Matched</b><br>";
    echo "<ul>";
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $nm = $row['nm'];
      $name = $row['name'];
      $birthdate = $row['birthdate'];
      echo "<li><a href='$page?nm=$nm'>$name ($birthdate)</a>\n"; //makes the hyperlink for that person
    }
    echo "</ul>";
  }
}

//gets the specific (full) actor page for one actor
function getActorPage($nm,$dbh,$page){
  //$actorsmovies query gets the movies that an actor has been in
  $actorsmovies = "SELECT tt, title, `release` from movie where tt in (select tt from credit where credit.nm = ?) order by title";
  //$actorinfo query gets the information of one specific actor
  $actorinfo = "SELECT name, birthdate from person where nm = ?";
  $values = array($nm);
  $resultset1 = prepared_query($dbh,$actorinfo,$values); //gets the actor's specific info
  $resultset2 = prepared_query($dbh,$actorsmovies,$values); //gets the movies the actor has been in

  while($row = $resultset1->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $name = $row['name'];
    $birthdate = $row['birthdate'];
    echo "<p><b>$name</b><br>born on ($birthdate)\n";
  }
  echo "<br><br><em>Filmography</em><ul>";
  while($row = $resultset2->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $tt = $row['tt'];
    $title = $row['title'];
    $release = $row['release'];
    echo "<li><a href='$page?tt=$tt'>$title ($release)</a>\n"; //makes the movies hyperlinks
  }
  echo "</ul>";
  echo "Here's the real <a href='http://www.imdb.com/name/nm$nm'>IMDB entry for $name</a>\n"; //links to the real IMDB site of that actor
}

//gets the specific movie page for one movie
function getMoviePage($tt,$dbh,$page){
  //$movieinfo query gets information on one specific movie, including the director
  $movieinfo = "SELECT title, director, `release` from movie where tt = ?";
  //$moviesactors query gets the actors that have been in the movie specified
  $moviesactors = "SELECT nm, name from person where nm in (select nm from credit where credit.tt = ?) order by name";
  //$actorinfo query gets the information of one specific actor
  $actorinfo = "SELECT name, birthdate from person where nm = ?"; //need this for director
  $values = array($tt);
  $resultset1 = prepared_query($dbh,$movieinfo,$values); //gets the movie's general info, including title, release date, and director
  $resultset2 = prepared_query($dbh,$moviesactors,$values); //gets the cast of the movie

  while($row = $resultset1->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $title = $row['title'];
    $release = $row['release'];
    $director = $row['director'];
    $getdirector = prepared_query($dbh, $actorinfo, array($director)); //gets the name of the director given the nm
    while ($row = $getdirector->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $directorname = $row['name'];
    }    
    echo "<p><b>$title ($release)</b><br>directed by <a href='$page?nm=$director'>$directorname</a>\n"; //makes the director's name a hyperlink
  }
  echo "<br><br><em>Cast</em><ul>";
  while($row = $resultset2->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $nm = $row['nm'];
    $name = $row['name'];
    echo "<li><a href='$page?nm=$nm'>$name</a>\n"; //makes the actors' names hyperlinks
  }
  echo "</ul>";
  echo "Here's the real <a href='http://www.imdb.com/title/tt$tt'>IMDB entry for $title</a>\n";//provides the real IMDB link for the specific movie
}

?>