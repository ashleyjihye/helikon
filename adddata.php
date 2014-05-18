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
  <p>Description<p> <textarea rows="4" cols="50" name="description"></textarea>
    <input type="hidden" name="type" value="song">
    <br><br>
    <input type="submit">
  <input type="reset">
  </form>
  </div>


  <div class="tab-pane fade" id="album">
    <br>
    <form id="albumform" method="get" action="<?php echo $_SERVER['PHP_SELF']?>">
<input type="hidden" name="type" value="album">
    <p>Title <input required type="text" name="albumtitle">
  <p>Artist <input required type="text" name="albumartist">
  <p>Length <input type="text" name="albumlength">
  <p>Genre <input required type="text" name="albumgenre">   
  <p>Description<p> <textarea rows="4" cols="50" name="description"></textarea>
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
     <form id="moviesandtvform" method="get" action="<?php echo $_SERVER['PHP_SELF']?>">
      <input type="hidden" name="type" value="moviesandtv">
  Type: <select name="mediatype">
  <option value="tv">TV
  <option value="movie">Movie
  </select>
  <p>Title <input required type="text" name="title">
  <p>Length <input type="text" name="length">
  <p>Genre <input required type="text" name="genre">  
  <p>Description<p> <textarea rows="4" cols="50" name="description"></textarea>
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
     <form id="actorform" method="get" action="<?php echo $_SERVER['PHP_SELF']?>">
      <input type="hidden" name="type" value="person">
    <p>Name <input required type="text" name="name">
  <p>Description<p> <textarea rows="4" cols="50" name="description"></textarea>
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

// The following defines the data source name (username, password,
// host and database).

require_once('athomas2-dsn.inc');

//query statements
$dbh = db_connect($athomas2_dsn);
$person = "Insert into person (name,addedby,description) values (?,?,?)";
$media = "Insert into media (title, genre, length, type, albumid, dateadded,addedby, rating, description) values (?,?,?,?,?,?,?,0,?)";
$contribution = "Insert into contribution values (?,?)";
$findmedia = "Select * from media where title = ?";
$findmediawithtype = "select * from media where title = ? and type = ?";
$findperson = "select * from person where name = ?";
$findcontribution = "select * from contribution where pid = ? and mid = ?";
$findmediausingpid = "select mid, pid, genre from media inner join contribution using (mid) where title = ? and type = ? and pid = ?";

//get the user's uid
$uid = getUid($dbh,$_SESSION['username']);

if (empty($_REQUEST)){
  echo "Please enter either a person or a piece of media to add to the database!";
}

else if (!empty($_REQUEST['type'])){
  $type = $_REQUEST['type'];

  //user wnats to input person and associated media
  if ($type == 'person'){
    //get user-inputted name and enter into database if not exists yet
    $name = htmlspecialchars($_REQUEST['name']);
    $description = htmlspecialchars($_REQUEST['description']);
    $personid = addPerson($dbh, $name, $uid, $description, $findperson, $person);
    $mediacount = 1;

    //for each piece of media, associate the media and person only if media already exists in the database
    while ($mediacount <= 6 and $_REQUEST['media' . $mediacount] != ""){
      $mediatitle = htmlspecialchars($_REQUEST['media' . $mediacount]);
      $mediatype = htmlspecialchars($_REQUEST['personmediatype' . $mediacount]);
      $mediaid = findMedia($dbh, $mediatitle, $mediatype, $findmedia, $findmediawithtype);
      if ($mediaid != null){
        addContribution($dbh, $mediaid, $personid, $mediatitle, $name, $findcontribution, $contribution);
      }
      $mediacount++;
    }
  }
  
  //user wants to input a movie or tv show and associated actors
  else if ($type == 'moviesandtv'){
    $title = htmlspecialchars($_REQUEST['title']);
    $type = htmlspecialchars($_REQUEST['mediatype']);
    $genre = htmlspecialchars($_REQUEST['genre']);
    $length = htmlspecialchars($_REQUEST['length']);
    $description = htmlspecialchars($_REQUEST['description']);
    $values = array($title,$type);
    //add the media to the database if not existing yet
    $mediaid = addMedia($dbh, $title, $type, $genre, $length, $uid, NULL, $description, $findmediawithtype, $media, $values);

    //for each actor, add them if they don't exist in database and then associate movie/tvshow and actor
    $actorcount = 1;
    while ($actorcount <= 6 and htmlspecialchars($_REQUEST['mediaactor' . $actorcount]) != ""){
      $name = htmlspecialchars($_REQUEST['mediaactor' . $actorcount]);
      $personid = addPerson($dbh, $name, $uid, null, $findperson, $person);
      addContribution($dbh, $mediaid, $personid, $title, $name, $findcontribution, $contribution);
      $actorcount++;
    }
  }

  //adds a song with an album that may or may not exist and a person that may or may not exist.
  else if ($type == "song"){
    $title = htmlspecialchars($_REQUEST['songtitle']);
    $genre = htmlspecialchars($_REQUEST['songgenre']);
    $length = htmlspecialchars($_REQUEST['songlength']);
    $artist = htmlspecialchars($_REQUEST['songartist']);
    $description = htmlspecialchars($_REQUEST['description']);
    $albumid = NULL;

    //add person if they don't already exist in database
    $personid = addPerson($dbh, $artist, $uid, null, $findperson, $person);

    //if the song has an associated album, add that and add a contribution between person and album
    if (isset($_REQUEST['songalbum']) && htmlspecialchars($_REQUEST['songalbum']) != ""){
      $album = htmlspecialchars($_REQUEST['songalbum']);
      $values = array($album,"album",$personid);
      $albumid = addMedia($dbh, $album, $type, $genre, NULL, $uid, $albumid, null, $findmediausingpid, $media, $values);
      addContribution($dbh, $albumid, $personid, $album, $artist, $findcontribution, $contribution);
    }

    //add the song to the database and the contribution
    $values = array($title, "song", $personid);
    $songid = addMedia($dbh, $title, $type, $genre, $length, $uid, $albumid, $description, $findmediausingpid, $media, $values);
    addContribution($dbh, $songid, $personid, $title, $artist, $findcontribution, $contribution);
  }

  //user wants to input an album and possible songs
  else if ($type == 'album'){
    //add artist or check for existing artist
    $artist = htmlspecialchars($_REQUEST['albumartist']);
    $personid = addPerson($dbh, $artist, $uid, null, $findperson, $person);

    //add album or check for existing album, and create contribution between album and person if none exists
    $title = htmlspecialchars($_REQUEST['albumtitle']);
    $genre = htmlspecialchars($_REQUEST['albumgenre']);
    $description = htmlspecialchars($_REQUEST['description']);
    $albumid = NULL;
    $values = array($title,"album",$personid);
    $albumid = addMedia($dbh, $title, "album", $genre, $length, $uid, $albumid, $description, $findmediausingpid, $media, $values);
    addContribution($dbh, $albumid, $personid, $title, $artist, $findcontribution, $contribution);

    //for each song, add it if it doesn't exist by associating it with the album, and then create contribution
    //between song and person if none exists
    $songcount = 1;
    while ($songcount <= 14 and $_REQUEST['song' . $songcount] != ""){
      $title = htmlspecialchars($_REQUEST['song' . $songcount]);
      $length = htmlspecialchars($_REQUEST['length' . $songcount]);
      $values = array($title,"song",$personid);
      $songid = addMedia($dbh, $title, "song", $genre, $length, $uid, $albumid, null, $findmediausingpid, $media, $values);
      addContribution($dbh, $songid, $personid, $title, $artist, $findcontribution, $contribution);
      $songcount++;
    }
  }
}
?>
  
  </body>
  </html>