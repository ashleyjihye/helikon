<?php

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");

$dbh = db_connect($athomas2_dsn);

$msg = "";

if(isset($_POST['username'])) {

    $username = $_POST['username'];
    if( loginCredentialsAreOkay($dbh,$username,$_POST['password']) ) {
        if(setCookie('userCookie',$username)) {
            $msg = "<p>Welcome, $username!\n" . 
                "Here is a link to your <a href='home.php'>homepage</a>\n";
        } else {
            $msg = "<p>Hmm. Something went wrong setting the cookie.";
        }
    } else {
        $msg = "<p>Sorry, that's incorrect.  Please try again\n";
    }
}

printPageTop("hello");

// Finally, we can print the result of the login attempt.
print $msg;

printLoginForm();

function loginCredentialsAreOkay($dbh,$username,$password) {
    $check = "SELECT count(*) AS n FROM user WHERE username=? AND password=?";
    $resultset = prepared_query($dbh, $check, array($username,$password));
    $row = $resultset->fetchRow();
    return( $row[0] == 1 );
}

function printLoginForm() {
    $script = $_SERVER['PHP_SELF'];
    print <<<EOT
<form method="post" action="$script">
  <label for="username">User</label> <input type="text" name="username" id="username"><br>
  <label for="password">Pass</label> <input type="text" name="password" id="password"><br>
  <input type="submit" value="login">
</form>
EOT;
}
function printPageTop($title) {
    print <<<EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <title>Blog 304: Read/Post</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name=author content="Scott D. Anderson">
    <link rel="stylesheet" type="text/css" href="../../webdb-style.css">
</head>
<body>
EOT;
  }




?>

</body>
</html>