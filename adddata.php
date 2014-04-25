<!-- Ashley Thomas and Sasha Levy
  Helikon
  adddata.php
  
  To Do:
  add more helpful messages to the user and escape everything when adding stuff
  clean up! better variable names
  maybe make things into methods

  right now adding an album means:
    the album can previously exist
    the person can previously exist
    the songs being added must be new songs
    contributions will be made for the album and each song

 -->

<?php

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");

  checkLogInStatus();   
  printPageTop("Add Data");
  createNavBar("home.php");
?>

  <script>
  $(document).ready(function (){
  $("#type").change(function() { 
    var v = $(this).val();
    if (v == "person"){
      $("#person").show();
      $("#moviesandtv").hide();
      $("#song").hide();
      $("#albumsongs").hide();
    }
    else if (v == "song"){
      $("#person").hide();
      $("#moviesandtv").hide();
      $("#song").show();
      $("#albumsongs").hide();
    }
    else if (v == "moviesandtv"){
      $("#person").hide();
      $("#moviesandtv").show();
      $("#song").hide();
      $("#albumsongs").hide();
    }
    else if (v == "mediaalbum"){
      $("#person").hide();
      $("#moviesandtv").hide();
      $("#song").hide();
      $("#albumsongs").show();
    }
  });
  $("#type").change();
});

</script>

  <h2 class= "title" >Add Data to the Database </h2>
  <div id="form">
  <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
  <select name="type" id="type">
  <option selected="selected" value="person">Person
  <option value="song">Song
  <option value="mediaalbum">Album
  <option value="moviesandtv">Movies/TV
  </select>
  <div id="person">
  <p>Name <input type="text" name="name">

  <p>Media:
    <p><input type="text" name="media1">
    <p><input type="text" name="media2">
    <p><input type="text" name="media3">
    <p><input type="text" name="media4">
    <p><input type="text" name="media5">
    <p><input type="text" name="media6">

  </div>


  <div id="song">
  <p>Title <input type="text" name="songtitle">
  <p>Artist <input type="text" name="songartist">
  <p>Length <input type="text" name="songlength">
  <p>Album <input type="text" name="songalbum">
  <p>Genre <input type="text" name="songgenre">
  </div>


  <div id="albumsongs">
  <p>Title <input type="text" name="albumtitle">
  <p>Artist <input type="text" name="albumartist">
  <p>Length <input type="text" name="albumlength">
  <p>Genre <input type="text" name="albumgenre">   
  <p>Songs:
  <table style="padding-bottom:20px">
  <tr><th>Song</th><th>Length</th></tr>
  <tr><td><input type="text" name="song1"></td><td><input type="text" name="length1"></td></tr>
  <tr><td><input type="text" name="song2"></td><td><input type="text" name="length2"></td></tr>
  <tr><td><input type="text" name="song3"></td><td><input type="text" name="length3"></td></tr>
  <tr><td><input type="text" name="song4"></td><td><input type="text" name="length4"></td></tr>
  <tr><td><input type="text" name="song5"></td><td><input type="text" name="length5"></td></tr>
  <tr><td><input type="text" name="song6"></td><td><input type="text" name="length6"></td></tr>
  <tr><td><input type="text" name="song7"></td><td><input type="text" name="length7"></td></tr>
  <tr><td><input type="text" name="song8"></td><td><input type="text" name="length8"></td></tr>
  <tr><td><input type="text" name="song9"></td><td><input type="text" name="length9"></td></tr>
  <tr><td><input type="text" name="song10"></td><td><input type="text" name="length10"></td></tr>
  <tr><td><input type="text" name="song11"></td><td><input type="text" name="length11"></td></tr>
  <tr><td><input type="text" name="song12"></td><td><input type="text" name="length12"></td></tr>
  <tr><td><input type="text" name="song13"></td><td><input type="text" name="length13"></td></tr>
  <tr><td><input type="text" name="song14"></td><td><input type="text" name="length14"></td></tr>
  </table>
  </div>
  <div id="moviesandtv">
  <select name="mediatype">
  <option value="tv">TV
  <option value="movie">Movie
  </select>

  <p>Title <input type="text" name="title">
  <p>Length <input type="text" name="length">
  <p>Genre <input type="text" name="genre">  

  <p>Actors:
    <p><input type="text" name="mediaactor1">
    <p><input type="text" name="mediaactor2">
    <p><input type="text" name="mediaactor3">
    <p><input type="text" name="mediaactor4">
    <p><input type="text" name="mediaactor5">
    <p><input type="text" name="mediaactor6">

  </div>
  <input type="submit">
  <input type="reset">
  </form></div>
  <br>
  
  <?php
  
  $page = $_SERVER['PHP_SELF'];
// The following loads the Pear MDB2 class and our functions

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("functions.php");

// The following defines the data source name (username, password,
// host and database).

require_once('athomas2-dsn.inc');

// The following connects to the database, returning a database handle (dbh)

$dbh = db_connect($athomas2_dsn);
$person = "Insert into person (name) values (?)";
$media = "Insert into media (title, genre, length, type, albumid, dateadded) values (?,?,?,?,?,?)";
$contribution = "Insert into contribution values (?,?)";
$findmedia = "Select * from media where title = ?";
$findsongalbum = "Select * from media where title = ? and albumid = ?";
$findalbum = "select * from media where title = ? and type = 'album'";
$findperson = "select * from person where name = ?";
$findcontribution = "select * from contribution where pid = ? and mid = ?";
$findsongs = "select * from media where albumid = ?";

if (empty($_REQUEST)){
  echo "Please enter either a person or a piece of media to add to the database!";
}
else if (isset($_REQUEST['pickperson'])){
  //Do this later...
  
  
}

else if (!empty($_REQUEST['type'])){
  $type = $_REQUEST['type'];



  if ($type == 'person'){
    if ($_REQUEST['name'] == ""){
      echo "Please enter a valid name for the person.";
    }
    else{
      $name = $_REQUEST['name'];
      $values = array($name);
      $personexists = prepared_query($dbh,$findperson,$values);
      $numrows = $personexists->numRows();
      if ($numrows != 0){
        echo "<p>There's already someone with that name.";
        $getperson = prepared_query($dbh,$findperson,array($name));
        while($row3 = $getperson->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $personid = $row3['pid'];
        }
      }
      else {
        $addperson = prepared_query($dbh,$person,$values);
        echo "<p>Successfully added that person to the database.";
        $getpersonid = query($dbh,"select last_insert_id()");
        while($row3 = $getpersonid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $personid = $row3['last_insert_id()'];
        }
      }

      $mediacount = 1;
      while ($mediacount <= 6 and $_REQUEST['media' . $mediacount] != ""){
        $currentmedia = $_REQUEST['media' . $mediacount];
        $checkmedia = prepared_query($dbh,$findmedia,array($currentmedia));
        $numrows2 = $checkmedia->numRows();
        if ($numrows2 == 0){
          echo "Sorry, media in slot " . $mediacount . " doesn't exist. Please add the media separately first.";
        }
        else {
          while($row3 = $checkmedia->fetchRow(MDB2_FETCHMODE_ASSOC)) {
          $mediaid = $row3['mid'];
          }
          $contributionexists = prepared_query($dbh,$findcontribution,array($personid,$mediaid));
          $numrows = $contributionexists->numRows();
          if ($numrows == 0){
            $addactorcontribution = prepared_query($dbh,$contribution,array($personid,$mediaid));
            echo "Successfully added media in spot" . $mediacount . "<p>";
          }
        }
        $mediacount++;
      }
    }
  }
  
  else if ($type == 'moviesandtv'){
    if ($_REQUEST['title'] == ""){
      echo "Please enter a valid title for the piece of media you want to add.";
    }
    else{
      $title = $_REQUEST['title'];
      $values1 = array($title);
      $mediaexists = prepared_query($dbh,$findmedia,$values1);
      $numrows1 = $mediaexists->numRows();
      if ($numrows1 == 0){
        $genre = $_REQUEST['genre'];
        $type = $_REQUEST['mediatype'];
        $length = $_REQUEST['length'];
        $datetime = query($dbh,"Select now()");
        while ($row1 = $datetime->fetchRow(MDB2_FETCHMODE_ASSOC)){
          $thetime = $row1['now()'];
        }
        $values3 = array($title,$genre,$length,$type,NULL,$thetime);
        $addmedia = prepared_query($dbh,$media,$values3);
        echo "<p>Successfully added that piece of media to the database.";
        $getmediaid = query($dbh,"select last_insert_id()");
        while($row3 = $getmediaid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $mediaid = $row3['last_insert_id()'];
        }
      }
      else {
        $getmedia = prepared_query($dbh,$findmedia,array($title));
        while($row3 = $getmedia->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $mediaid = $row3['mid'];
        }
      }

      $actorcount = 1;
      while ($actorcount <= 6 and $_REQUEST['mediaactor' . $actorcount] != ""){
        $currentactor = $_REQUEST['mediaactor' . $actorcount];

        $checkactor = prepared_query($dbh,$findperson,array($currentactor));
        $numrows2 = $checkactor->numRows();
        if ($numrows2 == 0){
          $addperson = prepared_query($dbh,$person,array($currentactor));
          $getpersonid = query($dbh,"select last_insert_id()");
          while($row3 = $getpersonid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
          $personid = $row3['last_insert_id()'];
          }
        }
        else {
          while($row3 = $checkactor->fetchRow(MDB2_FETCHMODE_ASSOC)) {
          $personid = $row3['pid'];
          }
        }
        $contributionexists = prepared_query($dbh,$findcontribution,array($personid,$mediaid));
        $numrows = $contributionexists->numRows();
        if ($numrows == 0){
          $addactorcontribution = prepared_query($dbh,$contribution,array($personid,$mediaid));
          echo "Successfully added person in spot" . $actorcount . "<p>";
        }
        $actorcount++;
      }
    }
  }

  //adds a song with an album that may or may not exist and a person that may or may not exist.
  //doesn't allow a song with an associated album have same titles for two different people.
  else if ($type == "song"){
    if ($_REQUEST['songartist'] == ""){
      echo "Sorry, please enter a valid artist.";
    }
    else{     
    //get the time and input variables
      $datetime = query($dbh,"Select now()");
      while ($row1 = $datetime->fetchRow(MDB2_FETCHMODE_ASSOC)){
        $thetime = $row1['now()'];
      }
      $title = $_REQUEST['songtitle'];
      $genre = $_REQUEST['songgenre'];
      $length = $_REQUEST['songlength'];
      $artist = $_REQUEST['songartist'];
      $albumid = NULL;

      //check to see if artist is already in database; if not, add them
      $checkartist = prepared_query($dbh,$findperson,array($artist));
      $numrows2 = $checkartist->numRows();
      if ($numrows2 == 0){
        $addperson = prepared_query($dbh,$person,array($artist));
        $getpersonid = query($dbh,"select last_insert_id()");
        while($row3 = $getpersonid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $personid = $row3['last_insert_id()'];
        }
      }
      else {
        while($row3 = $checkartist->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $personid = $row3['pid'];
        }
      }

      //check to see if album is already in database; if not, add it and add contribution between artist and album
      if (isset($_REQUEST['songalbum'])){
        $album = $_REQUEST['songalbum'];
        $values2 = array($album);
        $albumexists = prepared_query($dbh,$findalbum,$values2);
        if ($albumexists->numRows() != 0){
          while($row = $albumexists->fetchRow(MDB2_FETCHMODE_ASSOC)) {
            $albumid = $row['mid'];
          }
        }
        else{
          $values2 = array($album,$genre,$length,"album",NULL,$thetime);
          $addalbum = prepared_query($dbh,$media,$values2);
          $getalbumid = query($dbh,"select last_insert_id()");
          while($row3 = $getalbumid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
          $albumid = $row3['last_insert_id()'];
          }

          $contributionexists = prepared_query($dbh,$findcontribution,array($personid,$albumid));
          $numrows = $contributionexists->numRows();
          if ($numrows == 0){
            $addalbumcontribution = prepared_query($dbh,$contribution,array($personid,$albumid));
          }

        }
      }

      //check to see if album and song pair exist. if not, add song, and add contribution to person and song.
      $songalbumexists = prepared_query($dbh,$findsongalbum,array($title,$albumid));
      $numrows1 = $songalbumexists->numRows();
      if ($numrows1 != 0){
        echo "<p>Sorry, there's already a song with that title and associated album.";
      }
      else{
        $values3 = array($title,$genre,$length,$type,$albumid,$thetime);
        $addmedia = prepared_query($dbh,$media,$values3);
        echo "<p>Successfully added this song to the database.";
        $getsongid = query($dbh,"select last_insert_id()");
        while($row3 = $getsongid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $songid = $row3['last_insert_id()'];
        }
        $contributionexists = prepared_query($dbh,$findcontribution,array($personid,$songid));
        $numrows = $contributionexists->numRows();
        if ($numrows == 0){
          $addsongcontribution = prepared_query($dbh,$contribution,array($personid,$songid));
        }

      }
    }
  }

  else if ($type == 'mediaalbum'){
    if ($_REQUEST['albumtitle'] == "" or $_REQUEST['albumartist'] == ""){
      echo "Please enter a valid album title and album artist.";
    }
    else{
      $datetime = query($dbh,"Select now()");
      while ($row1 = $datetime->fetchRow(MDB2_FETCHMODE_ASSOC)){
        $thetime = $row1['now()'];
      }
      $artist = $_REQUEST['albumartist'];
      $checkartist = prepared_query($dbh,$findperson,array($artist));
      $numrows2 = $checkartist->numRows();
      if ($numrows2 == 0){
        $addperson = prepared_query($dbh,$person,array($artist));
        $getpersonid = query($dbh,"select last_insert_id()");
        while($row3 = $getpersonid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $personid = $row3['last_insert_id()'];
        }
      }
      else {
        while($row3 = $checkartist->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $personid = $row3['pid'];
        }
      }
      $title = $_REQUEST['albumtitle'];
      $values7 = array($title);
      $albumexists = prepared_query($dbh,$findalbum,$values7);
      $numrows1 = $albumexists->numRows();
      if ($numrows1 != 0){
        echo "<p>There's already an album with that title.";
        while($row3 = $albumexists->fetchRow(MDB2_FETCHMODE_ASSOC)) {
          $genre = $row3['genre'];
        }
      }
      else {
        $genre = $_REQUEST['albumgenre'];
        $type = "album";
        $length = $_REQUEST['albumlength'];
        $albumid = NULL;
        $values3 = array($title,$genre,$length,$type,$albumid,$thetime);
        $addmedia = prepared_query($dbh,$media,$values3);
        echo "<p>Successfully added the album to the database.";
      }      
      $values7 = array($title);
      $albumexists = prepared_query($dbh,$findalbum,$values7);
      $numrows1 = $albumexists->numRows();
      while($row3 = $albumexists->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $genre = $row3['genre'];
        $albumid = $row3['mid'];
      }
      $contributionexists = prepared_query($dbh,$findcontribution,array($personid,$albumid));
      $numrows = $contributionexists->numRows();
      if ($numrows == 0){
        $addcontribution = prepared_query($dbh,$contribution,array($personid,$albumid));
      }
      $songcount = 1;
      while ($songcount <= 14 and $_REQUEST['song' . $songcount] != ""){

        $values6 = array($_REQUEST['song' . $songcount],$genre,$_REQUEST['length' . $songcount],"song",$albumid,$thetime);
        $addmedia = prepared_query($dbh,$media,$values6);
        $getsongid = query($dbh,"select last_insert_id()");
        while($row3 = $getsongid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $songid = $row3['last_insert_id()'];
        }
        $contributionexists = prepared_query($dbh,$findcontribution,array($personid,$songid));
        $numrows = $contributionexists->numRows();
        if ($numrows == 0){
         $addsongcontribution = prepared_query($dbh,$contribution,array($personid,$songid));
        }
        $songcount++;
      }
    }
  }
}
?>
  
  </body>
  </html>