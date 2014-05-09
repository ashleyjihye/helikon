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


    $("#myTab a[href='#song']").click(function (e) {
    e.preventDefault()
    $(this).tab('show')
  })

  $("#myTab a[href='#album']").click(function (e) {
    e.preventDefault()
    $(this).tab('show')
  })

  $("#myTab a[href='#moviesandtv']").click(function (e) {
    e.preventDefault()
    $(this).tab('show')
  })

  $("#myTab a[href='#actor']").click(function (e) {
    e.preventDefault()
    $(this).tab('show')
  })

  $("#myTab a[href='#info']").click(function (e) {
    e.preventDefault()
    $(this).tab('show')
  })

});

</script>

  <h2 class= "title" >Add Data to the Database </h2>


<div id="myTab">
<ul class="nav nav-tabs">
  <li class="active"><a href="#song">Song</a></li>
  <li><a href="#album">Album</a></li>
  <li><a href="#moviesandtv">Movies/TV</a></li>
  <li><a href="#actor">Actor</a></li>
  <li><a href="#info">Info</a></li>
</ul>
</div>


<div class="tab-content">
  <div class="tab-pane fade in active" id="song">
    <br>
      <form id="songform" method="get" action="<?php echo $_SERVER['PHP_SELF']?>">
    <p>Title <input required type="text" name="songtitle">
  <p>Artist <input required type="text" name="songartist">
  <p>Length <input type="text" name="songlength">
  <p>Album <input type="text" name="songalbum">
  <p>Genre <input required type="text" name="songgenre">
    <input type="hidden" name="type" value="song">
    <br><br>
    <input type="submit">
  <input type="reset">
  </form>
  </div>


  <div class="tab-pane fade" id="album">
    <br>
    <form id="albumform" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
<input type="hidden" name="type" value="album">
    <p>Title <input required type="text" name="albumtitle">
  <p>Artist <input required type="text" name="albumartist">
  <p>Length <input type="text" name="albumlength">
  <p>Genre <input required type="text" name="albumgenre">   
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
  <br><br>
  <input type="submit">
  <input type="reset">
  </form>
</div>


  <div class="tab-pane fade" id="moviesandtv">
    <br>
     <form id="moviesandtvform" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
      <input type="hidden" name="type" value="moviesandtv">
  Type: <select name="mediatype">
  <option value="tv">TV
  <option value="movie">Movie
  </select>
  <p>Title <input required type="text" name="title">
  <p>Length <input type="text" name="length">
  <p>Genre <input required type="text" name="genre">  
  <p>Actors:
    <p><input type="text" name="mediaactor1">
    <p><input type="text" name="mediaactor2">
    <p><input type="text" name="mediaactor3">
    <p><input type="text" name="mediaactor4">
    <p><input type="text" name="mediaactor5">
    <p><input type="text" name="mediaactor6">
      <br><br>
    <input type="submit">
    <input type="reset">
    </form>
    </div>


  <div class="tab-pane fade" id="actor">
    <br>
     <form id="actorform" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
      <input type="hidden" name="type" value="person">
    <p>Name <input required type="text" name="name">
  <p>Media:
  <table style="padding-bottom:20px">
  <tr><th>Title</th><th>Type</th></tr>
    <tr><td><input type="text" name="media1"></td>
      <td><select name="personmediatype1" id="personmediatype">
      <option selected="selected" value="">None
      <option value="movie">Movie
      <option value="tv">TV
      </select></td></tr>
    <tr><td><input type="text" name="media2"></td>
      <td><select name="personmediatype2" id="personmediatype">
      <option selected="selected" value="">None
      <option value="movie">Movie
      <option value="tv">TV
      </select></td></tr>
    <tr><td><input type="text" name="media3"></td>
      <td><select name="personmediatype3" id="personmediatype">
      <option selected="selected" value="">None
      <option value="movie">Movie
      <option value="tv">TV
      </select></td></tr>
    <tr><td><input type="text" name="media4"></td>
      <td><select name="personmediatype4" id="personmediatype">
      <option selected="selected" value="">None
      <option value="movie">Movie
      <option value="tv">TV
      </select></td></tr>
    <tr><td><p><input type="text" name="media5"></td>
      <td><select name="personmediatype5" id="personmediatype">
      <option selected="selected" value="">None
      <option value="movie">Movie
      <option value="tv">TV
      </select></td></tr>
    <tr><td><p><input type="text" name="media6"></td>
      <td><select name="personmediatype6" id="personmediatype">
      <option selected="selected" value="">None
      <option value="movie">Movie
      <option value="tv">TV
      </select></td></tr>
    </table>
    <br><br>
    <input type="submit">
    <input type="reset">
    </form>
  </div>

  <div class="tab-pane fade" id="info">
    <br>
    Welcome to this page! 
    <br>To add a song, go to the Song tab and enter the song's title, artist, and genre.
    <br>To add an album, go to the Album tab and enter the album title, the artist, the genre, and any songs that are on the album.
    <br>To add a movie or tv show, go to the Movie/TV tab. Select either TV or Movie, then enter the title of the media, as well as the genre and any actors that are in it.
    <br>To add multiple movies or tv shows to an actor, go to the Actor tab. Enter the person's name, and then any movies or TV shows they are in. If there are duplicates of any tv shows and movies, you will need to select the type of media (TV show or movie) that you are trying to add.
    <br><br>Types of genres (both movies/tv and music): <br>'action','comedy','adventure','documentary','drama','mystery','reality',<br>'sitcom','anime','children','classic','faith','foreign','horror','independent',<br>'musical','romance','scifi','fantasy','romance','thriller','medical','procedural','hiphop',<br>'pop','classical','jazz','rap','country','alternative','faith','rock','blues','children','dance','electronic',<br>'easy listening','r&b','soul','reggae','metal','soundtrack','foreign','indie','kpop','dubstep'
  </div>


</div>

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

function getUid($dbh, $username) {
  $sql = "select uid from user where username = ?";
  $resultset = prepared_query($dbh, $sql, $username);
  $detailrow = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
  $uid = $detailrow['uid'];
  return $uid;
}
// The following connects to the database, returning a database handle (dbh)

$dbh = db_connect($athomas2_dsn);
$person = "Insert into person (name,addedby) values (?,?)";
$media = "Insert into media (title, genre, length, type, albumid, dateadded,addedby, rating) values (?,?,?,?,?,?,?,0)";
$contribution = "Insert into contribution values (?,?)";
$findmedia = "Select * from media where title = ?";
$findmediawithtype = "select * from media where title = ? and type = ?";
$findmediausingmid = "select * from media where mid = ?";
$findsongalbum = "Select * from media where title = ? and albumid = ?";
$findalbum = "select * from media where title = ? and type = 'album'";
$findperson = "select * from person where name = ?";
$findcontribution = "select * from contribution where pid = ? and mid = ?";
$findsongs = "select * from media where albumid = ?";
$findmediausingpid = "select mid, pid, genre from media inner join contribution using (mid) where title = ? and type = ? and pid = ?";
$findmediausingname = "select mid, pid from media inner join contribution using (mid) inner join person using (pid) where title = ? and type = ? and name = ?";

$uid = getUid($dbh,$_SESSION['username']);

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
      $name = htmlspecialchars($_REQUEST['name']);
      $values = array($name);
      $personexists = prepared_query($dbh,$findperson,$values);
      $numrows = $personexists->numRows();
      if ($numrows != 0){
        echo "<p>There's already someone named " . $name .".";
        $getperson = prepared_query($dbh,$findperson,array($name));
        while($row3 = $getperson->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $personid = $row3['pid'];
        }
      }
      else {
        $addperson = prepared_query($dbh,$person,array($name,$uid));
        echo "<p>Successfully added " . $name . " to the database.";
        $getpersonid = query($dbh,"select last_insert_id()");
        while($row3 = $getpersonid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $personid = $row3['last_insert_id()'];
        }
      }

      $mediacount = 1;
      while ($mediacount <= 6 and $_REQUEST['media' . $mediacount] != ""){
        $currentmedia = htmlspecialchars($_REQUEST['media' . $mediacount]);
        $currenttype = htmlspecialchars($_REQUEST['personmediatype' . $mediacount]);
        if ($currenttype == ""){
          $checkmedia = prepared_query($dbh,$findmedia,array($currentmedia));
        }
        else{
          $checkmedia = prepared_query($dbh,$findmediawithtype,array($currentmedia,$currenttype));
        }
        $numrows2 = $checkmedia->numRows();
        if ($numrows2 == 0){
          echo "Sorry, the media \"" . $currentmedia . "\" doesn't exist. Please add the media separately first.";
        }
        else if ($numrows2 != 1){
          echo "Sorry, searching for \"" . $currentmedia . "\" returned more than one result. Please enter more information about the piece of media.";
        }
        else {
          while($row3 = $checkmedia->fetchRow(MDB2_FETCHMODE_ASSOC)) {
          $mediaid = $row3['mid'];
          }
          $contributionexists = prepared_query($dbh,$findcontribution,array($personid,$mediaid));
          $numrows = $contributionexists->numRows();
          if ($numrows == 0){
            $addactorcontribution = prepared_query($dbh,$contribution,array($personid,$mediaid));
            echo "Successfully added \"" . $currentmedia . "\" to the database.<p>";
          }
        }
        $mediacount++;
      }
    }
  }
  
  else if ($type == 'moviesandtv'){
    if (htmlspecialchars($_REQUEST['title']) == ""){
      echo "Please enter a valid title for the piece of media you want to add.";
    }
    else{
      $title = htmlspecialchars($_REQUEST['title']);
      $type = htmlspecialchars($_REQUEST['mediatype']);
      $values1 = array($title,$type);
      $mediaexists = prepared_query($dbh,$findmediawithtype,$values1);
      $numrows1 = $mediaexists->numRows();
      if ($numrows1 == 0){
        $genre = htmlspecialchars($_REQUEST['genre']);
        $length = htmlspecialchars($_REQUEST['length']);
        $datetime = query($dbh,"Select now()");
        while ($row1 = $datetime->fetchRow(MDB2_FETCHMODE_ASSOC)){
          $thetime = $row1['now()'];
        }
        $values3 = array($title,$genre,$length,$type,NULL,$thetime,$uid);
        $addmedia = prepared_query($dbh,$media,$values3);
        echo "<p>Successfully added \"" . $title . "\" to the database.";
        $getmediaid = query($dbh,"select last_insert_id()");
        while($row3 = $getmediaid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $mediaid = $row3['last_insert_id()'];
        }
      }
      else {
        $getmedia = prepared_query($dbh,$findmediawithtype,array($title,$type));
        while($row3 = $getmedia->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $mediaid = $row3['mid'];
        }
        echo "<p>The media \"" . $title . "\" already existed.";
      }

      $actorcount = 1;
      while ($actorcount <= 6 and htmlspecialchars($_REQUEST['mediaactor' . $actorcount]) != ""){
        $currentactor = htmlspecialchars($_REQUEST['mediaactor' . $actorcount]);

        $checkactor = prepared_query($dbh,$findperson,array($currentactor));
        $numrows2 = $checkactor->numRows();
        if ($numrows2 == 0){
          $addperson = prepared_query($dbh,$person,array($currentactor,$uid));
          $getpersonid = query($dbh,"select last_insert_id()");
          while($row3 = $getpersonid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
          $personid = $row3['last_insert_id()'];
          }
          echo "<p>Successfully added " . $currentactor . " to the database.<p>";
        }
        else {
          while($row3 = $checkactor->fetchRow(MDB2_FETCHMODE_ASSOC)) {
          $personid = $row3['pid'];
          }
          echo "<p>" . $currentactor . " is already in the database.<p>";
        }
        $contributionexists = prepared_query($dbh,$findcontribution,array($personid,$mediaid));
        $numrows = $contributionexists->numRows();
        if ($numrows == 0){
          $addactorcontribution = prepared_query($dbh,$contribution,array($personid,$mediaid));
          echo "<p>Successfully added \"" . $title . "\" and " . $currentactor . " together to the database.<p>";
        }
        else{

        }
        $actorcount++;
      }
    }
  }

  //adds a song with an album that may or may not exist and a person that may or may not exist.
  //doesn't allow a song with an associated album have same titles for two different people.
  else if ($type == "song"){
    if (htmlspecialchars($_REQUEST['songartist']) == ""){
      echo "Sorry, please enter a valid artist.";
    }
    else{     
    //get the time and input variables
      $datetime = query($dbh,"Select now()");
      while ($row1 = $datetime->fetchRow(MDB2_FETCHMODE_ASSOC)){
        $thetime = $row1['now()'];
      }
      $title = htmlspecialchars($_REQUEST['songtitle']);
      $genre = htmlspecialchars($_REQUEST['songgenre']);
      $length = htmlspecialchars($_REQUEST['songlength']);
      $artist = htmlspecialchars($_REQUEST['songartist']);
      $albumid = NULL;

      //check to see if artist is already in database; if not, add them
      $checkartist = prepared_query($dbh,$findperson,array($artist));
      $numrows2 = $checkartist->numRows();
      if ($numrows2 == 0){
        $addperson = prepared_query($dbh,$person,array($artist,$uid));
        $getpersonid = query($dbh,"select last_insert_id()");
        while($row3 = $getpersonid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $personid = $row3['last_insert_id()'];
        }
        echo "<p>Successfully added " . $artist . " to the database.<p>";
      }
      else {
        while($row3 = $checkartist->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $personid = $row3['pid'];
        }
        echo "<p>" . $artist . " is already in the database.<p>";
      }

      //check to see if album is already in database; if not, add it and add contribution between artist and album
      if (isset($_REQUEST['songalbum']) && htmlspecialchars($_REQUEST['songalbum']) != ""){
        $album = htmlspecialchars($_REQUEST['songalbum']);
        $values2 = array($album,"album",$personid);
        $albumexists = prepared_query($dbh,$findmediausingpid,$values2);
        if ($albumexists->numRows() != 0){
          while($row = $albumexists->fetchRow(MDB2_FETCHMODE_ASSOC)) {
            $albumid = $row['mid'];
          }
          echo "<p>\"" . $album . "\" is already in the database.<p>";
        }
        else{
          $values2 = array($album,$genre,$length,"album",NULL,$thetime,$uid);
          $addalbum = prepared_query($dbh,$media,$values2);
          $getalbumid = query($dbh,"select last_insert_id()");
          while($row3 = $getalbumid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
          $albumid = $row3['last_insert_id()'];
          }
          echo "<p>Successfully added \"" . $album . "\" to the database.<p>";

          $contributionexists = prepared_query($dbh,$findcontribution,array($personid,$albumid));
          $numrows = $contributionexists->numRows();
          if ($numrows == 0){
            $addalbumcontribution = prepared_query($dbh,$contribution,array($personid,$albumid));
          }

        }
      }

      //check to see if artist and song pair exist. if not, add song, and add contribution to person and song.
      $songartistexists = prepared_query($dbh,$findmediausingpid,array($title,"song",$personid));
      $numrows1 = $songartistexists->numRows();
      if ($numrows1 != 0){
        echo "<p>Sorry, there's already a song \"" . $title . "\" by " . $artist . " artist.";
      }
      else{
        $values3 = array($title,$genre,$length,$type,$albumid,$thetime,$uid);
        $addmedia = prepared_query($dbh,$media,$values3);
        echo "<p>Successfully added \"" . $title . "\" to the database.<p>";
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
    if (htmlspecialchars($_REQUEST['albumtitle']) == "" or htmlspecialchars($_REQUEST['albumartist']) == ""){
      echo "Please enter a valid album title and album artist.";
    }
    else{
      $datetime = query($dbh,"Select now()");
      while ($row1 = $datetime->fetchRow(MDB2_FETCHMODE_ASSOC)){
        $thetime = $row1['now()'];
      }
      $artist = htmlspecialchars($_REQUEST['albumartist']);
      $checkartist = prepared_query($dbh,$findperson,array($artist));
      $numrows2 = $checkartist->numRows();
      if ($numrows2 == 0){
        $addperson = prepared_query($dbh,$person,array($artist,$uid));
        $getpersonid = query($dbh,"select last_insert_id()");
        while($row3 = $getpersonid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $personid = $row3['last_insert_id()'];
        }
        echo "<p>Successfully added " . $artist . " to the database.<p>";
      }
      else {
        while($row3 = $checkartist->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $personid = $row3['pid'];
        }
        echo "<p>" . $artist . " is already in the database.<p>";
      }
      $title = htmlspecialchars($_REQUEST['albumtitle']);
      $values7 = array($title,"album",$personid);
      $albumexists = prepared_query($dbh,$findmediausingpid,$values7);
      $numrows1 = $albumexists->numRows();
      if ($numrows1 != 0){
        echo "<p>\"" . $title . "\" is already in the database.<p>";
        while($row3 = $albumexists->fetchRow(MDB2_FETCHMODE_ASSOC)) {
          $genre = $row3['genre'];
          $albumid = $row3['mid'];
        }
      }
      else {
        $genre = htmlspecialchars($_REQUEST['albumgenre']);
        $type = "album";
        $length = htmlspecialchars($_REQUEST['albumlength']);
        $albumid = NULL;
        $values3 = array($title,$genre,$length,$type,$albumid,$thetime,$uid);
        $addmedia = prepared_query($dbh,$media,$values3);
        echo "<p>Successfully added \"" . $title . "\" to the database.<p>";
        $getalbumid = query($dbh,"select last_insert_id()");
        while($row3 = $getalbumid->fetchRow(MDB2_FETCHMODE_ASSOC)) {
          $albumid = $row3['last_insert_id()'];
        }
        $values7 = array($albumid);
        $albumexists = prepared_query($dbh,$findmediausingmid,$values7);
        $numrows1 = $albumexists->numRows();
        while($row3 = $albumexists->fetchRow(MDB2_FETCHMODE_ASSOC)) {
          $genre = $row3['genre'];
         }
      }      

      $contributionexists = prepared_query($dbh,$findcontribution,array($personid,$albumid));
      $numrows = $contributionexists->numRows();
      if ($numrows == 0){
        $addcontribution = prepared_query($dbh,$contribution,array($personid,$albumid));
      }
      $songcount = 1;
      while ($songcount <= 14 and $_REQUEST['song' . $songcount] != ""){
        $title = htmlspecialchars($_REQUEST['song' . $songcount]);
        $length = htmlspecialchars($_REQUEST['length' . $songcount]);
        $songartistexists = prepared_query($dbh,$findmediausingpid,array($title,"song",$personid));
        $numrows1 = $songartistexists->numRows();
        if ($numrows1 != 0){
          echo "<p>Sorry, there's already a song \"" . $title . "\" by " . $artist . " artist.";
        }
        else{
          $values3 = array($title,$genre,$length,"song",$albumid,$thetime,$uid);
          $addmedia = prepared_query($dbh,$media,$values3);
          echo "<p>Successfully added \"" . $title . "\" to the database.<p>";
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
          $songcount++;
      }
    }
  }
}
?>
  
  </body>
  </html>