<!-- Ashley Thomas and Sasha Levy
  Helikon
  adddata.php
  5/19/14

This file allows the user to insert data into the database. It includes a lot of forms,
including forms to enter an album or song, tv show or movie, and person
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

    //the following lines switch between tabs based on which tab the user clicks
    $("#myTab a[href='#song']").click(function (e) {
    e.preventDefault();
    $(this).tab('show');
  })

  $("#myTab a[href='#album']").click(function (e) {
    e.preventDefault();
    $(this).tab('show');
  })

  $("#myTab a[href='#moviesandtv']").click(function (e) {
    e.preventDefault();
    $(this).tab('show');
  })

  $("#myTab a[href='#actor']").click(function (e) {
    e.preventDefault();
    $(this).tab('show');
  })

  $("#myTab a[href='#info']").click(function (e) {
    e.preventDefault();
    $(this).tab('show');
  })

  $("#add_row_song").click(function (e) {
    e.preventDefault();
    var nextCount = $('#albumTable tr').length;
    $('#albumTable tr:last').after('<tr><td><input type="text" name="song' + nextCount + '" class="form-control"></td><td><input type="text" name="length' + nextCount + '" class="form-control"></td></tr>');
  })

  $("#add_row_actor").click(function (e) {
  console.log("It got here!");
  e.preventDefault();
  var nextCount1 = $('#actorTable tr').length;
  $('#actorTable tr:last').after('<tr><td><input type="text" name="mediaactor' + nextCount1 + '" class="form-control"></td></tr>');
})

 $("#add_row_media").click(function (e) {
  console.log("It got here!");
  e.preventDefault();
  var nextCount2 = $('#mediaTable tr').length;
  $("#mediaTable tr:last").after('<tr><td><p><input type="text" name="media' + nextCount2 + '" class="form-control"></td><td><select name="personmediatype' + nextCount2 + '" id="personmediatype" class="form-control"><option selected="selected" value="">None<option value="movie">Movie<option value="tv">TV</select></td></tr>');
})


});

</script>

<style>
.form-control{
  width:80%;
  }

#info{
  margin-left:40px;
  margin-right:40px;
}
</style>


 <h2 class= "title" >Add Data to the Database </h2>

<!--Associating each of the tabs with html code-->
<div id="myTab">
<ul class="nav nav-tabs">
  <li class="active"><a href="#song">Song</a></li>
  <li><a href="#album">Album</a></li>
  <li><a href="#moviesandtv">Movies/TV</a></li>
  <li><a href="#actor">Actor</a></li>
  <li><a href="#info">Info</a></li>
</ul>
</div>

<!--the song tab-->
<div class="tab-content">
  <div class="tab-pane fade in active" id="song">
    <br>
      <form class="form-horizontal" id="songform" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
<div class="form-group">
    <label for="inputTitle" class="col-sm-2 control-label">Title</label>
<div class="col-sm-10">
    <input required type="text" class="form-control" name="songtitle">
</div>
<label for="inputArtist" class="col-sm-2 control-label">Artist</label>
<div class="col-sm-10">
    <input required type="text" class="form-control" name="songartist">
</div>
<label for="inputLength" class="col-sm-2 control-label">Length</label>
<div class="col-sm-10">
 <input type="text" class="form-control" name="songlength">
</div>
<label for="inputAlbum" class="col-sm-2 control-label">Album</label>
<div class="col-sm-10">
  <input type="text" class="form-control"name="songalbum">
</div>
<label for="inputGenre" class="col-sm-2 control-label">Genre</label>
<div class="col-sm-10">
  <input required type="text" class="form-control" name="songgenre">
</div>
<label for="inputDescription" class="col-sm-2 control-label">Description</label>
<div class="col-sm-10">
  <textarea class="form-control" rows="4" cols="50" name="description"></textarea>
    <input type="hidden" name="type" value="song">
    <br><br>
    <input type="submit" class="btn btn-default">
  <input type="reset" class="btn btn-default">
</div>
</form>
  </div>
</div>

<!--the album tab-->
  <div class="tab-pane fade" id="album">
    <br>
    <div class="form-group">
    <form class="form-horizontal" id="albumform" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">

<input type="hidden" name="type" value="album">
<label for="inputTitle" class="col-sm-2 control-label">Title</label>
  <div class="col-sm-10">
    <input required type="text" class="form-control" name="albumtitle">
</div>
<label for="inputArtist" class="col-sm-2 control-label">Artist</label>
<div class="col-sm-10">
 <input required type="text" class="form-control" name="albumartist">
    </div>
<label for="inputLength" class="col-sm-2 control-label">Length</label>
<div class="col-sm-10">
  <input type="text" class="form-control" name="albumlength">
</div>
<label for="inputGenre" class="col-sm-2 control-label">Genre</label>
<div class="col-sm-10">
   <input required type="text" class="form-control" name="albumgenre">   
</div>
<label for="inputDescrption" class="col-sm-2 control-label">Description</label>
  <div class="col-sm-10">
    <textarea rows="4" cols="50" name="description" class="form-control"></textarea><br>
  </div>
    <label for="inputSongs" class="col-sm-2 control-label">Songs</label>
  <div class="col-10-sm">
  <table id="albumTable" style="width:60%;" class="table">
  <tr><th>Song</th><th>Length</th></tr>
  <tr><td><input type="text" name="song1" class="form-control"></td><td><input type="text" name="length1" class="form-control"></td></tr>
  <tr><td><input type="text" name="song2" class="form-control"></td><td><input type="text" name="length2" class="form-control"></td></tr>
  <tr><td><input type="text" name="song3" class="form-control"></td><td><input type="text" name="length3" class="form-control"></td></tr>
  <tr><td><input type="text" name="song4" class="form-control"></td><td><input type="text" name="length4" class="form-control"></td></tr>
  <tr><td><input type="text" name="song5" class="form-control"></td><td><input type="text" name="length5" class="form-control"></td></tr>
  <tr><td><input type="text" name="song6" class="form-control"></td><td><input type="text" name="length6" class="form-control"></td></tr>
  <tr><td><input type="text" name="song7" class="form-control"></td><td><input type="text" name="length7" class="form-control"></td></tr>
  <tr><td><input type="text" name="song8" class="form-control"></td><td><input type="text" name="length8" class="form-control"></td></tr>
  <tr><td><input type="text" name="song9" class="form-control"></td><td><input type="text" name="length9" class="form-control"></td></tr>
  <tr><td><input type="text" name="song10" class="form-control"></td><td><input type="text" name="length10" class="form-control"></td></tr>
  </table>
</div>
<label for="addRowSong" class="col-sm-2 control-label"></label>
<div class="col-10-sm">
  <button class="btn btn-default" id="add_row_song">Add Row</button><br><br>
</div><label for="submitting" class="col-sm-2 control-label"></label><div class="col-10-sm">
  <input type="submit" class="btn btn-default">
  <input type="reset" class="btn btn-default">
</div>
  </form>
</div>
</div>

<!--the movies/tv tab-->
  <div class="tab-pane fade" id="moviesandtv">
    <br>
     <form class="form-horizontal" id="moviesandtvform" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
<div class="form-group">
      <input type="hidden" name="type" value="moviesandtv">
<label for="inputType" class="col-sm-2 control-label">Type</label>
<div class="col-sm-10">
  <select name="mediatype" class="form-control">
  <option value="tv">TV
  <option value="movie">Movie
  </select>
</div>
<label for="inputTitle" class="col-sm-2 control-label">Title</label>
<div class="col-sm-10">
  <input required type="text" name="title" class="form-control">
</div>
<label for="inputLength" class="col-sm-2 control-label">Length</label>
<div class="col-sm-10">
 <input type="text" name="length" class="form-control">
</div>
<label for="inputGenre" class="col-sm-2 control-label">Genre</label>
<div class="col-sm-10">
  <input required type="text" name="genre" class="form-control">  
</div>
<label for="inputDescription" class="col-sm-2 control-label">Description</label>
<div class="col-sm-10">
  <textarea rows="4" cols="50" name="description" class="form-control"></textarea><br>
</div><p><p>
 <label for="inputActors" class="col-sm-2 control-label">Actors</label>
    <div class="col-sm-10">
  <table id="actorTable" style="width:60%;" class="table">
  <tr><th>Actor</th></tr>
    <tr><td><input type="text" name="mediaactor1" class="form-control"></td></tr>
    <tr><td><input type="text" name="mediaactor2" class="form-control"></td></tr>
    <tr><td><input type="text" name="mediaactor3" class="form-control"></td></tr>
    <tr><td><input type="text" name="mediaactor4" class="form-control"></td></tr>
    <tr><td><input type="text" name="mediaactor5" class="form-control"></td></tr>
    </table>
    </div>
<label for="addRowActor" class="col-sm-2 control-label"></label>
<div class="col-10-sm">
  <button class="btn btn-default" id="add_row_actor">Add Row</button><br><br>
      </div><label for="submitting" class="col-sm-2 control-label"></label><div class="col-10-sm">
    <input type="submit" class="btn btn-default">
    <input type="reset" class="btn btn-default">
</div>
    </form>
    </div>
</div>

<!--the person tab-->
  <div class="tab-pane fade" id="actor">
    <br>
     <form id="actorform" class="form-horizontal" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
<div class="form-group">
      <input type="hidden" name="type" value="person">
<label for="inputName" class="col-sm-2 control-label">Name</label>
    <div class="col-sm-10"><input required type="text" class="form-control" name="name"></div>
<label for="inputDescription" class="col-sm-2 control-label">Description</label>
  <div class="col-sm-10"><textarea rows="4" cols="50" class="form-control" name="description"></textarea></div>
<label for="inputMedia" class="col-sm-2 control-label">Media</label>
 <div class="col-sm-10">
  <table id="mediaTable" class="table" style="width: 80%;">
  <tr><th>Title</th><th>Type</th></tr>
    <tr><td><input type="text" name="media1" class="form-control"></td>
      <td><select name="personmediatype1" id="personmediatype" class="form-control">
      <option selected="selected" value="">None
      <option value="movie">Movie
      <option value="tv">TV
      </select></td></tr>
    <tr><td><input type="text" class="form-control" name="media2"></td>
      <td><select name="personmediatype2" id="personmediatype" class="form-control">
      <option selected="selected" value="">None
      <option value="movie">Movie
      <option value="tv">TV
      </select></td></tr>
    <tr><td><input type="text" name="media3" class="form-control"></td>
      <td><select name="personmediatype3" id="personmediatype" class="form-control">
      <option selected="selected" value="">None
      <option value="movie">Movie
      <option value="tv">TV
      </select></td></tr>
    <tr><td><input type="text" name="media4" class="form-control"></td>
      <td><select name="personmediatype4" id="personmediatype" class="form-control">
      <option selected="selected" value="">None
      <option value="movie">Movie
      <option value="tv">TV
      </select></td></tr>
    <tr><td><p><input type="text" name="media5" class="form-control"></td>
      <td><select name="personmediatype5" id="personmediatype" class="form-control">
      <option selected="selected" value="">None
      <option value="movie">Movie
      <option value="tv">TV
      </select></td></tr>
    </table>
</div>
<label for="addRowMedia" class="col-sm-2 control-label"></label>
<div class="col-10-sm">
  <button class="btn btn-default" id="add_row_media">Add Row</button><br><br>
</div><label for="submitting" class="col-sm-2 control-label"></label><div class="col-10-sm">
    <input type="submit" class="btn btn-default">
    <input type="reset" class="btn btn-default">
</div>
    </form>
  </div>
</div>

  <div class="tab-pane fade" id="info">

    <br>
    Welcome to this page! 
    <br><br>To add a song, go to the Song tab and enter the song's title, artist, and genre (length optional).
    <br><br>To add an album, go to the Album tab and enter the album title, the artist, the genre, and any songs that are on the album
    <br>(lengths of both album and songs optional).
    <br><br>To add a movie or tv show, go to the Movie/TV tab. Select either TV or Movie, then enter the title of the media, as well as the genre and any actors that are in it.
    <br><br>To add multiple movies or tv shows to an actor, go to the Actor tab. Enter the person's name, and then any movies or TV shows they are in. 
    <br>If there are duplicates of any tv shows and movies, you will need to select the type of media (TV show or movie) that you are trying to add.
    <br><br>In terms of genres, feel free to add a genre that best fits the piece of media you are trying to add.
    <br>Since there are so many options for media, we didn't want to provide an overwhelming menu and instead leave it up to your best judgment!
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
    while (isset($_REQUEST['media' . $mediacount]) and htmlspecialchars($_REQUEST['media' . $mediacount]) != ""){
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
    while (isset($_REQUEST['mediaactor' . $actorcount]) and htmlspecialchars($_REQUEST['mediaactor' . $actorcount]) != ""){
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
    $length = htmlspecialchars($_REQUEST['albumlength']);
    $albumid = NULL;
    $values = array($title,"album",$personid);
    $albumid = addMedia($dbh, $title, "album", $genre, $length, $uid, $albumid, $description, $findmediausingpid, $media, $values);
    addContribution($dbh, $albumid, $personid, $title, $artist, $findcontribution, $contribution);

    //for each song, add it if it doesn't exist by associating it with the album, and then create contribution
    //between song and person if none exists
    $songcount = 1;
    while (isset($_REQUEST['song' . $songcount]) and htmlspecialchars($_REQUEST['song' . $songcount]) != ""){
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