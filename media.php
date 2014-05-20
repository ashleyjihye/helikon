<!-- Ashley Thomas and Sasha Levy
  Helikon
  media.php
  5/19/14

This file includes all functionality for seeing media pages, being able to edit media pages,
and looking at recently added media, as well as adding comments to media pages
 -->

<?php
   
require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");

$page = $_SERVER['PHP_SELF'];
$dbh = db_connect($athomas2_dsn);
?>
<style>
#currentRating {
display:inline-block;
}

#myRating {
  display:inline-block;
}
.category {
  margin-left: 30px;
}

ul{
  margin-left:300px;
}

#actorheader{
  margin-left:300px;
}

#albumsongheader{
  margin-left:300px;
}

#addmediabutton{
  margin-left:280px;
}

#description{
  margin-left:280px;
  margin-right:100px;
}

.replyform1{
  margin-left:50px;
}

.auto{
  margin-left:50px;
}

#table {
  width:80%;
  }
</style>

<!--This function handles a lot of Ajax requests, including dynamically adding comments
//to the page, dealing with the user pressing reply and thereby showing a textarea in
//order for the user to type, and dealing with the user pressing enter instead of submit
//in order to submit a comment -->

<?php

function printJQuery(){
echo '<script>
  $(document).on("ready ajaxSuccess",function (){

    $(".comment-reply").hover(function(){
       $(this).css("color","pink");},function(){
        $(this).css("color","blue");
       }
    );

     $(".commentform").submit(function(e){
      console.log("Submitting form by Ajax");
      e.preventDefault();
      var thetextbox = $(this).find("#comment");
      data = $(e.target).closest(".commentform").serialize();
      var media = $(this).closest(".media-body");
      $.ajax({
        type: "POST",
        url: "commentsajax.php",
        data: data,
        success: function(data){
          $(media).append(data);
          $(thetextbox).val("");
          $(".replyform").hide();
        },
        error: function(jqXHR, textStatus, errorThrown){
          alert(textStatus);
        }
      });
    });

     $(".commentform1").submit(function(e){
      console.log("Submitting form by Ajax1");
      e.preventDefault();
      var thetextbox = $(this).find("#comment");
      data = $(e.target).closest(".commentform1").serialize();
      var media = $(this).closest(".replyform1");
      console.log("will append to "+media);
      $.ajax({
        type: "POST",
        url: "commentsajax.php",
        data: data,
        success: function(data){
          $(media).append(data);
          $(thetextbox).val("");
          $(".replyform").hide();
        },
        error: function(jqXHR, textStatus, errorThrown){
          alert(textStatus);
        }
      });
    });

  $(".replyform textarea")
      .keypress(function (e) {
        if (e.keyCode == 13  && !e.shiftKey){
          $(this).closest("form")
            .submit();
        }
      });

    $(".replyform").hide();
    $(".comment-reply").click(
      function() {
        $(".replyform").hide();
        $(this).closest(".media-body")
        .find(".replyform")
        .first()
        .show();    
        
        $(this).closest(".media-body")
        .find(".replyform")
        .first().find("#comment").focus();
      });

  });



</script>';
}

//Returns an array of all information concerning a piece of media given its mid
function getMedia($values, $dbh) {
   $sql = "select * from media where mid=?";
   $resultset = prepared_query($dbh, $sql, $values);
   $numRows = $resultset->numRows();
   if ($numRows != 0){
     $detailrow = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
     $mid = $detailrow['mid'];
     $rating = $detailrow['rating'];
     $title = $detailrow['title'];
     $dateadded = $detailrow['dateadded'];
     $genre = $detailrow['genre'];
     $length = $detailrow['length'];
     $type = $detailrow['type'];
     $description = $detailrow['description'];
     $contributionarray = array();
     //get all contributions by people concerning this mid
    $sql = "select * from media inner join contribution using (mid) inner join person using (pid) where mid = ?";
    $resultset = prepared_query($dbh, $sql, $values);
    $numRows = $resultset->numRows();
    $counter = 0;
    while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)){
      $contributionarray[$counter] = array('pid' => $row['pid'], 'name' => $row['name']);
      $counter++;
    }
   if ($type == "song" and $detailrow['albumid'] != null){
    $albumid = $detailrow['albumid'];
    $albumarray = getMedia($albumid, $dbh);
    $albumname = $albumarray['title'];
  }
    else {
    $albumid = 0;
    $albumname = "";
   }

     return array('mid' => $mid, 'rating' => $rating, 'title' => $title, 'dateadded' => $dateadded, 
                  'genre' => $genre, 'length' => $length, 'type' => $type, 'albumid' => $albumid, 
                  'albumname' => $albumname, 'contributionarray' => $contributionarray, 
                  'numContributions' => $numRows, 'description' => $description);
   }
   else{
    return null;
   }
}

//get all songs from an album in an array
function getAlbumSongsAsArray($dbh,$values,$page){
  $sql = "select * from media where albumid = ?";
  $resultset = prepared_query($dbh,$sql,$values);
  $albumarray = array();
  $counter = 0;
  while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $mid = $row['mid'];
    $title = $row['title'];
    $albumarray[$counter] = array('mid' => $mid, 'title' => $title);
    $counter++;
  } 
  return $albumarray;
}

//get all songs from an album
function getAlbumSongs($dbh,$values,$page){
  $sql = "select * from media where albumid = ?";
  $resultset = prepared_query($dbh,$sql,$values);
  $numRows = $resultset -> numRows();
  if ($numRows == 0){
    echo "<h3 id='albumsongheader'>No Songs</h3><br>";
    return;
  }
  echo "<h3 id='albumsongheader'>Songs</h3><ul>";
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
  echo "</ul><br>";
}

//show media that's most recently been added (sorted by dateadded)
function showRecentMedia($dbh,$page){
  $sql = "select * from media order by dateadded desc limit 20";
  $resultset = query($dbh,$sql);
  echo "<div class='category'><h3>Most Recently Added Items</h3>";
   echo "<table id='table' class='table'><tr><th>Title</th><th>Genre</th></tr>";
    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $title= $row['title'];
      $genre = $row['genre'];
      $mid = $row['mid'];
      echo"<tr><td><a href=\"media.php?mid=" . $mid . "\">$title</a></td><td>" . $genre . "</td></tr>";
    }
    echo "</table><br></div>";
}

//add this media to your top 10 button
function addMediaButton($dbh, $page, $mid, $uid){
  $sql = "select * from likes where uid = ? and mid = ?";
  $resultset = prepared_query($dbh, $sql, array($uid,$mid));
  $numResults = $resultset->numRows();
  if ($numResults == 1){
    echo '<div id="addmediabutton">This media is in your Top Ten List.<br></div>';
  }
  else {
    echo '<div id="addmediabutton"><form method="post" action="' . $page . '">
      <input type="hidden" name="mid" value="' . $mid . '">
    <input type="hidden" name="addmedia">
    <input type="submit" value="Add to Top Ten" class="btn btn-primary btn-lg">
  </form></div><br>';
  }
}

//add this media to your likes
function addMediaToUser($dbh, $mid, $uid){
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

//show all the comments in the database that correspond to this mid
function showComments($page, $dbh, $mid, $pageuid){
  $sql = "select rid, uid, name, comment, initial, dateadded from reviews inner join user using (uid) where mid = ? order by initial desc, dateadded";
  $resultset = prepared_query($dbh,$sql,$mid);
  $lastinitial = 0;
  $counter = 0;
  echo '<div class="auto" style="width:600px; height:500px;">';
  while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $name = $row['name'];
    $comment = $row['comment'];
    $time = $row['dateadded'];
    $initial = $row['initial'];
    $rid = $row['rid'];
    $uid = $row['uid'];
    $counter++;

    $picture = getUserPicture($uid,$dbh); //get the picture of each user

    if ($counter == 1){
      $lastinitial = $initial;
    }
 if ($rid == $initial){
      if ($lastinitial != $initial){
        echo '</div></div>';
        $lastinitial = $initial;
      } 
      //different scenarios to account for formatting
      echo '<div class="media"><a class="pull-left" href="user.php?uid=' . $uid . '">
            <img width=70 height=70 class="media-object" src="' . $picture . '" alt="Media Object"></a>
            <div class="media-body">
            <h4 class="media-heading">' . $name . '</h4> at ' . $time . '<br>' . $comment . '
            <br><span class="comment-reply" style="color:blue;" >Reply</span>
            <br><div class="replyform"><form class="commentform" method="post" action="' . $page . '">
            <input type="hidden" name="mid" value="' . $mid . '">
            <input type="hidden" name="rid" value="' . $rid . '">
            <input type="hidden" name="uid" value="' . $pageuid . '">
            <textarea rows="1" cols="50" id="comment" name="comment"></textarea><br>
            <input type="hidden" name="addcomment">
            </form></div><br>';
    }
    else{
      echo '<div class="media"><a class="pull-left" href="user.php?uid=' . $uid . '">
            <img width=70 height=70 class="media-object" src="' . $picture . '" alt="Media Object"></a>
            <div class="media-body">
            <h4 class="media-heading">' . $name . '</h4> at ' . $time . '<br>' . $comment . '
            <br><span class="comment-reply" style="color:blue"; >Reply</span>
            <br><div class="replyform"><form class="commentform" method="post" action="' . $page . '">
            <input type="hidden" name="mid" value="' . $mid . '">
            <input type="hidden" name="rid" value="' . $rid . '">
            <input type="hidden" name="uid" value="' . $pageuid . '">
            <textarea rows="1" cols="50" id="comment" name="comment"></textarea><br>
            <input type="hidden" name="addcomment">
            </form></div><br></div></div>';
    }
  }
  echo "</div><br><br>";
}

//add comment form
function commentForm($page, $mid, $uid){
  echo '<div class="replyform1"><form class="commentform1" method="post" action="' . $page . '">
        <input type="hidden" name="mid" value="' . $mid . '">
        <input type="hidden" name="uid" value="' . $uid . '">
        <textarea rows="4" cols="50" id="comment" name="comment"></textarea><br>
        <input type="hidden" name="addcomment">
        <input type="submit" value="Add Comment" class="btn btn-primary btn-lg">
        </form></div><br>';
}
  
//to display your current rating of this media
function getYourRating($dbh,$uid,$mid){
  $sql = "select rating from ratings where uid = ? and mid = ?";
  $values = array($uid, $mid);
  $resultset = prepared_query($dbh,$sql,$values);
  $numRows = $resultset->numRows();
  if ($numRows == 0){
    return 0;
  }
  else{
    $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
    return $row['rating'];
  }
}

//gets information from itunes about the media (using their Search API) and replaces it with the current info
//if the current info is blank
function getMediaInfoFromItunes($dbh,$mid,$artist){

  $sql = "select * from media where mid = ?";
  $resultset = prepared_query($dbh,$sql,$mid);
  $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
  $picture = $row['picture'];
  $title = $row['title'];
  $type = $row['type'];
  $genre = $row['genre'];
  $description = $row['description'];
  $length = $row['length'];

  $final = "";

  if ($type == "song"){ //have to search by differernt terms depending on different type of media
    $words = str_replace(" ","+",$title) . "+" . str_replace(" ","+",$artist);
    $json =  file_get_contents('http://itunes.apple.com/search?term='.$words.'&limit=25&media=music&entity=song'); 
  }  

  else if ($type == "album"){
    $words = str_replace(" ","+",$title) . "+" . str_replace(" ","+",$artist);
    $json =  file_get_contents('http://itunes.apple.com/search?term='.$words.'&limit=25&media=music&entity=album'); 
  } 

  else if ($type == "tv"){
    $words = str_replace(" ","+",$title);
    $json =  file_get_contents('http://itunes.apple.com/search?term='.$words.'&limit=25&media=tvShow&entity=tvSeason'); 
    $json1 =  file_get_contents('http://itunes.apple.com/search?term='.$words.'&limit=25&media=tvShow&entity=tvEpisode'); 

  }
  else if ($type == "movie"){
    $words = str_replace(" ","+",$title);
    $json =  file_get_contents('http://itunes.apple.com/search?term='.$words.'&limit=25&media=movie'); 
  }

  $array = json_decode($json, true);
  foreach($array['results'] as $value)
  {
    if ($picture == null or $picture == ""){ //get the artwork for this
      $picture = $value['artworkUrl100'];
    }
    if ($genre == null or $genre == ""){ //the genre
      $genre = $value['primaryGenreName'];
    }
    //a description
    if (($type == "tv" or $type == "movie") and ($description == null or $description == "")){
      if (array_key_exists('longDescription',$value)){
        $description = $value['longDescription'];
      }
      else if (array_key_exists('shortDescription',$value)){
        $description = $value['shortDescription'];
      }
    }
    //time for song is minutes and seconds, for album is # of songs, for movie is minutes (iTunes doesn't provide good length for tv)
    if ($type == "song" and ($length == null or $length == "")){
      $length = ((int)$value['trackTimeMillis'])/1000.0;
      $length = decimal_to_time_song($length);
    }    
    if ($type == "album" and ($length == null or $length == "")){
      $length = $value['trackCount'] . " songs";
    }
    if ($type == "movie" and ($length == null or $length == "")){
      $length = round((((int)$value['trackTimeMillis'])/1000.0)/60) . " minutes";
    }    

    $sql = "update media set picture = ?, genre = ?, description = ?, length = ? where mid = ?";
    prepared_statement($dbh,$sql,array($picture,$genre,$description,$length,$mid));
    break;
  }
}

//helpfer function to help convert seconds to minutes and seconds and display it nicely
function decimal_to_time_song($decimal) {
    $minutes = floor($decimal / 60);
    $seconds = $decimal - (int)$decimal;
    $seconds = round($seconds * 60);
    return str_pad($minutes, 2, " ",STR_PAD_LEFT) . ":" . str_pad($seconds, 2, "0", STR_PAD_LEFT);
}

//function to get the picture of the media based on if user uploaded one, or if one exists in database
function processPicture($mid, $dbh){
  $destfile = "";

  //user uploaded a new picture
  if (isset($_FILES['imagefile']) and $_FILES["imagefile"]["error"] == 0){
    if( $_FILES['imagefile']['error'] != UPLOAD_ERR_OK ) {
        print "<P>Upload error: " . $_FILES['imagefile']['error'];
    } 
    else {
      // image was successfully uploaded.  
      $name = $_FILES['imagefile']['name'];
      $type = $_FILES['imagefile']['type'];
      $tmp  = $_FILES['imagefile']['tmp_name'];

      $destdir = "mediaimages/";
      $destfilename = "$mid.jpg";
      $destfile = $destdir . $destfilename;

      $sql = "UPDATE media SET picture = ? WHERE mid = ?";

      if(move_uploaded_file($tmp, $destfile)) {
        prepared_statement($dbh,$sql,array($destfile,$mid));
      } 
      else {
        print "<p>Error moving $tmp\n";
      }
    }
  }

  else {
    $destfile = getMediaPicture($mid,$dbh); //look in the database
  }
  return $destfile;
}

//different page for editing the media form
function editMediaPage($mediaarray){
  global $page;
  global $dbh;

  //get all these values to have as values in the form
  $mid = $mediaarray['mid'];
  $title = $mediaarray['title'];
  $genre = $mediaarray['genre'];
  $length = $mediaarray['length'];
  $type = $mediaarray['type'];
  $albumname = $mediaarray['albumname'];
  $albumid = $mediaarray['albumid'];
  $rating = $mediaarray['rating'];
  $description = $mediaarray['description'];
  $contributionarray = $mediaarray['contributionarray'];

  echo '<form class="form-horizontal" style="width:1000px;" method="post" action="' . $page . '" enctype="multipart/form-data">
    <div class="form-group">
    <input type="hidden" name="mid" value="' . $mid . '">
    <input type="hidden" name="edited">
    <label for="uploadPic" class="col-sm-2 control-label">Upload Picture</label>
    <div class="col-sm-10">
    <input type="file" name="imagefile" size="50" class="form-control">
    </div>
    <label for="title" class="col-sm-2 control-label">Title</label>
    <div class="col-sm-10">
    <input type="text" class="form-control" name="title" value="' . $title . '"><br>
    </div>
    <label for="genre" class="col-sm-2 control-label">Genre</label>
    <div class="col-sm-10">
    <input type="text" name="genre" class="form-control" value="' . $genre . '"><br>
    </div>
    <label for="length" class="col-sm-2 control-label">Length</label>
    <div class="col-sm-10">
    <input type="text" name="length" class="form-control" value="' . $length . '"><br>
    </div>
    <label for="type" class="col-sm-2 control-label">Type</label>
    <div class="col-sm-10">
    <input type="text" name="type" class="form-control" value="' . $type . '"><br>
    </div>';

  //artist
  if ($type == "song" or $type == "album"){
    echo '<label for="artist" class="col-sm-2 control-label">Artist</label>
      <div class="col-sm-10">
      <input type="text" name="artist" class="form-control" value="' . $contributionarray[0]['name'] . '"><br></div>';
  }

  //album that song came from
  if ($type == "song"){
    echo '<label for="album" class="col-sm-2 control-label">Album Name</label>
      <div class="col-sm-10">
      <input type="text" name="albumname" class="form-control" value="' . $albumname . '"><br></div>';
  }

  echo '<label for="description" class="col-sm-2 control-label">Description</label>
    <div class="col-sm-10">
    <textarea rows="4" cols="50" class="form-control" name="description">' . $description . '</textarea><br></div>';

  //songs for an album
  if ($type == "album"){
    $albumarray = getAlbumSongsAsArray($dbh,$mid,$page);
    if($albumarray != null) {
      echo '<label for="deletesong" class="col-sm-2 control-label">Delete Songs</label>';
      $counter = 0;
      foreach ($albumarray as $key => $value) {
        if ($counter !=0){
          echo '<label for="deletesong" class="col-sm-2 control-label"></label>';
        }
          echo '<div class="col-sm-10"><h4>' . $value['title'] . '</h4></div>';
          echo '<label for="deletesong" class="col-sm-2 control-label"></label>';
          echo '<div class="col-sm-10"> <input type="checkbox" float="left" class="form-control" name="song' . $counter . '" value="' . $value['mid'] . '"></div><br>';
          $counter++;
      }
    }

    echo '<label for="addsongs" class="col-sm-2 control-label">Add Songs</label><div class="col-sm-10"> <table id="songTable" style="width:60%;" class="table">
        <tr><th>Song</th></tr>';
    for ($counter = 1; $counter <= 5; $counter++){
      echo '<tr><td><input type="text" style="width:80%;" class="form-control" name="newsong' . $counter . '"></td></tr>';
    }
    echo '</table></div><label for="addRowSong" class="col-sm-2 control-label"></label>
      <div class="col-10-sm"><button class="btn btn-default" id="add_row_song">Add Row</button><br><br></div>';

  }

  //actors
  if ($type == "movie" or $type == "tv"){
    if($contributionarray != null) {
      echo '<label for="deleteactor" class="col-sm-2 control-label">Delete Actors</label>';
      $counter = 0;
      foreach ($contributionarray as $key => $value) {
        if ($counter !=0){
          echo '<label for="deleteactor" class="col-sm-2 control-label"></label>';
        }
          echo '<div class="col-sm-10"><h4>' . $value['name'] . '</h4></div>';
          echo '<label for="deleteactor" class="col-sm-2 control-label"></label>';
          echo '<div class="col-sm-10"> <input type="checkbox" float="left" class="form-control" name="actor' . $counter . '" value="' . $value['pid'] . '"></div><br>';
          $counter++;
      }
    }

    echo '<label for="addactors" class="col-sm-2 control-label">Add Actors</label><div class="col-sm-10"> <table id="actorTable" style="width:60%;" class="table">
        <tr><th>Actor</th></tr>';
    for ($counter = 1; $counter <= 5; $counter++){
      echo '<tr><td><input type="text" style="width:80%;" class="form-control" name="newactor' . $counter . '"></td></tr>';
    }
    echo '</table></div><label for="addRowActor" class="col-sm-2 control-label"></label>
      <div class="col-10-sm"><button class="btn btn-default" id="add_row_actor">Add Row</button><br><br></div>';
  }
  echo '<label for="submitting" class="col-sm-2 control-label"></label><div class="col-10-sm">
    <input type="submit" value="Make Changes" class="btn btn-default"></div></form></div>';
}

checkLogInStatus();   
$username = $_SESSION['username'];
$uid = getUid($dbh,$username);

if (isset($_REQUEST['mid'])){
  $pagemid = htmlspecialchars($_REQUEST['mid']);
  $mediaarray = getMedia($pagemid,$dbh);
  if ($mediaarray == null){ //make sure the mid is actually in the database
    header ("Location: media.php");
    exit();
  }

  else{
    //user edited the media
    if (isset($_REQUEST['edited'])){

      $mid = htmlspecialchars($_REQUEST['mid']);

      $mediaarray1 = getMedia($mid,$dbh);
      $albumarray = getAlbumSongsAsArray($dbh,$mid,$page);

      $title = htmlspecialchars($_REQUEST['title']);
      if ($title == ""){
        $title = $mediaarray1['title'];
      }
      $type = htmlspecialchars($_REQUEST['type']);
      if ($type == ""){
        $type = $mediaarray1['type'];
      }
      $genre = htmlspecialchars($_REQUEST['genre']);
      if ($genre == ""){
        $genre = $mediaarray1['genre'];
      }
      $length = htmlspecialchars($_REQUEST['length']);
      $description = htmlspecialchars($_REQUEST['description']);
      $albumname = null;
      $artist = null;
      $deleteactorarray = null;
      $addactorarray = null;
      $deletesongarray = null;
      $addsongarray = null;

      if ($type == "song"){
        if (htmlspecialchars($_REQUEST['albumname']) != ""){
          $albumname = htmlspecialchars($_REQUEST['albumname']);
        }
        else{
         $albumname = $mediaarray1['albumname'];
        }
      }

      if ($type == "song" or $type == "album"){
        if (htmlspecialchars($_REQUEST['artist']) != ""){
          $artist = htmlspecialchars($_REQUEST['artist']);
        }
        else{
          $artist = $mediaarray1['contributionarray'][0]['name'];
        }
      }

      if ($type == "tv" or $type == "movie"){
        $counter = 0;
        $deleteactorarray = array();
        for ($i=0; $i < $mediaarray['numContributions']; $i++) { 
          if (isset($_REQUEST['actor' . $counter])){
           $deleteactorarray[$counter] = htmlspecialchars($_REQUEST['actor' . $counter]);
         }
          $counter++;
        }
        $addactorarray = array();
        $counter = 1;
        while (isset($_REQUEST['newactor' . $counter]) and htmlspecialchars($_REQUEST['newactor' . $counter]) != ""){
          $addactorarray[$counter] = htmlspecialchars($_REQUEST['newactor' . $counter]);
          $counter++;
        }
      }

      if ($type == "album"){
        $counter = 0;
        $deletesongarray = array();
        for ($i=0; $i < count($albumarray); $i++) { 
          if (isset($_REQUEST['song' . $counter])){
           $deletesongarray[$counter] = htmlspecialchars($_REQUEST['song' . $counter]);
         }
          $counter++;
        }
        $addsongarray = array();
        $counter = 1;
        while (isset($_REQUEST['newsong' . $counter]) and htmlspecialchars($_REQUEST['newsong' . $counter]) != ""){
          $addsongarray[$counter] = htmlspecialchars($_REQUEST['newsong' . $counter]);
          $counter++;
        }
      }

      editMedia($uid, $mid, $title, $type, $genre, $length, $artist, $albumname, $deleteactorarray, $addactorarray, $description, $deletesongarray, $addsongarray);
    }

    $artistarray = getAlbumSongContributions($dbh,$pagemid);
    getMediaInfoFromItunes($dbh,$pagemid,$artistarray['name']); //get info from iTunes
    $picture = processPicture($pagemid,$dbh); //get correct picture
    $mediaarray = getMedia($pagemid, $dbh); //get all the media data again, since it could have been edited

    $title = $mediaarray['title'];
    $genre = $mediaarray['genre'];
    $length = $mediaarray['length'];
    $type = $mediaarray['type'];
    $albumname = $mediaarray['albumname'];
    $albumid = $mediaarray['albumid'];
    $rating = $mediaarray['rating'];
    $description = $mediaarray['description'];
    $contributionarray = $mediaarray['contributionarray'];

    printPageTop("$title");
    printJQuery();

?>

<!--This handles getting extra rows for the editing table -->
<script>
$(document).ready(function (){
  $("#add_row_actor").click(function (e) {
    console.log("It got here!");
    e.preventDefault();
    var nextCount = $("#actorTable tr").length;
    $("#actorTable tr:last").after('<tr><td><input type="text" style="width:80%;" class="form-control" name="newactor' + nextCount + '"></td></tr>');
  })

  $("#add_row_song").click(function (e) {
    console.log("It got here!");
    e.preventDefault();
    var nextCount = $("#songTable tr").length;
    $("#songTable tr:last").after('<tr><td><input type="text" style="width:80%;" class="form-control" name="newsong' + nextCount + '"></td></tr>');
  })
});
</script>

<?php

    createNavBar("home.php");

    if (isset($_REQUEST['edit'])){ //user wants to edit media
      echo "<h1>$title</h1>";
      editMediaPage($mediaarray);
    }

    else { //regular media page
      echo '<h1>' . $title .'  <button class="btn btn-primary btn-large" onclick="location.href=\'' . $page . '?mid=' . $pagemid . '&edit\'">edit</button></h1>';
      echo "<p style='float:left; display:inline-block; margin: 20px; padding:20px;'>";
      if($picture!="") {
	echo "<img width=200 height=200 src='$picture'><p><br><br>\n";
      }
      $numRatings = getNumRatings($dbh,$pagemid);
      $myRating = getYourRating($dbh,$uid,$pagemid);
      echo "<div id='currentRating'>";
      createActualRating($rating, $numRatings);
      echo "</div><br><div id='myRating'>";
      createYourRating($pagemid, $myRating, $uid);
      echo "</div>";
      echo "<br>Genre: $genre<br>";

      if ($length != ""){
        echo "Length: $length<br>";
      }

      if ($type == "song"){
        $artistarray = getAlbumSongContributions($dbh,$pagemid);
        echo "Artist: <a href=\"person.php?pid=" . $artistarray['pid'] . "\">" . $artistarray['name'] . "</a><br>";
        if ($albumid != "" and $albumid != null){
          echo "From Album: <a href= \"media.php?mid=" . $albumid . "\">$albumname</a><br>";
        }
      }
      if ($type == "album"){
        $artistarray = getAlbumSongContributions($dbh,$pagemid);
        echo "Artist: <a href=\"person.php?pid=" . $artistarray['pid'] . "\">" . $artistarray['name'] . "</a><br>";

      }

      if ($description != "" and $description != "null"){
        echo "<div id='description'>Description: $description</div><br>";
      }

      if (isset($_REQUEST['addmedia'])){
        addMediaToUser($dbh, $pagemid, $uid);
      }
      echo "<br>";
      addMediaButton($dbh,$page,$pagemid,$uid);

      if ($type == "tv" or $type == "movie"){
        echo "<h3 id='actorheader'>Actors</h3><ul>";
        foreach ($contributionarray as $key => $value) {
            echo "<li><a href= \"person.php?pid=" . $value['pid'] . "\">" . $value['name'] . "</a><br><br></li>";
          }
        echo "</ul>";
      }
      else if ($type == 'album'){
        getAlbumSongs($dbh,$pagemid,$page);
      }

      //comments
      commentForm($page, $pagemid, $uid);
      showComments($page,$dbh,$pagemid, $uid);

    }

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