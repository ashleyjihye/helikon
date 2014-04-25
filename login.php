<?php

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");

printPageTop("Login to Helikon");
printLoginForm();
logIn();

function printLoginForm() {
    $script = $_SERVER['PHP_SELF'];
    print <<<EOT
<form method="post" action="$script">
  <label for="username">User</label> <input type="text" name="username" id="username"><br>
  <label for="password">Pass</label> <input type="password" name="password" id="password"><br>
  <input type="submit" value="login">
</form>
EOT;
}






?>

</body>
</html>