

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

ul{
  margin-left:300px;
}

.replyform1{
  margin-left:100px;
}

.auto{
  margin-left:100px;
}
</style>

<?php
function printJquery() {
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
      console.log($(this));
      var thetextbox = $(this).find("#comment");
      data = $(e.target).closest(".commentform").serialize();
      console.log(data);

      var media = $(this).closest(".media-body");
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

     $(".commentform1").submit(function(e){
      console.log("Submitting form by Ajax1");
      e.preventDefault();
      console.log($(this));
      var thetextbox = $(this).find("#comment");
      data = $(e.target).closest(".commentform1").serialize();
      console.log(data);

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
          console.log("Submitting");
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

     return array('mid' => $mid, 'rating' => $rating, 'title' => $title, 'dateadded' => $dateadded, 'genre' => $genre, 'length' => $length, 'type' => $type, 'albumid' => $albumid, 'albumname' => $albumname, 'contributionarray' => $contributionarray, 'numContributions' => $numRows, 'description' => $description);
   }
   else{
    return null;
   }
}

function getTVMovieContributions($dbh,$values,$page){
  $sql = "select pid, name from person inner join contribution using (pid) where mid = ?";
  $resultset = prepared_query($dbh,$sql,$values);
  echo "<h3 style:'margin-left:300px;'>Actors</h3><ul style:'margin-left:300px;'>";
  while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $pid = $row['pid'];
    $name = $row['name'];
    echo "<li><a href= \"person.php?pid=" . $pid . "\">$name</a><br><br></li>";
  }
  echo "</ul>";
}

function getAlbumSongs($dbh,$values,$page){
  $sql = "select * from media where albumid = ?";
  $resultset = prepared_query($dbh,$sql,$values);
  echo "<h3>Songs</h3><ul>";
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
  echo "</ul>";
}

function showRecentMedia($dbh,$page){
  $sql = "select * from media order by dateadded desc limit 10";
  $resultset = query($dbh,$sql);
  echo "<h3>Most Recently Added Items</h3>";
  echo "<table class='table' style='width:80%;'><thead><tr><th>Title</th><th>Genre</th></tr></thead><tbody>";
  while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $mid = $row['mid'];
    $title = $row['title'];
    $genre = $row['genre'];
     echo "<tr><td><a href= \"" . $page . "?mid=" . $mid . "\">$title</a></td><td>" . $genre . "</td><tr>";
  }
  echo "</tbody></table>";
}

function addMediaButton($dbh, $page, $mid, $uid){
  $sql = "select * from likes where uid = ? and mid = ?";
  $resultset = prepared_query($dbh, $sql, array($uid,$mid));
  $numResults = $resultset->numRows();
  if ($numResults == 1){
    echo "This media is in your Top Ten List.<br>";
  }
  else {
    echo '<form method="get" action="' . $page . '">
      <input type="hidden" name="mid" value="' . $mid . '">
    <input type="hidden" name="addmedia">
    <input type="submit" value="Add to Top Ten" class="btn btn-primary btn-lg">
  </form><br>';
  }
}

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

    $picture = getUserPicture($uid,$dbh);

    if ($counter == 1){
      $lastinitial = $initial;
    }
 if ($rid == $initial){
      if ($lastinitial != $initial){
        echo '</div></div>';
        $lastinitial = $initial;
      }
      echo '<div class="media"><a class="pull-left" href="user.php?uid=' . $uid . '">
            <img width=70 height=70 class="media-object" src="' . $picture . '" alt="Media Object"></a>
            <div class="media-body">
            <h4 class="media-heading">' . $name . '</h4> at ' . $time . '<br>' . $comment . '
            <br><span class="comment-reply" style="color:blue;" >Reply</span>
            <br><div class="replyform"><form class="commentform" method="get" action="' . $page . '">
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
            <br><div class="replyform"><form class="commentform" method="get" action="' . $page . '">
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

function commentForm($page, $mid, $uid){
  echo '<div class="replyform1"><form class="commentform1" method="get" action="' . $page . '">
        <input type="hidden" name="mid" value="' . $mid . '">
        <input type="hidden" name="uid" value="' . $uid . '">
        <textarea rows="4" cols="50" id="comment" name="comment"></textarea><br>
        <input type="hidden" name="addcomment">
        <input type="submit" value="Add Comment" class="btn btn-primary btn-lg">
        </form></div><br>';
}

function getNumRatings($dbh,$mid){
  $sql = "select count(uid) as count from ratings where mid = ?";
  $values = array($mid);
  $resultset = prepared_query($dbh,$sql,$values);
  $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
  return $row['count'];
}
  
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

function editDatabase($dbh){
  $sql = "update media set title = ?, genre = ?, length = ?, type = ? where mid = ?";
  $values = array($uid, $mid);
  $resultset = prepared_statement($dbh,$sql,$values);
}

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

  if ($type == "song"){
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
    if ($picture == null or $picture == ""){
      $picture = $value['artworkUrl100'];
    }
    if ($genre == null or $genre == ""){
      $genre = $value['primaryGenreName'];
    }
    if (($type == "tv" or $type == "movie") and ($description == null or $description == "")){
      if (array_key_exists('longDescription',$value)){
        $description = $value['longDescription'];
      }
      else if (array_key_exists('shortDescription',$value)){
        $description = $value['shortDescription'];
      }
    }
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

function decimal_to_time_song($decimal) {
    $minutes = floor($decimal / 60);
    $seconds = $decimal - (int)$decimal;
    $seconds = round($seconds * 60);
    return str_pad($minutes, 2, " ",STR_PAD_LEFT) . ":" . str_pad($seconds, 2, "0", STR_PAD_LEFT);
}

function processPicture($mid, $dbh){

  $destfile = "";

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
    $destfile = getMediaPicture($mid,$dbh);
  }

  return $destfile;
}

function editMediaPage($mediaarray){
  global $page;
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

  if ($type == "song" or $type == "album"){
    echo '<label for="artist" class="col-sm-2 control-label">Artist</label>
<div class="col-sm-10">
<input type="text" name="artist" class="form-control" value="' . $contributionarray[0]['name'] . '"><br></div>';
  }

  if ($type == "song"){
    echo '<label for="album" class="col-sm-2 control-label">Album Name</label>
<div class="col-sm-10">
<input type="text" name="albumname" class="form-control" value="' . $albumname . '"><br></div>';
  }

  echo '<label for="description" class="col-sm-2 control-label">Description</label>
<div class="col-sm-10">
<textarea rows="4" cols="50" class="form-control" name="description">' . $description . '</textarea><br></div>';

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

    echo '<label for="addactors" class="col-sm-2 control-label">Add Actors</label><div class="col-sm-10">';
    for ($counter = 0; $counter < 5; $counter++){
      echo '<input type="text" style="width:40%;" class="form-control" name="newactor' . $counter . '"><br>';
    }
  }

  echo '<br><input type="submit" value="Make Changes" class="btn btn-default"></form></div>';
}

checkLogInStatus();   
$username = $_SESSION['username'];
$uid = getUid($dbh,$username);

if (isset($_REQUEST['mid'])){
  $pagemid = htmlspecialchars($_REQUEST['mid']);
  $mediaarray = getMedia($pagemid,$dbh);
  if ($mediaarray == null){
    header ("Location: media.php");
    exit();
  }

  else{

    if (isset($_REQUEST['edited'])){
      $mid = htmlspecialchars($_REQUEST['mid']);
      $title = htmlspecialchars($_REQUEST['title']);
      $type = htmlspecialchars($_REQUEST['type']);
      $genre = htmlspecialchars($_REQUEST['genre']);
      $length = htmlspecialchars($_REQUEST['length']);
      $description = htmlspecialchars($_REQUEST['description']);
      $albumname = null;
      $artist = null;
      $deleteactorarray = null;
      $addactorarray = null;

      if ($type == "song"){
        $albumname = htmlspecialchars($_REQUEST['albumname']);
      }

      if ($type == "song" or $type == "album"){
        $artist = htmlspecialchars($_REQUEST['artist']);
      }
      else{

        $counter = 0;
        $deleteactorarray = array();
        for ($i=0; $i < $mediaarray['numContributions']; $i++) { 
          if (isset($_REQUEST['actor' . $counter])){
           $deleteactorarray[$counter] = htmlspecialchars($_REQUEST['actor' . $counter]);
         }
          $counter++;
        }

        $addactorarray = array();
        for ($counter = 0; $counter < 5; $counter++){
          if (isset($_REQUEST['newactor' . $counter]) and htmlspecialchars($_REQUEST['newactor' . $counter]) != ""){
            $addactorarray[$counter] = htmlspecialchars($_REQUEST['newactor' . $counter]);
          }
          else {
            break;
          }
        }

      }

      editMedia($uid, $mid, $title, $type, $genre, $length, $artist, $albumname, $deleteactorarray, $addactorarray, $description);
    }

    $artistarray = getAlbumSongContributions($dbh,$pagemid);
    getMediaInfoFromItunes($dbh,$pagemid,$artistarray['name']);
    $picture = processPicture($pagemid,$dbh);
    $mediaarray = getMedia($pagemid, $dbh);

    $title = $mediaarray['title'];
    $genre = $mediaarray['genre'];
    $length = $mediaarray['length'];
    $type = $mediaarray['type'];
    $albumname = $mediaarray['albumname'];
    $albumid = $mediaarray['albumid'];
    $rating = $mediaarray['rating'];
    $description = $mediaarray['description'];

    printPageTop("$title");
    printJquery();
    createNavBar("home.php");

    if (isset($_REQUEST['edit'])){
      echo "<h1>$title</h1>";
      editMediaPage($mediaarray);
    }

    else {

      echo '<h1>' . $title .'  <button class="btn btn-primary btn-large" onclick="location.href=\'' . $page . '?mid=' . $pagemid . '&edit\'">edit</button></h1>';
      echo "<p style='float:left; display:inline-block; margin: 20px; padding:20px;'>";
      if($picture!="") {
	echo "<img width=200 height=200 src='$picture'><p>\n";
      }
      $numRatings = getNumRatings($dbh,$pagemid);
      $myRating = getYourRating($dbh,$uid,$pagemid);
      echo "<div id='currentRating'>";
      createActualRating($rating, $numRatings);
      echo "<br>";
      createYourRating($pagemid, $myRating, $uid);
      echo "</div>";
      echo "<br>Genre: $genre<br>";

      if ($length != ""){
        echo "Length: $length<br>";
      }

      if ($type == "song"){
        $artistarray = getAlbumSongContributions($dbh,$pagemid);
        echo "Artist: <a href=\"person.php?pid=" . $artistarray['pid'] . "\">" . $artistarray['name'] . "</a><br>";
        echo "From Album: <a href= \"media.php?mid=" . $albumid . "\">$albumname</a><br>";
      }
      if ($type == "album"){
        $artistarray = getAlbumSongContributions($dbh,$pagemid);
        echo "Artist: <a href=\"person.php?pid=" . $artistarray['pid'] . "\">" . $artistarray['name'] . "</a><br>";

      }

      echo "Description: $description<br><br>";

      if (isset($_REQUEST['addmedia'])){
        addMediaToUser($dbh, $pagemid, $uid);
      }

      addMediaButton($dbh,$page,$pagemid,$uid);

      if ($type == "tv" or $type == "movie"){
        getTVMovieContributions($dbh,$pagemid,$page);
      }
      else if ($type == 'album'){
        getAlbumSongs($dbh,$pagemid,$page);
      }
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