<?php

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");

printPageTop("Sign In to Helikon");
printSignInForm();
signIn();
logIn();

function printSignInForm() {
    $script = $_SERVER['PHP_SELF'];
    print <<<EOT
<form method="post" action="$script">
  <label for="name">Name</label> <input type="text" name="name" id="name"><br>
  <label for="email">Email</label> <input type="text" name="email" id="email"><br>
  <label for="username">Username</label> <input type="text" name="username" id="username"><br>
  <label for="password">Password</label> <input type="password" name="password" id="password"><br>
    <label for="password">Confirm Password</label> <input type="password" name="password1" id="password1"><br>
  <input type="submit" value="login">
</form>
EOT;
}






?>

</body>
</html>