<?php

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");

$page = $_SERVER['PHP_SELF'];
$dbh = db_connect($athomas2_dsn);

function addComment($dbh, $mid, $uid, $parentrid, $comment){
  $datetime = query($dbh,"Select now()");
  while ($row1 = $datetime->fetchRow(MDB2_FETCHMODE_ASSOC)){
    $thetime = $row1['now()'];
  }
   $sql = "select rid from reviews order by rid desc limit 1";
   $resultset = query($dbh,$sql);
   $row1 = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
   $rid = $row1['rid'] + 1;
   if ($parentrid == null){
    $parentrid = $rid;
   }
   else{
     $sql = "select initial from reviews where rid = ?";
     $resultset = prepared_query($dbh,$sql,array($parentrid));
     $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
     $parentrid = $row['initial'];
   }
   $values = array($uid,$mid,$thetime,$parentrid,$comment);
   $sql = "insert into reviews (uid,mid,dateadded,initial,comment) values (?,?,?,?,?)";
   prepared_statement($dbh, $sql, $values);
}

function getLastComment($dbh){
	$commentrid = query($dbh,"select last_insert_id()");
	$row = $commentrid->fetchRow(MDB2_FETCHMODE_ASSOC);
	$rid = $row['last_insert_id()'];

	$sql = "select uid, mid, name, comment, initial, dateadded from reviews inner join user using (uid) where rid = ?";
	$resultset = prepared_query($dbh,$sql,$rid);
	$row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
	$name = $row['name'];
	$comment = $row['comment'];
	$time = $row['dateadded'];
	$initial = $row['initial'];
	$uid = $row['uid'];
	$mid = $row['mid'];

  echo '<div class="media"><a class="pull-left" href="user.php?uid=' . $uid . '">
    <img class="media-object" src="adele.jpg" alt="Media Object"></a>
    <div class="media-body">
    <h4 class="media-heading">' . $name . '</h4> at ' . $time . '<br>' . $comment . '
    <br><span class="comment-reply" style="color:blue"; >Reply</span>
    <br><div class="replyform"><form class="commentform" method="get" action="media.php">
    <input type="hidden" name="mid" value="' . $mid . '">
    <input type="hidden" name="rid" value="' . $rid . '">
    <textarea rows="1" cols="50" id="comment" name="comment"></textarea><br>
    <input type="hidden" name="addcomment">
    <input type="submit" name="submit">
    </form></div><br></div></div>';
}

if (isset($_REQUEST['addcomment'])){
  $comment = $_REQUEST['comment'];
  if (isset($_REQUEST['rid'])){
    $parentrid = $_REQUEST['rid'];
  }
  else{
    $parentrid = null;
  }
  addComment($dbh, $_REQUEST['mid'], $_REQUEST['uid'], $parentrid, $_REQUEST['comment']);
}

getLastComment($dbh);

?>