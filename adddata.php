<!-- Ashley Thomas and Sasha Levy
  Helikon
  adddata.php
  
  To Do:
  add more helpful messages to the user and escape everything when adding stuff
  clean up! better variable names
  maybe make things into methods
  -->
 
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
  
  <script src="https://code.jquery.com/jquery.js"></script>

  <script>
  $(document).ready(function (){
  $("#type").change(function() { 
    var v = $(this).val();
    if (v == "person"){
      $("#person").show();
      $("#contribution").hide();
      $("#media").hide();
      $("#albumsongs").hide();
    }
    else if (v == "media"){
      $("#person").hide();
      $("#contribution").hide();
      $("#media").show();
      $("#albumsongs").hide();
    }
    else if (v == "contribution"){
      $("#person").hide();
      $("#contribution").show();
      $("#media").hide();
      $("#albumsongs").hide();
    }
    else if (v == "mediaalbum"){
      $("#person").hide();
      $("#contribution").hide();
      $("#media").hide();
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
  <option value="media">Media
  <option value="mediaalbum">Album
  <option value="contribution">Contribution
  </select>
  <div id="person">
  <p>Name <input type="text" name="name">
  </div>
  <div id="media">
  <p>Title <input type="text" name="title">
  <p>Type
  <select name="mediatype">
  <option value="tv">TV
  <option value="movie">Movie
  <option value="song">Song
  <option value="album">Album
  </select>
  <p>Length <input type="text" name="length">
  <p>Album <input type="text" name="album">
  <p>Genre <input type="text" name="genre">
  </div>
  <div id="albumsongs">
  <p>Title <input type="text" name="albumtitle">
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
  <div id="contribution">
  <p>Name <input type="text" name="personname">
  <p>Title <input type="text" name="mediatitle">
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

// The following defines the data source name (username, password,
// host and database).

require_once('athomas2-dsn.inc');

// The following connects to the database, returning a database handle (dbh)

$dbh = db_connect($athomas2_dsn);
$person = "Insert into person (name) values (?)";
$media = "Insert into media (title, genre, length, type, albumid, dateadded) values (?,?,?,?,?,?)";
$contribution = "Insert into contribution values (?,?)";
$findmedia = "Select * from media where title = ?";
$findalbum = "select * from media where title = ? and type = 'album'";
$findperson = "select * from person where name = ?";
$findcontribution = "select * from contribution where pid = ? and mid = ?";
$findsongs = "select * from media where albumid = ?";

if (empty($_REQUEST)){
  echo "Please enter either a person or a piece of media to add to the database!";
}
//fix this!

else if (!empty($_REQUEST['type'])){
  $type = $_REQUEST['type'];
  echo $type;
  if ($type == 'person'){
    if ($_REQUEST['name'] == ""){
      echo "Please enter a valid name for the person.";
    }
    else{
      $values = array($_REQUEST['name']);
      $personexists = prepared_query($dbh,$findperson,$values);
      $numrows = $personexists->numRows();
      if ($numrows != 0){
        echo "<p>Sorry, there's already someone with that name.";
      }
      else {
        $addperson = prepared_query($dbh,$person,$values);
        echo "<p>Successfully added that person to the database.";
      }
    }
  }
  
  else if ($type == 'media'){
    if ($_REQUEST['title'] == ""){
      echo "Please enter a valid title for the piece of media you want to add.";
    }
    else{
      $title = $_REQUEST['title'];
      $values1 = array($title);
      $mediaexists = prepared_query($dbh,$findmedia,$values1);
      $numrows1 = $mediaexists->numRows();
      if ($numrows1 != 0){
        echo "<p>Sorry, there's already a piece of media with that title.";
      }
      else {
        $genre = $_REQUEST['genre'];
        $type = $_REQUEST['mediatype'];
        $length = $_REQUEST['length'];
        $albumid = NULL;
        if (isset($_REQUEST['album']) and $_REQUEST['mediatype'] == "song"){
          $album = $_REQUEST['album'];
          $values2 = array($album);
          $albumexists = prepared_query($dbh,$findalbum,$values2);
          if ($albumexists->numRows() != 0){
            while($row = $albumexists->fetchRow(MDB2_FETCHMODE_ASSOC)) {
              $albumid = $row['mid'];
            }
          }
        }
        $datetime = query($dbh,"Select now()");
        while ($row1 = $datetime->fetchRow(MDB2_FETCHMODE_ASSOC)){
          $thetime = $row1['now()'];
        }
        $values3 = array($title,$genre,$length,$type,$albumid,$thetime);
        $addmedia = prepared_query($dbh,$media,$values3);
        echo "<p>Successfully added that piece of media to the database.";
      }
    }
  }
  else if ($type == 'mediaalbum'){
    if ($_REQUEST['albumtitle'] == ""){
      echo "Please enter a valid album title.";
    }
    else{
      $datetime = query($dbh,"Select now()");
      while ($row1 = $datetime->fetchRow(MDB2_FETCHMODE_ASSOC)){
        $thetime = $row1['now()'];
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
      $songcount = 1;
      while ($songcount <= 14 and $_REQUEST['song' . $songcount] != ""){
        $values6 = array($_REQUEST['song' . $songcount],$genre,$_REQUEST['length' . $songcount],"song",$albumid,$thetime);
        $addmedia = prepared_query($dbh,$media,$values6);
        $songcount++;
      }
    }
  }
  else if ($type == 'contribution'){
    if ($_REQUEST['personname'] == "" or $_REQUEST['mediatitle'] == ""){
      echo "Please enter a valid name for the person and a valid title for the media you wish to add.";
    }
    else{
      $pid = null;
      $mid = null;
      $values4 = array($_REQUEST['personname']);
      $personexists = prepared_query($dbh,$findperson,$values4);
      $numrows = $personexists->numRows();
      if ($numrows == 0){
        echo "<p>Sorry, nobody with that name exists.";
      }
      else if ($numrows != 1){
        echo "<p>More than one person matched your result. Please select the correct name from the drop down menu below.";
        echo '<form method="post" action="$page">';
        echo "<select name='pickperson'>";
        while ($row2 = $personexists->fetchRow(MDB2_FETCHMODE_ASSOC)){
          $personid = $row2['pid'];
          $personname = $row2['name'];
          echo "<option value='$personnm'>$personname<p>";
        }
        echo "</select>";
      }
      else{
        while ($row2 = $personexists->fetchRow(MDB2_FETCHMODE_ASSOC)){
          $pid = $row2['pid'];
        }
      }
      
      if ($numrows != 0){
        $values5 = array($_REQUEST['mediatitle']);
        $mediaexists = prepared_query($dbh,$findmedia,$values5);
        $numrows1 = $mediaexists->numRows();
        if ($numrows1 == 0){
          echo "<p>Sorry, nothing with that title exists.";
        }
        else if ($numrows1 != 1){
          echo "<p>More than one title matched your result. Please select the correct title from the drop down menu below.";
          if ($numrows == 1){
            echo '<form method="post" action="$page">';
          }
          echo "<select name='picktitle'>";
          while ($row3 = $mediaexists->fetchRow(MDB2_FETCHMODE_ASSOC)){
            $mediaid = $row3['mid'];
            $mediatitle = $row3['title'];
            echo "<option value='$mediaid'>$mediatitle<p>";
          }
          echo "</select></form>";
        }
        else{
          while ($row3 = $mediaexists->fetchRow(MDB2_FETCHMODE_ASSOC)){
            $mid = $row3['mid'];
            $type = $row3['type'];
          }
        }
      }
      
      if (isset($pid) and isset($mid) and $numrows !=0 and $numrows1 != 0){
        $values6 = array($pid,$mid);
        $contributionexists = prepared_query($dbh,$findcontribution,$values6);
        $numrows = $contributionexists->numRows();
        if ($numrows == 1){
          echo "This contribution already exists.";
        }
        else{
          $insertcontribution = prepared_query($dbh,$contribution,$values6);
          echo "Successfully inserted contribution.";
        }
        if ($type == "album"){
          $values6 = array($mid);
          $findalbumsongs = prepared_query($dbh,$findsongs,$values6);
          while ($row6 = $findalbumsongs->fetchRow(MDB2_FETCHMODE_ASSOC)){
            $songid = $row6['mid'];
            $values7 = array($pid,$songid);
            $contributionexists = prepared_query($dbh,$findcontribution,$values7);
            $numrows = $contributionexists->numRows();
            if ($numrows == 1){
              echo "This song and person contribution already exists.";
            }
            else{
              $insertcontribution = prepared_query($dbh,$contribution,$values7);
              echo "Successfully inserted song and person pair contribution.";
            }
          }
        }
      }
    }
  }
}
?>
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  </body>
  </html>