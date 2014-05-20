<?php

/*
Ashley Thomas and Sasha Levy
  Helikon
  ratings.php
  5/19/14

This file is in charge of Ajax handling in order to change the user's rating of media
in the database and then return the results of both the user's rating and the overall 
average rating

 */

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");

$page = $_SERVER['PHP_SELF'];
$dbh = db_connect($athomas2_dsn);

//add user's rating to the database
function addRating($dbh,$uid,$mid,$rating){
  $sql = "select * from ratings where uid = ? and mid = ?";
  $values = array($uid,$mid,);
  $resultset = prepared_query($dbh,$sql,$values);
  $numRows = $resultset->numRows();
  if ($numRows == 0){
     $sql = "insert into ratings values (?,?,?)";
     $values = array($uid,$mid,$rating,);
     prepared_statement($dbh, $sql, $values);
   }
   else {
      $sql = "update ratings set rating = ? where uid = ? and mid = ?";
      $values = array($rating,$uid,$mid,);
      prepared_statement($dbh,$sql,$values);
   }
   $sql = "select mid, avg(rating) as avgrating from ratings where mid = ?";
   $resultset = prepared_query($dbh,$sql,$mid);
   $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
   $avgrating = $row['avgrating'];
   $sql = "update media set rating = ? where mid = ?";
   $resultset = prepared_statement($dbh,$sql,array($avgrating,$mid,));
   return $avgrating;
}

//handle the request
if (isset($_REQUEST['mid'])){
  $pagemid = $_REQUEST['mid'];

   if (isset($_REQUEST['rating'])){
      $rating = addRating($dbh,$_REQUEST['uid'],$pagemid,$_REQUEST['rating']);
      $numRatings = getNumRatings($dbh,$pagemid);
      $string = createActualRating($rating,$numRatings);
      echo $string;
    }

}

 ?>