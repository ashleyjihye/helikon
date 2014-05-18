<?php
   
require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");

$page = $_SERVER['PHP_SELF'];
$dbh = db_connect($athomas2_dsn);

function getUser($values, $dbh) {
   $sql = "select uid, name, username from user where uid=?";
   $resultset = prepared_query($dbh, $sql, $values);
   $numRows = $resultset->numRows();
   if ($numRows != 0){
     $detailrow = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
     $name = $detailrow['name'];
     $username = $detailrow['username'];
     $uid = $detailrow['uid'];
     return array('uid' => $uid, 'name' => $name, 'username' => $username);
   }
   else{
    return null;
   }
}

function getLikes($values, $dbh) {
   $sql = "select title, type, likes.dateadded from user inner join likes using (uid) inner join media using (mid) where user.uid=? order by likes.dateadded desc limit 10";
   $resultset = prepared_query($dbh, $sql, $values);
   echo "Current Top Ten:<p><ol>";
   $count = 1;
   while($detailrow = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
     $title = $detailrow['title'];
     $type = $detailrow['type'];
   echo "<li>$title ($type) </li>";
   }
   echo "</ol>";
}

function deleteLike($dbh, $values){
  $sql = "delete from likes where uid = ? and mid = ?";
  prepared_statement($dbh,$sql,$values);
}

function getLikesOwner($values, $dbh) {
   $sql = "select mid, title, type, likes.dateadded from user inner join likes using (uid) inner join media using (mid) where user.uid=? order by likes.dateadded desc limit 10";
   $resultset = prepared_query($dbh, $sql, $values);
   echo "<form type='get' action='user.php'><input type='hidden' name='uid' value='" . $values . "'>Current Top Ten:<p><ol>";
   $counter = 0;
   while($detailrow = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
     $title = $detailrow['title'];
     $type = $detailrow['type'];
     $mid = $detailrow['mid'];
     echo "<li>$title ($type) <input type='checkbox' name='delete" . $counter . "' value='" . $mid . "'>Delete?</li><br>";
     $counter++;
   }
   echo "</ol><input type='submit' value='Make Changes'></form><br><br>";
}

function addFriendButton($page, $array, $username){
  if ($username != $array['username']){
    $uid = $array['uid'];
    echo '<form method="get" action="' . $page . '">
        <input type="hidden" name="uid" value="' . $uid . '">
    <input type="hidden" name="addfriend">
    <input type="submit" value="Add Friend" class="btn btn-primary btn-lg">
  </form><br>';
  }
}

function areFriends($dbh, $array, $username){
  $uid = getUid($dbh, $username);
  $sql = "select * from friends where (uid = ? or friendid = ?) and (friendid = ? or uid = ?);";
  $values = array($uid,$uid,$array['uid'],$array['uid'],);
  $resultset = prepared_query($dbh,$sql,$values);
  $numResults = $resultset->numRows();
  if ($numResults == 1){
    $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
    $state = $row['state'];
    if ($uid == $array['uid']){
    return -10;
  }
    else if ($state == '0'){
      return 0;
    }
    else if ($state == '1'){
    return 1;
    }
  }

  else {
    return -1;
  }
}

function getFriendRequests($dbh, $page, $username){
  $uid = getUid($dbh, $username);
  $sql = "select user.uid, name from friends, user where friendid = ? and state = '0' and user.uid = friends.uid";
  $resultset = prepared_query($dbh,$sql,$uid);
  $numRows = $resultset->numRows();
  if ($numRows == 0){
    echo "You have no pending friend requests.";
  }
  else{
    echo "<strong>Here are your pending friend requests.</strong><br><br>";
    while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)){
      $frienduid = $row['uid'];
      $friendname = $row['name'];
      echo '' . $friendname . '<form method="get" action="' . $page . '">
      <input type="hidden" name="uid" value="' . $uid . '">
      <input type="hidden" name="acceptdenyfriend" value="true">
      <input type="hidden" name="friendid" value="' . $frienduid . '">
      <input type="submit" name="submit" value="Accept" class="btn btn-primary btn-lg">
      <input type="submit" name="submit" value="Deny" class="btn btn-primary btn-lg">
      </form><br>';
    }
  }
}

function requestFriend($dbh, $userarray, $username){
   $uid = getUid($dbh, $username);
   $values = array($uid,$uid,$userarray['uid'],$userarray['uid'],);
   $sql = "select * from friends where (uid = ? or friendid = ?) and (friendid = ? or uid = ?)";
   $resultset = prepared_query($dbh,$sql,$values);
   $numResults = $resultset->numRows();
   if ($numResults == 0){
     $sql = "insert into friends values (?,?,'0')";
     $values = array($uid,$userarray['uid']);
     prepared_statement($dbh, $sql, $values);
 }
}

function addFriend($dbh, $friendid, $username){
   $uid = getUid($dbh, $username);
   $values = array($uid,$uid,$friendid,$friendid,);
   $sql = "select * from friends where (uid = ? or friendid = ?) and (friendid = ? or uid = ?)";
   $resultset = prepared_query($dbh,$sql,$values);
   $numResults = $resultset->numRows();
   if ($numResults != 0){
     $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
     $uid = $row['uid'];
     $friendid = $row['friendid'];
     $sql = "update friends set state = '1' where uid = ? and friendid = ?";
     prepared_statement($dbh, $sql, array($uid,$friendid,));
 }
}

function denyFriend($dbh, $friendid, $username){
   $uid = getUid($dbh, $username);
   $sql = "select * from friends where uid = ? and friendid = ?";
   $values = array($friendid,$uid,);
   $resultset = prepared_query($dbh,$sql,$values);
   $numResults = $resultset->numRows();
   if ($numResults != 0){
   $sql = "delete from friends where uid = ? and friendid = ?";
   $resultset = prepared_statement($dbh,$sql,$values);
  }
}

checkLogInStatus();   
$username = $_SESSION['username'];

if (isset($_REQUEST['uid'])){
  $pageuid = $_REQUEST['uid'];
}
else{
  $pageuid = getUid($dbh,$username);
}

$userarray = getUser($pageuid,$dbh);
if ($userarray == null){
  header('Location: user.php');
  exit();
}

  $name = $userarray['name'];
  printPageTop("$name's Profile");
  createNavBar("home.php");
  echo "<h1>$name's Profile</h1>";

  if (isset($_REQUEST['addfriend'])){
    requestFriend($dbh, $userarray, $username);
  }

  if (isset($_REQUEST['acceptdenyfriend'])){
    if ($_REQUEST['submit'] == 'Deny'){
      denyFriend($dbh,$_REQUEST['friendid'],$username);
    }
    else if ($_REQUEST['submit'] == 'Accept'){
      addFriend($dbh,$_REQUEST['friendid'],$username);
    }
  }

 if (areFriends($dbh, $userarray, $username) == 1){
    echo "You are friends.<br><br>";
  }
  else if (areFriends($dbh, $userarray, $username) == 0){
    echo "Pending friend request.<br><br>";
  }
  else if (areFriends($dbh, $userarray, $username) == -1){
    addFriendButton($page,$userarray,$username);
  }

  for ($i=0; $i < 10; $i++) { 
    if (isset($_REQUEST['delete' . $i])){
      deleteLike($dbh,array($pageuid,$_REQUEST['delete' . $i]));
    }
  }

if ($pageuid == getUid($dbh,$username)){
  getLikesOwner($pageuid,$dbh);
  getFriendRequests($dbh,$page,$username);
}
else{
  getLikes($userarray['uid'], $dbh);
}

?>

</body>
</html>